<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\InvitationRepository;
use App\Repositories\SiteMetricsRepository;

class DashboardController extends Controller
{
    public function __construct(
        protected InvitationRepository $storage,
        protected SiteMetricsRepository $metrics
    ) {
    }

    public function index()
    {
        $stats = $this->storage->stats();
        $stats['wa_clicks'] = $this->metrics->get('wa_clicks');
        $undangan = $this->storage->recentForAdmin(5);

        return view('admin.dashboard', compact('stats', 'undangan'));
    }
}
