<?php

namespace App\Http\Controllers;

use App\Repositories\CampaignRepository;

class LandingController extends Controller
{
    public function __construct(protected CampaignRepository $campaigns)
    {
    }

    public function index()
    {
        $campaign = $this->campaigns->getActiveForLanding();

        return view('landing', compact('campaign'));
    }
}
