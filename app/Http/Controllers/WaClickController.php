<?php

namespace App\Http\Controllers;

use App\Repositories\SiteMetricsRepository;
use Illuminate\Http\Request;

class WaClickController extends Controller
{
    public function store(Request $request, SiteMetricsRepository $metrics)
    {
        $metrics->increment('wa_clicks');

        return response()->json(['ok' => true]);
    }
}
