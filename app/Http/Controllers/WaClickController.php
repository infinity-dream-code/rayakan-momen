<?php

namespace App\Http\Controllers;

use App\Repositories\SiteMetricsRepository;
use Illuminate\Http\Request;

class WaClickController extends Controller
{
    public function store(Request $request, SiteMetricsRepository $metrics)
    {
        $total = $metrics->increment('wa_clicks');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'total' => $total]);
        }

        // Pixel / sendBeacon GET — balas GIF 1x1 transparan
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
