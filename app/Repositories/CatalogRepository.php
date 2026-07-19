<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CatalogRepository
{
    public function allOverrides(): array
    {
        $rows = DB::select(
            'SELECT template_key, harga, diskon_persen, aktif_katalog FROM catalog_templates'
        );

        $out = [];
        foreach ($rows as $row) {
            $out[$row->template_key] = [
                'harga' => (int) $row->harga,
                'diskon_persen' => (float) $row->diskon_persen,
                'aktif_katalog' => (bool) $row->aktif_katalog,
            ];
        }

        return $out;
    }

    public function templates(): array
    {
        $overrides = $this->allOverrides();
        $merged = [];

        foreach (config('templates.templates', []) as $key => $t) {
            $ov = $overrides[$key] ?? [];
            $harga = (int) ($ov['harga'] ?? $t['harga'] ?? 0);
            $diskon = (float) ($ov['diskon_persen'] ?? 0);
            $diskon = max(0, min(100, $diskon));
            $final = $this->hargaFinal($harga, $diskon);

            $merged[$key] = array_merge($t, [
                'harga' => $harga,
                'diskon_persen' => $diskon,
                'harga_final' => $final,
                'punya_diskon' => $diskon > 0 && $harga > 0,
                'aktif_katalog' => array_key_exists('aktif_katalog', $ov)
                    ? (bool) $ov['aktif_katalog']
                    : true,
            ]);
        }

        $this->syncMissingKeys($merged, $overrides);

        return $merged;
    }

    protected function syncMissingKeys(array $merged, array $overrides): void
    {
        foreach ($merged as $key => $t) {
            if (! isset($overrides[$key])) {
                DB::insert(
                    'INSERT INTO catalog_templates (template_key, harga, diskon_persen, aktif_katalog, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE template_key = template_key',
                    [
                        $key,
                        (int) ($t['harga'] ?? 0),
                        (float) ($t['diskon_persen'] ?? 0),
                        1,
                        now()->toDateTimeString(),
                        now()->toDateTimeString(),
                    ]
                );
            }
        }
    }

    public function hargaFinal(int $harga, float $diskonPersen): int
    {
        if ($harga <= 0) {
            return 0;
        }
        if ($diskonPersen <= 0) {
            return $harga;
        }

        return (int) round($harga * (1 - ($diskonPersen / 100)));
    }

    public function updateMany(array $rows): void
    {
        $now = now()->toDateTimeString();

        foreach ($rows as $key => $row) {
            if (! is_array($row)) {
                continue;
            }
            $harga = max(0, (int) ($row['harga'] ?? 0));
            $diskon = max(0, min(100, (float) ($row['diskon_persen'] ?? 0)));
            $aktif = ! empty($row['aktif_katalog']) ? 1 : 0;

            DB::insert(
                'INSERT INTO catalog_templates (template_key, harga, diskon_persen, aktif_katalog, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    harga = VALUES(harga),
                    diskon_persen = VALUES(diskon_persen),
                    aktif_katalog = VALUES(aktif_katalog),
                    updated_at = VALUES(updated_at)',
                [$key, $harga, $diskon, $aktif, $now, $now]
            );
        }
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
