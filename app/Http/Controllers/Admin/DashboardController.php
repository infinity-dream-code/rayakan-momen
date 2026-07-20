<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CampaignRepository;
use App\Repositories\InvitationRepository;

class DashboardController extends Controller
{
    public function __construct(
        protected InvitationRepository $storage,
        protected CampaignRepository $campaigns
    ) {
    }

    public function index()
    {
        $stats = $this->storage->stats();
        $undangan = $this->storage->recentForAdmin(5);
        $campaign = $this->campaigns->get();

        return view('admin.dashboard', compact('stats', 'undangan', 'campaign'));
    }
}
