<?php

namespace App\Console\Commands;

use App\Repositories\InvitationRepository;
use Illuminate\Console\Command;

class ExpireInvitationsCommand extends Command
{
    protected $signature = 'undangan:expire';

    protected $description = 'Mark invitations past expires_at as expired/nonaktif';

    public function handle(InvitationRepository $repo): int
    {
        $rows = \Illuminate\Support\Facades\DB::select(
            "SELECT slug FROM invitations
             WHERE access_state = 'live'
               AND expires_at IS NOT NULL
               AND expires_at <= NOW()"
        );

        $count = $repo->markExpiredDue();

        foreach ($rows as $row) {
            $repo->forgetClientCache($row->slug ?? '');
        }

        $this->info("Expired {$count} invitation(s).");

        return self::SUCCESS;
    }
}
