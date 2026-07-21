<?php

namespace App\Http\Controllers;

use App\Repositories\CampaignRepository;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(
        protected CampaignRepository $campaigns,
        protected CatalogRepository $catalog,
        protected CategoryRepository $categories
    ) {
    }

    public function index()
    {
        $campaign = $this->campaigns->getActiveForLanding();
        $categories = $this->categories->allActive();
        $homeTemplates = $this->catalog->forHome();

        return view('landing', compact('campaign', 'categories', 'homeTemplates'));
    }

    public function katalog(Request $request)
    {
        $categories = $this->categories->allActive();
        $allTemplates = collect($this->catalog->forKatalog())
            ->filter(fn ($t) => isset($categories[$t['kategori'] ?? '']))
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
