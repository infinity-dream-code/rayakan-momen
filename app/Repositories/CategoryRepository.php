<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    public function all(): array
    {
        $this->syncFromConfig();

        $rows = DB::select(
            'SELECT slug, nama, tagline, deskripsi, icon, warna, aktif, sort_order, image_url, cloudinary_id
             FROM catalog_categories
             ORDER BY sort_order ASC, slug ASC'
        );

        $out = [];
        foreach ($rows as $row) {
            $out[$row->slug] = $this->rowToArray($row);
        }

        return $out;
    }

    public function allActive(): array
    {
        return array_filter($this->all(), fn (array $cat) => $cat['aktif']);
    }

    public function get(string $slug): ?array
    {
        return $this->all()[$slug] ?? null;
    }

    /**
     * @return array{image_url: ?string, cloudinary_id: ?string}
     */
    public function getImage(string $slug): array
    {
        $row = DB::selectOne(
            'SELECT image_url, cloudinary_id FROM catalog_categories WHERE slug = ? LIMIT 1',
            [$slug]
        );

        if (! $row) {
            return ['image_url' => null, 'cloudinary_id' => null];
        }

        return [
            'image_url' => $row->image_url ?: null,
            'cloudinary_id' => $row->cloudinary_id ?: null,
        ];
    }

    public function updateMany(array $rows): void
    {
        $now = now()->toDateTimeString();

        foreach ($rows as $slug => $row) {
            if (! is_string($slug) || ! is_array($row)) {
                continue;
            }
            if (! $this->slugExists($slug)) {
                continue;
            }

            DB::update(
                'UPDATE catalog_categories
                 SET nama = ?, tagline = ?, aktif = ?, updated_at = ?
                 WHERE slug = ?',
                [
                    trim((string) ($row['nama'] ?? '')),
                    trim((string) ($row['tagline'] ?? '')) ?: null,
                    ! empty($row['aktif']) ? 1 : 0,
                    $now,
                    $slug,
                ]
            );
        }
    }

    public function updateImage(string $slug, ?string $imageUrl, ?string $publicId): void
    {
        if (! $this->slugExists($slug)) {
            return;
        }

        DB::update(
            'UPDATE catalog_categories
             SET image_url = ?, cloudinary_id = ?, updated_at = ?
             WHERE slug = ?',
            [$imageUrl, $publicId, now()->toDateTimeString(), $slug]
        );
    }

    protected function syncFromConfig(): void
    {
        $defaults = config('templates.categories', []);
        if ($defaults === []) {
            return;
        }

        $now = now()->toDateTimeString();
        $sort = 0;

        foreach ($defaults as $slug => $cat) {
            DB::insert(
                'INSERT INTO catalog_categories (slug, nama, tagline, deskripsi, icon, warna, aktif, sort_order, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE slug = slug',
                [
                    $slug,
                    $cat['nama'] ?? ucfirst($slug),
                    $cat['tagline'] ?? null,
                    $cat['deskripsi'] ?? null,
                    $cat['icon'] ?? 'fa-layer-group',
                    $cat['warna'] ?? '#c9a84c',
                    $sort++,
                    $now,
                    $now,
                ]
            );
        }
    }

    protected function slugExists(string $slug): bool
    {
        $row = DB::selectOne('SELECT slug FROM catalog_categories WHERE slug = ? LIMIT 1', [$slug]);

        return $row !== null;
    }

    protected function rowToArray(object $row): array
    {
        return [
            'id' => $row->slug,
            'slug' => $row->slug,
            'nama' => $row->nama,
            'tagline' => $row->tagline,
            'deskripsi' => $row->deskripsi,
            'icon' => $row->icon,
            'warna' => $row->warna,
            'aktif' => (bool) $row->aktif,
            'sort_order' => (int) $row->sort_order,
            'image' => $row->image_url ?: null,
            'image_url' => $row->image_url ?: null,
            'cloudinary_id' => $row->cloudinary_id ?: null,
        ];
    }
}
