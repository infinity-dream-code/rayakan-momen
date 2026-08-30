<?php

namespace App\Repositories;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteMetricsRepository
{
    protected function ensureTable(): void
    {
        if (Schema::hasTable('site_metrics')) {
            return;
        }

        Schema::create('site_metrics', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function get(string $key): int
    {
        try {
            $this->ensureTable();
            $row = DB::selectOne('SELECT value FROM site_metrics WHERE `key` = ? LIMIT 1', [$key]);

            return (int) ($row->value ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    public function increment(string $key, int $by = 1): int
    {
        try {
            $this->ensureTable();
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
