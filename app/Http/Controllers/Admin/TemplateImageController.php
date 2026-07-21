<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class TemplateImageController extends Controller
{
    public function __construct(
        protected CatalogRepository $catalog,
        protected CloudinaryService $cloudinary
    ) {
    }

    public function index()
    {
        $categories = config('templates.categories', []);
        $templates = $this->catalog->templates();
        $grouped = collect($templates)->groupBy('kategori', preserveKeys: true);

        return view('admin.template-gambar.index', compact('categories', 'templates', 'grouped'));
    }

    public function update(Request $request, string $key)
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
            ->route('admin.template-gambar.index')
            ->with('success', 'Gambar template "'.$nama.'" berhasil disimpan.');
    }
}
