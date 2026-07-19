<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('demo_admin_logged_in')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login dulu untuk mengakses panel admin.');
        }

        return $next($request);
    }
}
