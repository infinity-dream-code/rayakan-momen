<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileUploadService
{
    /** Allowed final extensions only. */
    protected array $allowedExt = ['jpg', 'jpeg', 'png'];

    /** Allowed MIME types (real content). */
    protected array $allowedMime = [
        'image/jpeg',
        'image/png',
        'image/jpg',
    ];

    /** Any of these appearing before the final ext = double-extension attack. */
    protected array $blockedMiddleExt = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8',
        'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash',
        'exe', 'bat', 'cmd', 'com', 'dll', 'msi',
        'js', 'mjs', 'html', 'htm', 'shtml', 'xhtml', 'svg', 'svgz',
        'htaccess', 'ini', 'env', 'sql', 'jar', 'war',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'tif', 'tiff',
    ];

    /** Target max file size after compress (bytes). */
    protected int $maxBytes;

    /** Max longest side in pixels before re-encode. */
    protected int $maxDimension;

    public function __construct()
    {
        $this->maxBytes = max(100 * 1024, (int) config('undangan.upload_max_kb', 500) * 1024);
        $this->maxDimension = max(640, (int) config('undangan.upload_max_dimension', 1920));
    }

    public function storeUpload(?UploadedFile $file, string $folder = 'covers'): ?string
    {
        if (! $file) {
            return null;
        }

        $this->assertSafeImage($file);

        $dir = public_path('uploads/'.$folder);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        // Always save as .jpg after re-encode (safe + compressible)
        $name = Str::uuid().'.jpg';
        $dest = $dir.DIRECTORY_SEPARATOR.$name;

        $this->compressToJpeg($file->getRealPath(), $dest);

        if (! is_file($dest) || filesize($dest) < 32) {
            @unlink($dest);
            throw new InvalidArgumentException('Gagal memproses gambar. Coba file JPG/PNG lain.');
        }

        return 'uploads/'.$folder.'/'.$name;
    }

    public function storeMultipleUploads(array $files, string $folder = 'galeri'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storeUpload($file, $folder);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    public function deletePublicPath(?string $relative): void
    {
        if (! $relative) {
            return;
        }
        $full = public_path($relative);
        if (File::exists($full) && File::isFile($full)) {
            File::delete($full);
        }
    }

    /**
     * Validate extension, MIME, and block double-extension filenames.
     */
    public function assertSafeImage(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Upload gagal. File tidak valid.');
        }

        $original = $file->getClientOriginalName();
        if ($original === '' || str_contains($original, "\0")) {
            throw new InvalidArgumentException('Nama file tidak valid.');
        }

        // Normalize: only basename, no path tricks
        $base = basename(str_replace(['\\', '/'], '', $original));
        $lower = strtolower($base);

        if ($this->hasDoubleExtension($lower)) {
            throw new InvalidArgumentException('Nama file tidak boleh double ekstensi (contoh: foto.php.jpg).');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($lower, PATHINFO_EXTENSION));
        if (! in_array($ext, $this->allowedExt, true)) {
            throw new InvalidArgumentException('Hanya file JPG, JPEG, atau PNG yang diperbolehkan.');
        }

        // Prefer guessExtension / mime from content, not client claim
        $mime = $file->getMimeType() ?: '';
        $guessExt = strtolower((string) $file->guessExtension());

        if (! in_array($mime, $this->allowedMime, true)) {
            throw new InvalidArgumentException('Tipe file ditolak. Upload gambar JPG/PNG asli.');
        }

        if ($guessExt && ! in_array($guessExt, ['jpg', 'jpeg', 'png'], true)) {
            throw new InvalidArgumentException('Isi file bukan gambar JPG/PNG yang sah.');
        }

        // Must be a real image (getimagesize reads binary header)
        $realPath = $file->getRealPath();
        $info = @getimagesize($realPath);
        if ($info === false || empty($info[0]) || empty($info[1])) {
            throw new InvalidArgumentException('File bukan gambar yang bisa dibaca.');
        }

        $imageType = $info[2] ?? 0;
        if (! in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            throw new InvalidArgumentException('Format gambar harus JPEG atau PNG.');
        }

        // Cap raw upload before compress (10MB) to avoid memory bombs
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran file terlalu besar (maks 10MB sebelum kompresi).');
        }
    }

    protected function hasDoubleExtension(string $filenameLower): bool
    {
        // foto.php.jpg / foto.jpg.php / archive.tar.gz.jpg
        $parts = explode('.', $filenameLower);
        if (count($parts) < 2) {
            return true; // no extension
        }

        // Check every segment except the last: if it looks like an extension → double ext
        $last = array_pop($parts); // final ext
        if (! in_array($last, $this->allowedExt, true)) {
            return true;
        }

        foreach ($parts as $segment) {
            if ($segment === '') {
                return true;
            }
            if (in_array($segment, $this->blockedMiddleExt, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Re-encode & compress image to JPEG under ~500KB.
     */
    protected function compressToJpeg(string $sourcePath, string $destPath): void
    {
        if (! function_exists('imagecreatefromstring')) {
            // Fallback: copy without compress if GD missing (still validated)
            copy($sourcePath, $destPath);

            return;
        }

        $binary = file_get_contents($sourcePath);
        if ($binary === false) {
            throw new InvalidArgumentException('Tidak bisa membaca file gambar.');
        }

        $src = @imagecreatefromstring($binary);
        if ($src === false) {
            throw new InvalidArgumentException('Gagal membuka gambar untuk kompresi.');
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // Downscale if too large
        $max = $this->maxDimension;
        if ($width > $max || $height > $max) {
            $ratio = min($max / $width, $max / $height);
            $newW = max(1, (int) round($width * $ratio));
            $newH = max(1, (int) round($height * $ratio));
            $resized = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $resized;
            $width = $newW;
            $height = $newH;
        } else {
            // Flatten PNG transparency onto white
            $canvas = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopy($canvas, $src, 0, 0, 0, 0, $width, $height);
            imagedestroy($src);
            $src = $canvas;
        }

        // Binary search quality to fit under maxBytes
        $quality = 85;
        $minQ = 40;
        $maxQ = 90;
        $best = null;

        for ($i = 0; $i < 8; $i++) {
            ob_start();
            imagejpeg($src, null, $quality);
            $data = ob_get_clean();
            $size = strlen($data);

            if ($size <= $this->maxBytes) {
                $best = $data;
                $minQ = $quality + 1;
                $quality = (int) ceil(($quality + $maxQ) / 2);
            } else {
                $maxQ = $quality - 1;
                $quality = (int) floor(($minQ + $quality) / 2);
            }

            if ($minQ > $maxQ) {
                break;
            }
        }

        // If still too big at low quality, shrink dimensions further
        if ($best === null || strlen($best) > $this->maxBytes) {
            $scale = 0.75;
            while ($scale >= 0.35) {
                $nw = max(1, (int) round($width * $scale));
                $nh = max(1, (int) round($height * $scale));
                $tmp = imagecreatetruecolor($nw, $nh);
                $white = imagecolorallocate($tmp, 255, 255, 255);
                imagefill($tmp, 0, 0, $white);
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $nw, $nh, $width, $height);

                ob_start();
                imagejpeg($tmp, null, 72);
                $data = ob_get_clean();
                imagedestroy($tmp);

                if (strlen($data) <= $this->maxBytes) {
                    $best = $data;
                    break;
                }
                $scale -= 0.1;
            }
        }

        imagedestroy($src);

        if ($best === null) {
            throw new InvalidArgumentException('Gambar terlalu besar untuk dikompres ke 500KB.');
        }

        if (file_put_contents($destPath, $best) === false) {
            throw new InvalidArgumentException('Gagal menyimpan gambar.');
        }
    }
}
