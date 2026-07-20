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

        $timestamp = time();
        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];
        ksort($params);
        $signature = sha1(http_build_query($params).$apiSecret);

        $response = Http::timeout(60)
            ->asMultipart()
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloud}/image/upload", [
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'folder' => $folder,
            ]);

        if (! $response->successful()) {
            throw new InvalidArgumentException('Upload Cloudinary gagal. Coba lagi atau periksa kredensial.');
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
        ksort($params);
        $signature = sha1(http_build_query($params).$apiSecret);

        Http::timeout(30)->asForm()->post("https://api.cloudinary.com/v1_1/{$cloud}/image/destroy", [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ]);
    }

    protected function assertSafeImage(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Upload gambar gagal. File tidak valid.');
        }

        $original = $file->getClientOriginalName();
        if ($original === '' || str_contains($original, "\0")) {
            throw new InvalidArgumentException('Nama file tidak valid.');
        }

        $base = basename(str_replace(['\\', '/'], '', $original));
        $lower = strtolower($base);
        $parts = explode('.', $lower);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Hanya file gambar tunggal yang diperbolehkan.');
        }

        $ext = $parts[1];
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
    }
}
