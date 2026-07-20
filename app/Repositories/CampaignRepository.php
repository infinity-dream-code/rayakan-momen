<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CampaignRepository
{
    /**
     * @return array{aktif: bool, image_url: ?string, cloudinary_public_id: ?string}
     */
    public function get(): array
    {
        $row = DB::selectOne('SELECT aktif, image_url, cloudinary_public_id FROM landing_campaign WHERE id = 1 LIMIT 1');

        if (! $row) {
            return [
                'aktif' => false,
                'image_url' => null,
                'cloudinary_public_id' => null,
            ];
        }

        return [
            'aktif' => (bool) $row->aktif,
            'image_url' => $row->image_url ?: null,
            'cloudinary_public_id' => $row->cloudinary_public_id ?: null,
        ];
    }

    /**
     * Data untuk popup landing (hanya jika aktif + ada gambar).
     *
     * @return array{image_url: string}|null
     */
    public function getActiveForLanding(): ?array
    {
        $campaign = $this->get();
        if (! $campaign['aktif'] || ! filled($campaign['image_url'])) {
            return null;
        }

        return [
            'image_url' => $campaign['image_url'],
        ];
    }

    /**
     * @param  array{aktif?: bool, image_url?: ?string, cloudinary_public_id?: ?string}  $data
     */
    public function save(array $data): void
    {
        $current = $this->get();

        DB::update(
            'UPDATE landing_campaign SET aktif = ?, image_url = ?, cloudinary_public_id = ?, updated_at = ? WHERE id = 1',
            [
                array_key_exists('aktif', $data) ? (int) (bool) $data['aktif'] : (int) $current['aktif'],
                array_key_exists('image_url', $data) ? $data['image_url'] : $current['image_url'],
                array_key_exists('cloudinary_public_id', $data) ? $data['cloudinary_public_id'] : $current['cloudinary_public_id'],
                now()->toDateTimeString(),
            ]
        );
    }
}
