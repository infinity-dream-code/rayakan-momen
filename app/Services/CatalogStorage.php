<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class CatalogStorage
{
    protected string $path;

    public function __construct()
    {
        $this->path = storage_path('app/catalog.json');
        $this->ensureFile();
    }

    protected function ensureFile(): void
    {
        if (! File::exists($this->path)) {
            File::put($this->path, json_encode($this->defaultsFromConfig(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    protected function defaultsFromConfig(): array
    {
        $items = [];
        foreach (config('templates.templates', []) as $key => $t) {
            $items[$key] = [
                'harga' => (int) ($t['harga'] ?? 0),
                'diskon_persen' => 0,
                'aktif_katalog' => true,
            ];
        }

        return $items;
    }

    public function allOverrides(): array
    {
        $data = json_decode(File::get($this->path), true);

        return is_array($data) ? $data : [];
    }

    public function saveOverrides(array $items): void
    {
        File::put($this->path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Merge config templates with stored prices/discounts.
     */
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
                'aktif_katalog' => (bool) ($ov['aktif_katalog'] ?? true),
            ]);
        }

        // Keep overrides in sync when new templates appear in config
        $this->syncMissingKeys($merged, $overrides);

        return $merged;
    }

    protected function syncMissingKeys(array $merged, array $overrides): void
    {
        $changed = false;
        foreach ($merged as $key => $t) {
            if (! isset($overrides[$key])) {
                $overrides[$key] = [
                    'harga' => (int) ($t['harga'] ?? 0),
                    'diskon_persen' => (float) ($t['diskon_persen'] ?? 0),
                    'aktif_katalog' => true,
                ];
                $changed = true;
            }
        }
        if ($changed) {
            $this->saveOverrides($overrides);
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
        $overrides = $this->allOverrides();

        foreach ($rows as $key => $row) {
            if (! is_array($row)) {
                continue;
            }
            $harga = max(0, (int) ($row['harga'] ?? 0));
            $diskon = max(0, min(100, (float) ($row['diskon_persen'] ?? 0)));
            $overrides[$key] = [
                'harga' => $harga,
                'diskon_persen' => $diskon,
                'aktif_katalog' => ! empty($row['aktif_katalog']),
            ];
        }

        $this->saveOverrides($overrides);
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
