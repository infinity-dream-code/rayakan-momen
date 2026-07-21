<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\TransactionRepository;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionRepository $transactions,
        protected CatalogRepository $catalog
    ) {
    }

    public function index()
    {
        $stats = $this->transactions->stats();
        $byTemplate = $this->transactions->summaryByTemplate();
        $items = $this->transactions->recent(150);

        return view('admin.transaksi.index', [
            'stats' => $stats,
            'byTemplate' => $byTemplate,
            'items' => $items,
            'catalog' => $this->catalog,
        ]);
    }

    public function destroy(int $id)
    {
        $this->transactions->delete($id);

        return redirect()
            ->route('admin.transaksi.index')
            ->with('success', 'Transaksi dihapus dari laporan.');
    }
}
