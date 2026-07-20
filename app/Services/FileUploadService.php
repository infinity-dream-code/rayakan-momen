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
        // Shared hosting: default lebih kecil biar resize cepat
        $this->maxDimension = max(640, (int) config('undangan.upload_max_dimension', 1280));
    }

    /**
     * Simpan gambar ke public/uploads/{folder}/.
     * Contoh folder: mempelai/niko-naswa/foto-mempelai
     * $basename opsional → foto-wanita-xxxx.jpg (versi unik biar cache browser tidak nempel).
     */
    public function storeUpload(?UploadedFile $file, string $folder = 'covers', ?string $basename = null): ?string
    {
        if (! $file) {
            return null;
        }

        $info = $this->assertSafeImage($file);

        $folder = $this->sanitizeFolder($folder);
        $dir = public_path('uploads/'.$folder);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if ($basename !== null && $basename !== '') {
            $safe = Str::slug(pathinfo($basename, PATHINFO_FILENAME), '-');
            $safe = $safe !== '' ? $safe : 'foto';
            $name = $safe.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4)).'.jpg';
        } else {
            $name = Str::uuid().'.jpg';
        }

        $dest = $dir.DIRECTORY_SEPARATOR.$name;
        $source = $file->getRealPath();

        $this->saveAsJpegFast($source, $dest, $info);

        if (! is_file($dest) || filesize($dest) < 32) {
            @unlink($dest);
            throw new InvalidArgumentException('Gagal memproses gambar. Coba file JPG/PNG lain.');
        }

        return 'uploads/'.$folder.'/'.$name;
    }

    public function storeMultipleUploads(array $files, string $folder = 'galeri'): array
    {
        $paths = [];
        $folder = $this->sanitizeFolder($folder);

        foreach ($files as $i => $file) {
            if ($file instanceof UploadedFile) {
                $label = 'galeri-'.now()->format('Ymd-His').'-'.($i + 1);
                $path = $this->storeUpload($file, $folder, $label);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /**
     * Base folder undangan: mempelai/{slug}
     */
    public function invitationBase(string $slug): string
    {
        $slug = Str::slug(Str::lower($slug), '-');

        return $slug !== '' ? 'mempelai/'.$slug : 'mempelai/undangan';
    }

    /**
     * Hanya izinkan a-z, 0-9, strip, underscore, slash — cegah path traversal.
     */
    protected function sanitizeFolder(string $folder): string
    {
        $folder = str_replace('\\', '/', $folder);
        $folder = trim($folder, '/');
        $parts = array_values(array_filter(explode('/', $folder), fn ($p) => $p !== '' && $p !== '.' && $p !== '..'));

        $clean = [];
        foreach ($parts as $part) {
            $part = Str::slug(Str::lower($part), '-');
            if ($part !== '') {
                $clean[] = $part;
            }
        }

        if ($clean === []) {
            throw new InvalidArgumentException('Folder upload tidak valid.');
        }

        return implode('/', $clean);
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
     *
     * @return array{0:int,1:int,2:int} getimagesize result (w, h, type)
     */
    public function assertSafeImage(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Upload gagal. File tidak valid.');
        }

        $original = $file->getClientOriginalName();
        if ($original === '' || str_contains($original, "\0")) {
            throw new InvalidArgumentException('Nama file tidak valid.');
        }

        $base = basename(str_replace(['\\', '/'], '', $original));
        $lower = strtolower($base);

        if ($this->hasDoubleExtension($lower)) {
            throw new InvalidArgumentException('Nama file tidak boleh double ekstensi (contoh: foto.php.jpg).');
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($lower, PATHINFO_EXTENSION));
        if (! in_array($ext, $this->allowedExt, true)) {
            throw new InvalidArgumentException('Hanya file JPG, JPEG, atau PNG yang diperbolehkan.');
        }

        $mime = $file->getMimeType() ?: '';
        $guessExt = strtolower((string) $file->guessExtension());

        if (! in_array($mime, $this->allowedMime, true)) {
            throw new InvalidArgumentException('Tipe file ditolak. Upload gambar JPG/PNG asli.');
        }

        if ($guessExt && ! in_array($guessExt, ['jpg', 'jpeg', 'png'], true)) {
            throw new InvalidArgumentException('Isi file bukan gambar JPG/PNG yang sah.');
        }

        $realPath = $file->getRealPath();
        $info = @getimagesize($realPath);
        if ($info === false || empty($info[0]) || empty($info[1])) {
            throw new InvalidArgumentException('File bukan gambar yang bisa dibaca.');
        }

        $imageType = $info[2] ?? 0;
        if (! in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            throw new InvalidArgumentException('Format gambar harus JPEG atau PNG.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran file terlalu besar (maks 10MB sebelum kompresi).');
        }

        return $info;
    }

    protected function hasDoubleExtension(string $filenameLower): bool
    {
        $parts = explode('.', $filenameLower);
        if (count($parts) < 2) {
            return true;
        }

        $last = array_pop($parts);
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
     * Simpan sebagai JPEG — prioritas kecepatan di shared hosting.
     *
     * @param  array{0:int,1:int,2:int}  $info
     */
    protected function saveAsJpegFast(string $sourcePath, string $destPath, array $info): void
    {
        if (! function_exists('imagecreatefromjpeg')) {
            if (! @copy($sourcePath, $destPath)) {
                throw new InvalidArgumentException('GD tidak tersedia dan copy gagal.');
            }

            return;
        }

        $width = (int) $info[0];
        $height = (int) $info[1];
        $type = (int) $info[2];
        $size = (int) (@filesize($sourcePath) ?: 0);
        $max = min($this->maxDimension, 1200);
        $needsResize = $width > $max || $height > $max;

        // JPEG sudah kecil + resolusi OK → copy langsung (paling cepat)
        if ($type === IMAGETYPE_JPEG && ! $needsResize && $size > 0 && $size <= $this->maxBytes) {
            if (! @copy($sourcePath, $destPath)) {
                throw new InvalidArgumentException('Gagal menyimpan gambar.');
            }

            return;
        }

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            default => false,
        };

        if ($src === false) {
            throw new InvalidArgumentException('Gagal membuka gambar untuk kompresi.');
        }

        if ($needsResize) {
            $ratio = min($max / $width, $max / $height);
            $newW = max(1, (int) round($width * $ratio));
            $newH = max(1, (int) round($height * $ratio));

            if (function_exists('imagescale')) {
                $scaled = imagescale($src, $newW, $newH, IMG_BILINEAR_FIXED);
                imagedestroy($src);
                if ($scaled === false) {
                    throw new InvalidArgumentException('Gagal resize gambar.');
                }
                $src = $scaled;
            } else {
                $resized = imagecreatetruecolor($newW, $newH);
                $white = imagecolorallocate($resized, 255, 255, 255);
                imagefill($resized, 0, 0, $white);
                imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagedestroy($src);
                $src = $resized;
            }
            $width = $newW;
            $height = $newH;
        } elseif ($type === IMAGETYPE_PNG) {
            // PNG → JPEG: flatten ke putih (hindari transparansi hitam)
            $canvas = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopy($canvas, $src, 0, 0, 0, 0, $width, $height);
            imagedestroy($src);
            $src = $canvas;
        }

        // Tulis langsung ke file (hindari buffer memori ob_*)
        $quality = 70;
        if (! @imagejpeg($src, $destPath, $quality)) {
            imagedestroy($src);
            throw new InvalidArgumentException('Gagal kompres gambar.');
        }

        // Kalau masih kebesaran, satu pass kualitas lebih rendah
        clearstatcache(true, $destPath);
        if (is_file($destPath) && filesize($destPath) > $this->maxBytes) {
            if (! @imagejpeg($src, $destPath, 52)) {
                imagedestroy($src);
                throw new InvalidArgumentException('Gagal kompres gambar.');
            }
        }

        imagedestroy($src);
    }
}
