<?php

namespace App\Http\Controllers;

use App\Repositories\CampaignRepository;
use App\Repositories\CatalogRepository;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(
        protected CampaignRepository $campaigns,
        protected CatalogRepository $catalog
    ) {
    }

    public function index()
    {
        $campaign = $this->campaigns->getActiveForLanding();

        return view('landing', compact('campaign'));
    }

    public function katalog(Request $request)
    {
        $categories = config('templates.categories', []);
        $allTemplates = collect($this->catalog->templates())
            ->filter(fn ($t) => ($t['aktif_katalog'] ?? true))
            ->all();

        $activeKat = (string) $request->query('kategori', 'all');
        if ($activeKat !== 'all' && ! array_key_exists($activeKat, $categories)) {
            $activeKat = 'all';
        }

        return view('katalog', [
            'categories' => $categories,
            'allTemplates' => $allTemplates,
            'catalog' => $this->catalog,
            'activeKat' => $activeKat,
            'campaign' => null,
        ]);
    }
}
