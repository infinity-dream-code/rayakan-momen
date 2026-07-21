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
        $jenisList = $this->categories->all();
        $templates = $this->catalog->templates();
        $grouped = collect($templates)->groupBy('kategori', preserveKeys: true);

        return view('admin.setting.index', compact('jenisList', 'templates', 'grouped'));
    }

    public function updateCategories(Request $request)
    {
        $items = $request->input('jenis', []);
        if (! is_array($items)) {
            $items = [];
        }

        $known = $this->categories->all();
        $cleaned = [];

        foreach ($items as $slug => $row) {
            if (! is_string($slug) || ! is_array($row)) {
                continue;
            }
            if (! array_key_exists($slug, $known)) {
                continue;
            }
            $nama = trim((string) ($row['nama'] ?? ''));
            if ($nama === '') {
                continue;
            }
            $cleaned[$slug] = [
                'nama' => $nama,
                'tagline' => $row['tagline'] ?? '',
                'aktif' => ! empty($row['aktif']),
            ];
        }

        if ($cleaned === []) {
            return redirect()
                ->route('admin.setting.index')
                ->with('error', 'Tidak ada data jenis yang valid untuk disimpan.');
        }

        try {
            $this->categories->updateMany($cleaned);
        } catch (Throwable $e) {
            Log::error('Gagal simpan jenis katalog: '.$e->getMessage());

            return redirect()
                ->route('admin.setting.index')
                ->with('error', 'Gagal menyimpan jenis katalog. ('.$e->getMessage().')');
        }

        return redirect()
            ->route('admin.setting.index')
            ->with('success', 'Jenis katalog berhasil disimpan ('.count($cleaned).' jenis).');
    }

    public function updateCategoryImage(Request $request, string $slug)
    {
        abort_if($this->categories->get($slug) === null, 404);

        $request->validate([
            'image' => 'nullable|file|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $current = $this->categories->getImage($slug);
        $imageUrl = $current['image_url'];
        $publicId = $current['cloudinary_id'];

        try {
            if ($request->boolean('remove_image')) {
                $this->cloudinary->deleteImage($publicId);
                $imageUrl = null;
                $publicId = null;
            } elseif ($request->hasFile('image')) {
                if (! $this->cloudinary->isConfigured()) {
                    return back()->with('error', 'Cloudinary belum dikonfigurasi. Isi CLOUDINARY_* di file .env');
                }

                $folder = 'rayakanmomen/jenis/'.$slug;
                $uploaded = $this->cloudinary->uploadImage($request->file('image'), $folder);
                if ($current['cloudinary_id']) {
                    $this->cloudinary->deleteImage($current['cloudinary_id']);
                }
                $imageUrl = $uploaded['url'];
                $publicId = $uploaded['public_id'];
            } else {
                return back()->with('error', 'Pilih gambar atau centang hapus.');
            }

            $this->categories->updateImage($slug, $imageUrl, $publicId);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal simpan gambar jenis '.$slug.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan gambar jenis. Coba lagi.');
        }

        $nama = $this->categories->get($slug)['nama'] ?? $slug;

        return redirect()
            ->route('admin.setting.index')
            ->with('success', 'Gambar jenis "'.$nama.'" berhasil disimpan.');
    }

    public function update(Request $request)
    {
        $items = $request->input('items', []);
        if (! is_array($items)) {
            $items = [];
        }

        $known = config('templates.templates', []);
        $cleaned = [];

        foreach ($items as $key => $row) {
            if (! is_string($key) || ! is_array($row)) {
                continue;
            }
            if (! array_key_exists($key, $known)) {
                continue;
            }
            $cleaned[$key] = [
                'harga' => $row['harga'] ?? 0,
                'diskon_persen' => $row['diskon_persen'] ?? 0,
                'aktif_katalog' => ! empty($row['aktif_katalog']),
            ];
        }

        if ($cleaned === []) {
            return redirect()
                ->route('admin.setting.index')
                ->with('error', 'Tidak ada data template yang valid untuk disimpan.');
        }

        try {
            $this->catalog->updateMany($cleaned);
        } catch (Throwable $e) {
            Log::error('Gagal simpan setting template: '.$e->getMessage());

            return redirect()
                ->route('admin.setting.index')
                ->with('error', 'Gagal menyimpan ke database. ('.$e->getMessage().')');
        }

        return redirect()
            ->route('admin.setting.index')
            ->with('success', 'Template berhasil disimpan ('.count($cleaned).' produk).');
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
        $imageUrl = $current['preview_image_url'];
        $publicId = $current['preview_cloudinary_id'];

        try {
            if ($request->boolean('remove_image')) {
                $this->cloudinary->deleteImage($publicId);
                $imageUrl = null;
                $publicId = null;
            } elseif ($request->hasFile('image')) {
                if (! $this->cloudinary->isConfigured()) {
                    return back()->with('error', 'Cloudinary belum dikonfigurasi. Isi CLOUDINARY_* di file .env');
                }

                $folder = 'rayakanmomen/templates/'.$key;
                $uploaded = $this->cloudinary->uploadImage($request->file('image'), $folder);
                if ($current['preview_cloudinary_id']) {
                    $this->cloudinary->deleteImage($current['preview_cloudinary_id']);
                }
                $imageUrl = $uploaded['url'];
                $publicId = $uploaded['public_id'];
            } else {
                return back()->with('error', 'Pilih gambar atau centang hapus.');
            }

            $this->catalog->updatePreview($key, $imageUrl, $publicId);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal simpan gambar template '.$key.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan gambar. Coba lagi.');
        }

        $nama = $known[$key]['nama'] ?? $key;

        return redirect()
            ->route('admin.setting.index')
            ->with('success', 'Gambar template "'.$nama.'" berhasil disimpan.');
    }
}
