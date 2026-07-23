<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class CloudinaryService
{
    public function isConfigured(): bool
    {
        return filled(config('undangan.cloudinary.cloud'))
            && filled(config('undangan.cloudinary.api_key'))
            && filled(config('undangan.cloudinary.api_secret'));
    }

    /**
     * @return array{url: string, public_id: string}
     */
    public function uploadImage(UploadedFile $file, string $folder = 'rayakanmomen/campaign'): array
    {
        if (! $this->isConfigured()) {
            throw new InvalidArgumentException('Cloudinary belum dikonfigurasi di .env');
        }

        $this->assertSafeImage($file);

        $cloud = (string) config('undangan.cloudinary.cloud');
        $apiKey = (string) config('undangan.cloudinary.api_key');
        $apiSecret = (string) config('undangan.cloudinary.api_secret');

        // PNG besar / aneh sering ditolak Cloudinary ("The image failed to upload").
        // Kompres ke JPEG dulu biar upload stabil (cover katalog cukup ~1600px).
        $prepared = $this->prepareJpegForUpload($file);
        $uploadPath = $prepared['path'];
        $uploadName = $prepared['name'];

        try {
            $timestamp = time();
            $params = [
                'folder' => $folder,
                'timestamp' => $timestamp,
            ];
            $signature = $this->signParams($params, $apiSecret);

            $contents = file_get_contents($uploadPath);
            if ($contents === false || $contents === '') {
                throw new InvalidArgumentException('Gagal membaca file gambar untuk upload.');
            }

            $response = Http::timeout(90)
                ->connectTimeout(15)
                ->asMultipart()
                ->attach('file', $contents, $uploadName)
                ->post("https://api.cloudinary.com/v1_1/{$cloud}/image/upload", [
                    'api_key' => $apiKey,
                    'timestamp' => (string) $timestamp,
                    'signature' => $signature,
                    'folder' => $folder,
                ]);
        } finally {
            if (! empty($prepared['temp']) && is_file($uploadPath)) {
                @unlink($uploadPath);
            }
        }

        if (! $response->successful()) {
            $message = (string) ($response->json('error.message') ?? '');
            if ($message === '') {
                $message = (string) ($response->header('X-Cld-Error') ?: '');
            }
            if ($message !== '') {
                throw new InvalidArgumentException('Upload Cloudinary gagal: '.$message);
            }

            throw new InvalidArgumentException('Upload Cloudinary gagal (HTTP '.$response->status().').');
        }

        $data = $response->json();
        $url = (string) ($data['secure_url'] ?? '');
        $publicId = (string) ($data['public_id'] ?? '');

        if ($url === '' || $publicId === '') {
            throw new InvalidArgumentException('Respons Cloudinary tidak valid.');
        }

        return [
            'url' => $url,
            'public_id' => $publicId,
        ];
    }

    public function deleteImage(?string $publicId): void
    {
        if (! $publicId || ! $this->isConfigured()) {
            return;
        }

        $cloud = (string) config('undangan.cloudinary.cloud');
        $apiKey = (string) config('undangan.cloudinary.api_key');
        $apiSecret = (string) config('undangan.cloudinary.api_secret');

        $timestamp = time();
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ];
        $signature = $this->signParams($params, $apiSecret);

        Http::timeout(30)->asForm()->post("https://api.cloudinary.com/v1_1/{$cloud}/image/destroy", [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => (string) $timestamp,
            'signature' => $signature,
        ]);
    }

    /**
     * Cloudinary signature: sorted key=value (tanpa URL-encode) + api_secret.
     */
    protected function signParams(array $params, string $apiSecret): string
    {
        ksort($params);
        $parts = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $parts[] = $key.'='.$value;
        }

        return sha1(implode('&', $parts).$apiSecret);
    }

    protected function assertSafeImage(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            $err = $file->getError();
            if (in_array($err, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                throw new InvalidArgumentException('File terlalu besar untuk server (naikkan upload_max_filesize / post_max_size di PHP).');
            }

            throw new InvalidArgumentException('Upload gambar gagal. File tidak valid.');
        }

        $original = $file->getClientOriginalName();
        if ($original === '' || str_contains($original, "\0")) {
            throw new InvalidArgumentException('Nama file tidak valid.');
        }

        $base = basename(str_replace(['\\', '/'], '', $original));
        $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));

        if ($ext === '') {
            throw new InvalidArgumentException('File harus punya ekstensi gambar (JPG, PNG, atau WEBP).');
        }

        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new InvalidArgumentException('Format gambar: JPG, PNG, atau WEBP.');
        }

        $mime = strtolower((string) ($file->getMimeType() ?: ''));
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        if ($mime !== '' && ! in_array($mime, $allowedMime, true)) {
            throw new InvalidArgumentException('Tipe file gambar tidak didukung.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran gambar maksimal 5MB.');
        }

        $info = @getimagesize($file->getRealPath());
        if ($info === false || empty($info[0]) || empty($info[1])) {
            throw new InvalidArgumentException('File bukan gambar yang bisa dibaca. Coba export ulang sebagai JPG/PNG.');
        }
    }

    /**
     * @return array{path: string, name: string, temp: bool}
     */
    protected function prepareJpegForUpload(UploadedFile $file): array
    {
        $source = $file->getRealPath();
        $info = @getimagesize($source);
        if ($info === false) {
            throw new InvalidArgumentException('File bukan gambar yang bisa dibaca.');
        }

        $width = (int) $info[0];
        $height = (int) $info[1];
        $type = (int) ($info[2] ?? 0);
        $size = (int) (@filesize($source) ?: 0);
        $maxSide = 1600;

        // JPEG kecil sudah OK → kirim langsung
        if ($type === IMAGETYPE_JPEG && $width <= $maxSide && $height <= $maxSide && $size > 0 && $size <= 900 * 1024) {
            return [
                'path' => $source,
                'name' => 'cover.jpg',
                'temp' => false,
            ];
        }

        if (! function_exists('imagecreatetruecolor')) {
            return [
                'path' => $source,
                'name' => 'cover.'.$this->guessExtension($file),
                'temp' => false,
            ];
        }

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };

        if ($src === false) {
            throw new InvalidArgumentException('Gagal membuka gambar. Coba simpan ulang sebagai JPG.');
        }

        $needsResize = $width > $maxSide || $height > $maxSide;
        if ($needsResize) {
            $ratio = min($maxSide / $width, $maxSide / $height);
            $newW = max(1, (int) round($width * $ratio));
            $newH = max(1, (int) round($height * $ratio));
            $scaled = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($scaled, 255, 255, 255);
            imagefill($scaled, 0, 0, $white);
            imagecopyresampled($scaled, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $scaled;
            $width = $newW;
            $height = $newH;
        } elseif ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            $canvas = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopy($canvas, $src, 0, 0, 0, 0, $width, $height);
            imagedestroy($src);
            $src = $canvas;
        }

        $temp = tempnam(sys_get_temp_dir(), 'cld');
        if ($temp === false) {
            imagedestroy($src);
            throw new InvalidArgumentException('Tidak bisa membuat file sementara.');
        }
        $dest = $temp.'.jpg';
        @unlink($temp);

        if (! @imagejpeg($src, $dest, 82)) {
            imagedestroy($src);
            throw new InvalidArgumentException('Gagal kompres gambar ke JPEG.');
        }
        imagedestroy($src);

        return [
            'path' => $dest,
            'name' => 'cover.jpg',
            'temp' => true,
        ];
    }

    protected function guessExtension(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? ($ext === 'jpeg' ? 'jpg' : $ext) : 'jpg';
    }
}
