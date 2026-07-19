<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogController extends Controller
{
    public function __construct(protected CatalogRepository $catalog)
    {
    }

    public function index()
    {
        $categories = config('templates.categories', []);
        $templates = $this->catalog->templates();
        // preserveKeys: jangan hilangkan key template (elegan, classic, …)
        $grouped = collect($templates)->groupBy('kategori', preserveKeys: true);

        return view('admin.katalog.index', compact('categories', 'templates', 'grouped'));
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
                ->route('admin.katalog.index')
                ->with('error', 'Tidak ada data harga yang valid untuk disimpan. Coba refresh halaman lalu simpan lagi.');
        }

        try {
            $this->catalog->updateMany($cleaned);
        } catch (Throwable $e) {
            Log::error('Gagal simpan katalog: '.$e->getMessage());

            return redirect()
                ->route('admin.katalog.index')
                ->with('error', 'Gagal menyimpan ke database. Pastikan tabel catalog_templates sudah di-migrate. ('.$e->getMessage().')');
        }

        return redirect()
            ->route('admin.katalog.index')
            ->with('success', 'Harga & diskon katalog berhasil disimpan ('.count($cleaned).' produk).');
    }
}
