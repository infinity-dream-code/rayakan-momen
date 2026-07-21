<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CategoryRepository
{
    public function all(): array
    {
        $this->syncFromConfig();

        $rows = DB::select(
            'SELECT slug, nama, tagline, deskripsi, icon, warna, aktif, sort_order, image_url, cloudinary_id
             FROM catalog_categories
             ORDER BY sort_order ASC, nama ASC'
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
        $row = DB::selectOne(
            'SELECT slug, nama, tagline, deskripsi, icon, warna, aktif, sort_order, image_url, cloudinary_id
             FROM catalog_categories WHERE slug = ? LIMIT 1',
            [$slug]
        );

        return $row ? $this->rowToArray($row) : null;
    }

    public function create(string $nama): string
    {
        $nama = trim($nama);
        if ($nama === '') {
            throw new InvalidArgumentException('Nama jenis wajib diisi.');
        }

        $slug = $this->uniqueSlug(Str::slug($nama) ?: 'jenis');
        $now = now()->toDateTimeString();
        $sort = (int) (DB::selectOne('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM catalog_categories')->n ?? 0);

        DB::insert(
            'INSERT INTO catalog_categories (slug, nama, aktif, sort_order, icon, warna, created_at, updated_at)
             VALUES (?, ?, 1, ?, ?, ?, ?, ?)',
            [$slug, $nama, $sort, 'fa-layer-group', '#c9a84c', $now, $now]
        );

        return $slug;
    }

    public function update(string $slug, string $nama, bool $aktif = true): void
    {
        if ($this->get($slug) === null) {
            throw new InvalidArgumentException('Jenis tidak ditemukan.');
        }

        $nama = trim($nama);
        if ($nama === '') {
            throw new InvalidArgumentException('Nama jenis wajib diisi.');
        }

        DB::update(
            'UPDATE catalog_categories SET nama = ?, aktif = ?, updated_at = ? WHERE slug = ?',
            [$nama, $aktif ? 1 : 0, now()->toDateTimeString(), $slug]
        );
    }

    public function delete(string $slug): void
    {
        if ($this->get($slug) === null) {
            throw new InvalidArgumentException('Jenis tidak ditemukan.');
        }

        $used = (int) (DB::selectOne(
            'SELECT COUNT(*) AS c FROM catalog_templates WHERE kategori = ?',
            [$slug]
        )->c ?? 0);

        if ($used > 0) {
            throw new InvalidArgumentException('Jenis masih dipakai '.$used.' template. Pindahkan dulu di Setting.');
        }

        DB::delete('DELETE FROM catalog_categories WHERE slug = ?', [$slug]);
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = Str::limit($base, 50, '');
        $slug = $slug !== '' ? $slug : 'jenis';
        $candidate = $slug;
        $i = 2;

        while ($this->get($candidate) !== null) {
            $candidate = Str::limit($slug, 47, '').'-'.$i;
            $i++;
        }

        return $candidate;
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
