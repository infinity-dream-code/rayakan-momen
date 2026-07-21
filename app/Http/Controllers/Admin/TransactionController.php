<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(): StreamedResponse
    {
        $items = $this->transactions->all();
        $byTemplate = $this->transactions->summaryByTemplate();
        $stats = $this->transactions->stats();
        $filename = 'transaksi-rayakanmomen-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($items, $byTemplate, $stats) {
            $out = fopen('php://output', 'w');
            // BOM supaya Excel baca UTF-8 benar
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['RINGKASAN']);
            fputcsv($out, ['Total Terjual', $stats['total_transaksi']]);
            fputcsv($out, ['Total Penghasilan', $stats['total_penghasilan']]);
            fputcsv($out, ['Terjual Bulan Ini', $stats['bulan_ini_transaksi']]);
            fputcsv($out, ['Penghasilan Bulan Ini', $stats['bulan_ini_penghasilan']]);
            fputcsv($out, []);

            fputcsv($out, ['PER TEMPLATE']);
            fputcsv($out, ['Template', 'Terjual', 'Penghasilan']);
            foreach ($byTemplate as $row) {
                fputcsv($out, [
                    $row['template_nama'],
                    $row['terjual'],
                    $row['penghasilan'],
                ]);
            }
            fputcsv($out, []);

            fputcsv($out, ['RIWAYAT TRANSAKSI']);
            fputcsv($out, [
                'Tanggal',
                'Pelanggan',
                'Slug',
                'Template',
                'Kategori',
                'Harga Asli',
                'Diskon (%)',
                'Diterima (Harga Final)',
            ]);

            foreach ($items as $item) {
                fputcsv($out, [
                    Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    $item['pelanggan'] ?? '',
                    $item['slug'] ?? '',
                    $item['template_nama'] ?: ($item['template_key'] ?? ''),
                    $item['kategori'] ?? '',
                    (int) ($item['harga_asli'] ?? 0),
                    (float) ($item['diskon_persen'] ?? 0),
                    (int) ($item['harga_final'] ?? 0),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
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
