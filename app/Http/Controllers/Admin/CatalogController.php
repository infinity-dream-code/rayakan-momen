<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogStorage;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(protected CatalogStorage $catalog)
    {
    }

    public function index()
    {
        $categories = config('templates.categories', []);
        $templates = $this->catalog->templates();
        $grouped = collect($templates)->groupBy('kategori');

        return view('admin.katalog.index', compact('categories', 'templates', 'grouped'));
    }

    public function update(Request $request)
    {
        $items = $request->input('items', []);
        if (! is_array($items)) {
            $items = [];
        }

        $cleaned = [];
        foreach ($items as $key => $row) {
            if (! is_string($key) || ! is_array($row)) {
                continue;
            }
            if (! array_key_exists($key, config('templates.templates', []))) {
                continue;
            }
            $cleaned[$key] = [
                'harga' => $row['harga'] ?? 0,
                'diskon_persen' => $row['diskon_persen'] ?? 0,
                'aktif_katalog' => ! empty($row['aktif_katalog']),
            ];
        }

        $this->catalog->updateMany($cleaned);

        return redirect()
            ->route('admin.katalog.index')
            ->with('success', 'Harga & diskon katalog berhasil disimpan.');
    }
}
