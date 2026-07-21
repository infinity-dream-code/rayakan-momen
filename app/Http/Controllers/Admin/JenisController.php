<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class JenisController extends Controller
{
    public function __construct(
        protected CategoryRepository $categories,
        protected CloudinaryService $cloudinary
    ) {
    }

    public function index()
    {
        return view('admin.jenis.index', [
            'jenisList' => $this->categories->all(),
        ]);
    }

    public function update(Request $request)
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
            return back()->with('error', 'Tidak ada data jenis yang valid.');
        }

        try {
            $this->categories->updateMany($cleaned);
        } catch (Throwable $e) {
            Log::error('Gagal simpan jenis: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan. ('.$e->getMessage().')');
        }

        return back()->with('success', 'Jenis katalog disimpan ('.count($cleaned).').');
    }

    public function updateImage(Request $request, string $slug)
    {
        abort_if($this->categories->get($slug) === null, 404);

        $request->validate([
            'image' => 'nullable|file|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $current = $this->categories->getImage($slug);

        try {
            if ($request->boolean('remove_image')) {
                $this->cloudinary->deleteImage($current['cloudinary_id']);
                $this->categories->updateImage($slug, null, null);
            } elseif ($request->hasFile('image')) {
                if (! $this->cloudinary->isConfigured()) {
                    return back()->with('error', 'Cloudinary belum dikonfigurasi.');
                }
                $uploaded = $this->cloudinary->uploadImage($request->file('image'), 'rayakanmomen/jenis/'.$slug);
                if ($current['cloudinary_id']) {
                    $this->cloudinary->deleteImage($current['cloudinary_id']);
                }
                $this->categories->updateImage($slug, $uploaded['url'], $uploaded['public_id']);
            } else {
                return back()->with('error', 'Pilih gambar atau centang hapus.');
            }
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal upload gambar jenis '.$slug.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan gambar.');
        }

        $nama = $this->categories->get($slug)['nama'] ?? $slug;

        return back()->with('success', 'Gambar "'.$nama.'" disimpan.');
    }
}
