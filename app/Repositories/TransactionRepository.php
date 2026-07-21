<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    /**
     * Catat penjualan saat undangan dibuat.
     * harga_final = harga setelah diskon (yang benar-benar diterima).
     *
     * @param  array{
     *   invitation_id?: ?string,
     *   slug?: ?string,
     *   template_key: string,
     *   template_nama?: ?string,
     *   kategori?: ?string,
     *   pelanggan?: ?string,
     *   harga_asli: int,
     *   diskon_persen: float|int,
     *   harga_final: int
     * }  $data
     */
    public function record(array $data): void
    {
        DB::insert(
            'INSERT INTO sales_transactions (
                invitation_id, slug, template_key, template_nama, kategori, pelanggan,
                harga_asli, diskon_persen, harga_final, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['invitation_id'] ?? null,
                $data['slug'] ?? null,
                $data['template_key'],
                $data['template_nama'] ?? null,
                $data['kategori'] ?? null,
                $data['pelanggan'] ?? null,
                max(0, (int) ($data['harga_asli'] ?? 0)),
                max(0, min(100, (float) ($data['diskon_persen'] ?? 0))),
                max(0, (int) ($data['harga_final'] ?? 0)),
                // Simpan UTC di DB, tampilkan sebagai Asia/Jakarta
                now('UTC')->toDateTimeString(),
            ]
        );
    }

    /**
     * Format waktu transaksi ke Asia/Jakarta (WIB).
     * created_at di DB disimpan UTC.
     */
    public static function formatWib(?string $datetime, string $format = 'd/m/Y H:i'): string
    {
        if (! $datetime) {
            return '—';
        }

        return \Carbon\Carbon::parse($datetime, 'UTC')
            ->timezone('Asia/Jakarta')
            ->format($format);
    }

    /**
     * @return array{
     *   total_transaksi: int,
     *   total_penghasilan: int,
     *   bulan_ini_transaksi: int,
     *   bulan_ini_penghasilan: int
     * }
     */
    public function stats(): array
    {
        $all = DB::selectOne(
            'SELECT COUNT(*) AS total_transaksi, COALESCE(SUM(harga_final), 0) AS total_penghasilan
             FROM sales_transactions'
        );

        $monthStartUtc = now('Asia/Jakarta')->startOfMonth()->utc()->toDateTimeString();

        $month = DB::selectOne(
            'SELECT COUNT(*) AS total_transaksi, COALESCE(SUM(harga_final), 0) AS total_penghasilan
             FROM sales_transactions
             WHERE created_at >= ?',
            [$monthStartUtc]
        );

        return [
            'total_transaksi' => (int) ($all->total_transaksi ?? 0),
            'total_penghasilan' => (int) ($all->total_penghasilan ?? 0),
            'bulan_ini_transaksi' => (int) ($month->total_transaksi ?? 0),
            'bulan_ini_penghasilan' => (int) ($month->total_penghasilan ?? 0),
        ];
    }

    /**
     * Ringkasan per template: laku berapa, penghasilan berapa.
     *
     * @return array<int, array{template_key: string, template_nama: string, terjual: int, penghasilan: int}>
     */
    public function summaryByTemplate(): array
    {
        $rows = DB::select(
            'SELECT template_key,
                    MAX(template_nama) AS template_nama,
                    COUNT(*) AS terjual,
                    COALESCE(SUM(harga_final), 0) AS penghasilan
             FROM sales_transactions
             GROUP BY template_key
             ORDER BY penghasilan DESC, terjual DESC'
        );

        return array_map(fn ($r) => [
            'template_key' => $r->template_key,
            'template_nama' => $r->template_nama ?: $r->template_key,
            'terjual' => (int) $r->terjual,
            'penghasilan' => (int) $r->penghasilan,
        ], $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $rows = DB::select(
            'SELECT id, invitation_id, slug, template_key, template_nama, kategori, pelanggan,
                    harga_asli, diskon_persen, harga_final, created_at
             FROM sales_transactions
             ORDER BY created_at DESC, id DESC'
        );

        return array_map(fn ($r) => (array) $r, $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recent(int $limit = 100): array
    {
        $rows = DB::select(
            'SELECT id, invitation_id, slug, template_key, template_nama, kategori, pelanggan,
                    harga_asli, diskon_persen, harga_final, created_at
             FROM sales_transactions
             ORDER BY created_at DESC, id DESC
             LIMIT ?',
            [$limit]
        );

        return array_map(fn ($r) => (array) $r, $rows);
    }

    public function delete(int $id): bool
    {
        return DB::delete('DELETE FROM sales_transactions WHERE id = ?', [$id]) > 0;
    }
}
