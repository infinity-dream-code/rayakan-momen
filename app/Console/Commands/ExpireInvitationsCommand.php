<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireInvitationsCommand extends Command
{
    protected $signature = 'undangan:expire';

    protected $description = 'No-op: undangan tidak lagi expire otomatis (nonaktif manual via admin toggle)';

    public function handle(): int
    {
        $this->info('Auto-expire dimatikan. Nonaktifkan undangan lewat toggle di panel admin.');

        return self::SUCCESS;
    }
}
