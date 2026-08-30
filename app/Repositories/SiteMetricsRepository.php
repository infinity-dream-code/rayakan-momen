<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SiteMetricsRepository
{
    public function get(string $key): int
    {
        try {
            $row = DB::selectOne('SELECT value FROM site_metrics WHERE `key` = ? LIMIT 1', [$key]);

            return (int) ($row->value ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    public function increment(string $key, int $by = 1): int
    {
        try {
            $now = now()->toDateTimeString();

            DB::insert(
                'INSERT INTO site_metrics (`key`, value, updated_at) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = value + ?, updated_at = ?',
                [$key, $by, $now, $by, $now]
            );

            return $this->get($key);
        } catch (\Throwable) {
            return 0;
        }
    }
}
