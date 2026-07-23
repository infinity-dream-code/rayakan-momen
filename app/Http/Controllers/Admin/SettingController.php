<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use App\Services\CloudinaryService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class SettingController extends Controller
{
    public function __construct(
        protected CatalogRepository $catalog,
        protected CategoryRepository $categories,
        protected CloudinaryService $cloudinary,
        protected FileUploadService $uploads
    ) {
    }

    public function index()
    {
        return view('admin.setting.index', [
            'jenisList' => $this->categories->all(),
            'templates' => $this->catalog->templates(),
        ]);
    }

    public function update(Request $request)
    {
        $items = $request->input('items', []);
        if (! is_array($items)) {
            $items = [];
        }

        $known = config('templates.templates', []);
        $validJenis = array_keys($this->categories->all());
        $cleaned = [];

        foreach ($items as $key => $row) {
            if (! is_string($key) || ! is_array($row)) {
                continue;
            }
            if (! array_key_exists($key, $known)) {
                continue;
            }
            $kategori = (string) ($row['kategori'] ?? '');
            if ($kategori !== '' && ! in_array($kategori, $validJenis, true)) {
                $kategori = $known[$key]['kategori'] ?? 'wedding';
            }
            $cleaned[$key] = [
                'kategori' => $kategori ?: ($known[$key]['kategori'] ?? 'wedding'),
                'nama' => $row['nama'] ?? '',
                'harga' => $row['harga'] ?? 0,
                'diskon_persen' => $row['diskon_persen'] ?? 0,
                'aktif_katalog' => ! empty($row['aktif_katalog']),
                'tampil_home' => ! empty($row['tampil_home']),
            ];
        }

        if ($cleaned === []) {
            return back()->with('error', 'Tidak ada data template yang valid.');
        }

        try {
            $this->catalog->updateMany($cleaned);
        } catch (Throwable $e) {
            Log::error('Gagal simpan template: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan. ('.$e->getMessage().')');
        }

        return back()->with('success', 'Template disimpan ('.count($cleaned).').');
    }

    public function updateImage(Request $request, string $key)
    {
        $known = config('templates.templates', []);
        abort_if(! array_key_exists($key, $known), 404);

        // Diagnosa dulu: pesan "The image failed to upload" = gagal di PHP, belum ke Cloudinary
        if ($request->hasFile('image') && ! $request->file('image')->isValid()) {
            return back()->with('error', $this->explainPhpUploadFailure($request->file('image')));
        }

        // post_max_size terlampaui → file tidak sampai sama sekali
        if (! $request->hasFile('image') && ! $request->boolean('remove_image') && $this->postTooLarge()) {
            return back()->with('error', $this->explainPostTooLarge());
        }

        try {
            $request->validate([
                'image' => 'nullable|file|max:5120',
                'remove_image' => 'nullable|boolean',
            ], [
                'image.uploaded' => $this->explainPhpUploadFailure($request->file('image')),
                'image.max' => 'Ukuran gambar maksimal 5MB (5120 KB).',
                'image.file' => 'File gambar tidak valid.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Pastikan pesan tampil juga di session error (bukan cuma bullet validation)
            $msg = collect($e->errors())->flatten()->first() ?: 'Validasi upload gagal.';

            return back()->withInput()->withErrors($e->errors())->with('error', $msg);
        }

        $current = $this->catalog->getPreview($key);
        $nama = $known[$key]['nama'] ?? $key;

        try {
            // Ada file baru → selalu upload (abaikan centang Hapus supaya tidak bentrok)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $uploaded = $this->storeCoverImage($file, $key, $current);

                $this->catalog->updatePreview($key, $uploaded['url'], $uploaded['public_id']);

                $note = $uploaded['via'] === 'local'
                    ? ' (disimpan lokal)'
                    : '';

                return back()->with('success', 'Cover "'.$nama.'" disimpan.'.$note);
            }

            if ($request->boolean('remove_image')) {
                $this->deleteCurrentCover($current);
                $this->catalog->updatePreview($key, null, null);

                return back()->with('success', 'Cover "'.$nama.'" dihapus.');
            }

            return back()->with('error', 'Pilih gambar atau centang hapus.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal upload cover template '.$key.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan cover.');
        }
    }

    protected function explainPhpUploadFailure(?\Illuminate\Http\UploadedFile $file): string
    {
        $code = $file ? $file->getError() : UPLOAD_ERR_NO_FILE;
        $map = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar untuk limit upload server.',
            UPLOAD_ERR_FORM_SIZE => 'File melebihi batas form.',
            UPLOAD_ERR_PARTIAL => 'Upload terputus. Coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara server tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke server.',
            UPLOAD_ERR_EXTENSION => 'Upload diblokir ekstensi PHP.',
        ];

        return $map[$code] ?? 'Upload gambar gagal.';
    }

    protected function postTooLarge(): bool
    {
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLength <= 0) {
            return false;
        }
        $postMax = $this->iniBytes((string) ini_get('post_max_size'));
        if ($postMax <= 0) {
            return false;
        }

        return empty($_POST) && empty($_FILES) && $contentLength > $postMax;
    }

    protected function explainPostTooLarge(): string
    {
        return 'File terlalu besar untuk limit server. Coba gambar lebih kecil.';
    }

    protected function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }
        $unit = strtolower(substr($value, -1));
        $num = (float) $value;
        return (int) match ($unit) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => (float) $value,
        };
    }

    /**
     * @param  array{preview_image_url: ?string, preview_cloudinary_id: ?string}  $current
     * @return array{url: string, public_id: ?string, via: string}
     */
    protected function storeCoverImage($file, string $key, array $current): array
    {
        $cloudError = null;

        if ($this->cloudinary->isConfigured()) {
            try {
                $uploaded = $this->cloudinary->uploadImage($file, 'rayakanmomen/templates/'.$key);
                if ($current['preview_cloudinary_id']) {
                    $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
                }
                $this->deleteLocalPreviewFile($current['preview_image_url'] ?? null);

                return [
                    'url' => $uploaded['url'],
                    'public_id' => $uploaded['public_id'],
                    'via' => 'cloudinary',
                ];
            } catch (InvalidArgumentException $e) {
                $cloudError = $e->getMessage();
                Log::warning('Cloudinary cover gagal, coba simpan lokal', [
                    'key' => $key,
                    'error' => $cloudError,
                    'file' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);
            }
        }

        // Fallback: simpan ke public/uploads supaya cover tetap bisa dipakai
        $relative = $this->uploads->storeUpload($file, 'covers/templates/'.$key, 'cover', 1600);
        if (! $relative) {
            throw new InvalidArgumentException(
                $cloudError ?: 'Gagal menyimpan cover. Cloudinary/local gagal.'
            );
        }

        if ($current['preview_cloudinary_id']) {
            $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
        }
        $this->deleteLocalPreviewFile($current['preview_image_url'] ?? null);

        return [
            'url' => asset($relative),
            'public_id' => null,
            'via' => 'local',
        ];
    }

    /**
     * @param  array{preview_image_url: ?string, preview_cloudinary_id: ?string}  $current
     */
    protected function deleteCurrentCover(array $current): void
    {
        if ($current['preview_cloudinary_id']) {
            $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
        }
        $this->deleteLocalPreviewFile($current['preview_image_url'] ?? null);
    }

    protected function deleteLocalPreviewFile(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $path = ltrim((string) $path, '/');
        if (! str_starts_with($path, 'uploads/')) {
            return;
        }

        $this->uploads->deletePublicPath($path);
    }
}
