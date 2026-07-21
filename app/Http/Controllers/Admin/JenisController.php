<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class JenisController extends Controller
{
    public function __construct(protected CategoryRepository $categories)
    {
    }

    public function index()
    {
        return view('admin.jenis.index', [
            'jenisList' => $this->categories->all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        try {
            $this->categories->create($request->input('nama'));
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal tambah jenis: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal menambah jenis.');
        }

        return back()->with('success', 'Jenis "'.$request->input('nama').'" ditambahkan.');
    }

    public function update(Request $request, string $slug)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'aktif' => 'nullable|boolean',
        ]);

        try {
            $this->categories->update(
                $slug,
                $request->input('nama'),
                $request->boolean('aktif')
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal ubah jenis '.$slug.': '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan perubahan.');
        }

        return back()->with('success', 'Jenis diperbarui.');
    }

    public function destroy(string $slug)
    {
        try {
            $this->categories->delete($slug);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Gagal hapus jenis '.$slug.': '.$e->getMessage());

            return back()->with('error', 'Gagal menghapus jenis.');
        }

        return back()->with('success', 'Jenis dihapus.');
    }
}
