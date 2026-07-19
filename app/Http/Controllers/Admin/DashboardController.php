<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InvitationStorage;

class DashboardController extends Controller
{
    public function __construct(protected InvitationStorage $storage)
    {
    }

    public function index()
    {
        $stats = $this->storage->stats();
        $undangan = collect($this->storage->all())
            ->sortByDesc('updated_at')
            ->take(5)
            ->values()
            ->all();

        return view('admin.dashboard', compact('stats', 'undangan'));
    }
}
