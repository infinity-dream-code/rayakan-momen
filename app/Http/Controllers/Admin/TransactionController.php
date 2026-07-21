<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\TransactionRepository;
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
        $filename = 'transaksi-rayakanmomen-'.now()->format('Ymd-His').'.xls';

        $kategoriLabel = [
            'wedding' => 'Pernikahan',
            'ultah_anak' => 'Ulang Tahun Anak',
            'couple' => 'Untuk Pasangan',
        ];

        $e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $rupiah = static fn (int $n): string => 'Rp '.number_format($n, 0, ',', '.');

        return response()->streamDownload(function () use ($items, $byTemplate, $stats, $kategoriLabel, $e, $rupiah) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            echo '<x:Name>Transaksi</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
            echo '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1a2234; }
                table { border-collapse: collapse; }
                .title { font-size: 16pt; font-weight: bold; color: #1a2234; }
                .sub { color: #666666; font-size: 10pt; }
                .section { font-size: 12pt; font-weight: bold; color: #1a2234; background: #faf7f2; }
                .th { background: #c9a84c; color: #12161f; font-weight: bold; border: 1px solid #a8843a; padding: 8px 10px; text-align: center; vertical-align: middle; }
                .td { border: 1px solid #d4c4a0; padding: 7px 10px; vertical-align: middle; }
                .td-num { border: 1px solid #d4c4a0; padding: 7px 10px; text-align: right; vertical-align: middle; }
                .td-center { border: 1px solid #d4c4a0; padding: 7px 10px; text-align: center; vertical-align: middle; }
                .money { color: #047857; font-weight: bold; }
                .label { border: 1px solid #d4c4a0; padding: 7px 10px; background: #fffdf8; }
                .alt { background: #fffdf8; }
            </style></head><body>';

            // Judul
            echo '<table>';
            echo '<tr><td class="title" colspan="9">LAPORAN TRANSAKSI RAYAKAN MOMEN</td></tr>';
            echo '<tr><td class="sub" colspan="9">Diekspor: '.$e(now('Asia/Jakarta')->format('d/m/Y H:i')).'</td></tr>';
            echo '<tr><td colspan="9">&nbsp;</td></tr>';
            echo '</table>';

            // Ringkasan
            echo '<table>';
            echo '<tr><td class="section" colspan="2">RINGKASAN</td></tr>';
            echo '<tr><td class="th" width="220">Keterangan</td><td class="th" width="180">Nilai</td></tr>';
            echo '<tr><td class="label">Total Terjual</td><td class="td-num">'.$e($stats['total_transaksi']).'</td></tr>';
            echo '<tr><td class="label">Total Penghasilan</td><td class="td-num money">'.$e($rupiah($stats['total_penghasilan'])).'</td></tr>';
            echo '<tr><td class="label">Terjual Bulan Ini</td><td class="td-num">'.$e($stats['bulan_ini_transaksi']).'</td></tr>';
            echo '<tr><td class="label">Penghasilan Bulan Ini</td><td class="td-num money">'.$e($rupiah($stats['bulan_ini_penghasilan'])).'</td></tr>';
            echo '</table>';

            echo '<br>';

            // Per template
            echo '<table>';
            echo '<tr><td class="section" colspan="3">PER TEMPLATE</td></tr>';
            echo '<tr>';
            echo '<td class="th" width="220">Template</td>';
            echo '<td class="th" width="100">Terjual</td>';
            echo '<td class="th" width="180">Penghasilan</td>';
            echo '</tr>';
            if ($byTemplate === []) {
                echo '<tr><td class="td" colspan="3" style="text-align:center;color:#999;">Belum ada data</td></tr>';
            } else {
                foreach ($byTemplate as $i => $tpl) {
                    $cls = ($i % 2 === 1) ? ' alt' : '';
                    echo '<tr>';
                    echo '<td class="td'.$cls.'">'.$e($tpl['template_nama']).'</td>';
                    echo '<td class="td-center'.$cls.'">'.$e($tpl['terjual']).'</td>';
                    echo '<td class="td-num money'.$cls.'">'.$e($rupiah($tpl['penghasilan'])).'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';

            echo '<br>';

            // Riwayat
            echo '<table>';
            echo '<tr><td class="section" colspan="9">RIWAYAT TRANSAKSI</td></tr>';
            echo '<tr>';
            foreach ([
                ['No', 40],
                ['Tanggal', 120],
                ['Pelanggan', 160],
                ['Slug', 120],
                ['Template', 140],
                ['Kategori', 130],
                ['Harga Asli', 120],
                ['Diskon (%)', 90],
                ['Diterima', 120],
            ] as [$label, $w]) {
                echo '<td class="th" width="'.$w.'">'.$e($label).'</td>';
            }
            echo '</tr>';

            if ($items === []) {
                echo '<tr><td class="td" colspan="9" style="text-align:center;color:#999;">Belum ada transaksi</td></tr>';
            } else {
                $no = 1;
                foreach ($items as $i => $item) {
                    $cls = ($i % 2 === 1) ? ' alt' : '';
                    $kat = (string) ($item['kategori'] ?? '');
                    $diskon = (float) ($item['diskon_persen'] ?? 0);
                    $diskonTxt = rtrim(rtrim(number_format($diskon, 2, ',', '.'), '0'), ',');

                    echo '<tr>';
                    echo '<td class="td-center'.$cls.'">'.$e($no++).'</td>';
                    echo '<td class="td-center'.$cls.'">'.$e(\App\Repositories\TransactionRepository::formatWib($item['created_at'] ?? null)).'</td>';
                    echo '<td class="td'.$cls.'">'.$e($item['pelanggan'] ?? '').'</td>';
                    echo '<td class="td'.$cls.'">'.$e($item['slug'] ?? '').'</td>';
                    echo '<td class="td'.$cls.'">'.$e($item['template_nama'] ?: ($item['template_key'] ?? '')).'</td>';
                    echo '<td class="td'.$cls.'">'.$e($kategoriLabel[$kat] ?? $kat).'</td>';
                    echo '<td class="td-num'.$cls.'">'.$e($rupiah((int) ($item['harga_asli'] ?? 0))).'</td>';
                    echo '<td class="td-center'.$cls.'">'.$e($diskonTxt).'</td>';
                    echo '<td class="td-num money'.$cls.'">'.$e($rupiah((int) ($item['harga_final'] ?? 0))).'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';

            echo '</body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
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
