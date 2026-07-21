<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class SettingController extends Controller
{
    public function __construct(
        protected CatalogRepository $catalog,
        protected CategoryRepository $categories,
        protected CloudinaryService $cloudinary
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

        $request->validate([
            'image' => 'nullable|file|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $current = $this->catalog->getPreview($key);

        try {
            if ($request->boolean('remove_image')) {
                $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
                $this->catalog->updatePreview($key, null, null);
            } elseif ($request->hasFile('image')) {
                if (! $this->cloudinary->isConfigured()) {
                    return back()->with('error', 'Cloudinary belum dikonfigurasi.');
                }
                $uploaded = $this->cloudinary->uploadImage($request->file('image'), 'rayakanmomen/templates/'.$key);
                if ($current['preview_cloudinary_id']) {
                    $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
                }
                $this->catalog->updatePreview($key, $uploaded['url'], $uploaded['public_id']);
            } else {
                return back()->with('error', 'Pilih gambar atau centang hapus.');
            }
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal upload cover template '.$key.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan cover.');
        }

        $nama = $known[$key]['nama'] ?? $key;

        return back()->with('success', 'Cover "'.$nama.'" disimpan.');
    }
}
