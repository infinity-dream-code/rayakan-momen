<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CatalogRepository
{
    public function allOverrides(): array
    {
        $rows = DB::select(
            'SELECT template_key, kategori, harga, diskon_persen, aktif_katalog, tampil_home, preview_image_url, preview_cloudinary_id FROM catalog_templates'
        );

        $out = [];
        foreach ($rows as $row) {
            $out[$row->template_key] = [
                'kategori' => $row->kategori ?: null,
                'harga' => (int) $row->harga,
                'diskon_persen' => (float) $row->diskon_persen,
                'aktif_katalog' => (bool) $row->aktif_katalog,
                'tampil_home' => (bool) $row->tampil_home,
                'preview_image_url' => $row->preview_image_url ?: null,
                'preview_cloudinary_id' => $row->preview_cloudinary_id ?: null,
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
            $kategori = $ov['kategori'] ?? $t['kategori'] ?? 'wedding';

            $merged[$key] = array_merge($t, [
                'kategori' => $kategori,
                'harga' => $harga,
                'diskon_persen' => $diskon,
                'harga_final' => $final,
                'punya_diskon' => $diskon > 0 && $harga > 0,
                'aktif_katalog' => array_key_exists('aktif_katalog', $ov)
                    ? (bool) $ov['aktif_katalog']
                    : true,
                'tampil_home' => (bool) ($ov['tampil_home'] ?? false),
                'preview' => $ov['preview_image_url'] ?? $t['preview'] ?? null,
            ]);
        }

        $this->syncMissingKeys($merged, $overrides);

        return $merged;
    }

    public function forHome(): array
    {
        return array_filter($this->templates(), fn (array $t) => ! empty($t['tampil_home']));
    }

    public function forKatalog(): array
    {
        return array_filter($this->templates(), fn (array $t) => ($t['aktif_katalog'] ?? true));
    }

    protected function syncMissingKeys(array $merged, array $overrides): void
    {
        foreach ($merged as $key => $t) {
            if (! isset($overrides[$key])) {
                DB::insert(
                    'INSERT INTO catalog_templates (template_key, kategori, harga, diskon_persen, aktif_katalog, tampil_home, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE template_key = template_key',
                    [
                        $key,
                        $t['kategori'] ?? null,
                        (int) ($t['harga'] ?? 0),
                        (float) ($t['diskon_persen'] ?? 0),
                        1,
                        0,
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
            $home = ! empty($row['tampil_home']) ? 1 : 0;
            $kategori = isset($row['kategori']) ? (string) $row['kategori'] : null;

            DB::insert(
                'INSERT INTO catalog_templates (template_key, kategori, harga, diskon_persen, aktif_katalog, tampil_home, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    kategori = VALUES(kategori),
                    harga = VALUES(harga),
                    diskon_persen = VALUES(diskon_persen),
                    aktif_katalog = VALUES(aktif_katalog),
                    tampil_home = VALUES(tampil_home),
                    updated_at = VALUES(updated_at)',
                [$key, $kategori, $harga, $diskon, $aktif, $home, $now, $now]
            );
        }
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * @return array{preview_image_url: ?string, preview_cloudinary_id: ?string}
     */
    public function getPreview(string $templateKey): array
    {
        $row = DB::selectOne(
            'SELECT preview_image_url, preview_cloudinary_id FROM catalog_templates WHERE template_key = ? LIMIT 1',
            [$templateKey]
        );

        if (! $row) {
            return [
                'preview_image_url' => null,
                'preview_cloudinary_id' => null,
            ];
        }

        return [
            'preview_image_url' => $row->preview_image_url ?: null,
            'preview_cloudinary_id' => $row->preview_cloudinary_id ?: null,
        ];
    }

    public function updatePreview(string $templateKey, ?string $imageUrl, ?string $publicId): void
    {
        $now = now()->toDateTimeString();
        $known = config('templates.templates.'.$templateKey, []);
        $harga = (int) ($known['harga'] ?? 0);
        $kategori = $known['kategori'] ?? null;

        DB::insert(
            'INSERT INTO catalog_templates (template_key, kategori, harga, diskon_persen, aktif_katalog, tampil_home, preview_image_url, preview_cloudinary_id, created_at, updated_at)
             VALUES (?, ?, ?, 0, 1, 0, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                preview_image_url = VALUES(preview_image_url),
                preview_cloudinary_id = VALUES(preview_cloudinary_id),
                updated_at = VALUES(updated_at)',
            [$templateKey, $kategori, $harga, $imageUrl, $publicId, $now, $now]
        );
    }
}
