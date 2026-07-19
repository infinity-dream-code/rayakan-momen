<?php

namespace App\Console\Commands;

use App\Repositories\CatalogRepository;
use App\Repositories\InvitationRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrateJsonInvitationsCommand extends Command
{
    protected $signature = 'undangan:migrate-json {--force : Re-import even if slug exists}';

    protected $description = 'Import invitations.json and catalog.json into MySQL';

    public function handle(InvitationRepository $invitations, CatalogRepository $catalog): int
    {
        $this->importCatalog($catalog);
        $this->importInvitations($invitations);

        return self::SUCCESS;
    }

    protected function importCatalog(CatalogRepository $catalog): void
    {
        $path = storage_path('app/catalog.json');
        if (! File::exists($path)) {
            $this->warn('catalog.json not found — seeding from config.');
            $catalog->templates();

            return;
        }

        $data = json_decode(File::get($path), true);
        if (! is_array($data)) {
            $this->error('Invalid catalog.json');

            return;
        }

        $catalog->updateMany($data);
        $this->info('Catalog imported: '.count($data).' template(s).');
    }

    protected function importInvitations(InvitationRepository $invitations): void
    {
        $path = storage_path('app/invitations.json');
        if (! File::exists($path)) {
            $this->warn('invitations.json not found — skip.');

            return;
        }

        $items = json_decode(File::get($path), true);
        if (! is_array($items)) {
            $this->error('Invalid invitations.json');

            return;
        }

        $force = (bool) $this->option('force');
        $imported = 0;
        $skipped = 0;

        foreach ($items as $item) {
            if (! is_array($item) || empty($item['slug'])) {
                continue;
            }
            $slug = Str::lower($item['slug']);
            if (! $force && $invitations->slugExists($slug)) {
                $skipped++;
                continue;
            }
            if ($force && $invitations->slugExists($slug)) {
                $existing = $invitations->findBySlug($slug);
                if ($existing) {
                    $invitations->delete($existing['id']);
                }
            }

            $wishes = $item['ucapan_tersimpan'] ?? [];
            unset($item['ucapan_tersimpan'], $item['id']);

            // Preserve dates if present
            $created = $item['created_at'] ?? null;
            $row = $invitations->create($item);

            if ($created && ! empty($row['id'])) {
                // Adjust expires/purge from original created_at
                $createdAt = \Illuminate\Support\Carbon::parse($created);
                $expireDays = (int) config('undangan.expire_days', 90);
                $purgeDays = (int) config('undangan.purge_days', 180);
                \Illuminate\Support\Facades\DB::update(
                    'UPDATE invitations SET created_at = ?, expires_at = ?, purge_at = ?, views = ? WHERE id = ?',
                    [
                        $createdAt->toDateTimeString(),
                        $createdAt->copy()->addDays($expireDays)->toDateTimeString(),
                        $createdAt->copy()->addDays($purgeDays)->toDateTimeString(),
                        (int) ($item['views'] ?? $row['views'] ?? 0),
                        $row['id'],
                    ]
                );
            }

            foreach (array_reverse($wishes) as $w) {
                if (! is_array($w)) {
                    continue;
                }
                \Illuminate\Support\Facades\DB::insert(
                    'INSERT INTO invitation_wishes (invitation_id, nama, ucapan, kehadiran, created_at) VALUES (?, ?, ?, ?, ?)',
                    [
                        $row['id'],
                        $w['nama'] ?? 'Tamu',
                        $w['ucapan'] ?? '',
                        $w['kehadiran'] ?? 'hadir',
                        $w['created_at'] ?? now()->toDateTimeString(),
                    ]
                );
            }

            $imported++;
            $this->line("Imported /{$slug}");
        }

        $this->info("Invitations imported: {$imported}, skipped: {$skipped}");
    }
}
