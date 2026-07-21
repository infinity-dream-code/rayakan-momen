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

        $kategoriLabel = [
            'wedding' => 'Pernikahan',
            'ultah_anak' => 'Ulang Tahun Anak',
            'couple' => 'Untuk Pasangan',
        ];

        $rupiah = static fn (int $n): string => 'Rp '.number_format($n, 0, ',', '.');

        return response()->streamDownload(function () use ($items, $byTemplate, $stats, $kategoriLabel, $rupiah) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 + sep=; supaya Excel Indonesia pecah kolom dengan benar
            fwrite($out, "\xEF\xBB\xBF");
            fwrite($out, "sep=;\r\n");

            $row = static function ($out, array $cols): void {
                fputcsv($out, $cols, ';');
            };

            $row($out, ['LAPORAN TRANSAKSI RAYAKAN MOMEN']);
            $row($out, ['Diekspor', now()->format('d/m/Y H:i')]);
            $row($out, []);

            $row($out, ['RINGKASAN']);
            $row($out, ['Keterangan', 'Nilai']);
            $row($out, ['Total Terjual', $stats['total_transaksi']]);
            $row($out, ['Total Penghasilan', $rupiah($stats['total_penghasilan'])]);
            $row($out, ['Terjual Bulan Ini', $stats['bulan_ini_transaksi']]);
            $row($out, ['Penghasilan Bulan Ini', $rupiah($stats['bulan_ini_penghasilan'])]);
            $row($out, []);

            $row($out, ['PER TEMPLATE']);
            $row($out, ['Template', 'Terjual', 'Penghasilan']);
            foreach ($byTemplate as $tpl) {
                $row($out, [
                    $tpl['template_nama'],
                    $tpl['terjual'],
                    $rupiah($tpl['penghasilan']),
                ]);
            }
            if ($byTemplate === []) {
                $row($out, ['—', 0, $rupiah(0)]);
            }
            $row($out, []);

            $row($out, ['RIWAYAT TRANSAKSI']);
            $row($out, [
                'No',
                'Tanggal',
                'Pelanggan',
                'Slug',
                'Template',
                'Kategori',
                'Harga Asli',
                'Diskon (%)',
                'Diterima',
            ]);

            $no = 1;
            foreach ($items as $item) {
                $kat = (string) ($item['kategori'] ?? '');
                $row($out, [
                    $no++,
                    Carbon::parse($item['created_at'])->format('d/m/Y H:i'),
                    $item['pelanggan'] ?? '',
                    $item['slug'] ?? '',
                    $item['template_nama'] ?: ($item['template_key'] ?? ''),
                    $kategoriLabel[$kat] ?? $kat,
                    $rupiah((int) ($item['harga_asli'] ?? 0)),
                    rtrim(rtrim(number_format((float) ($item['diskon_persen'] ?? 0), 2, ',', '.'), '0'), ','),
                    $rupiah((int) ($item['harga_final'] ?? 0)),
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
