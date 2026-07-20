<?php

namespace App\Http\Middleware;

use App\Services\AdminPinGuard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoAdminAuth
{
    public function __construct(protected AdminPinGuard $pinGuard)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->pinGuard->isBlocked($request)) {
            $request->session()->flush();
            abort(403, 'Akses ditolak.');
        }

        if ($request->session()->get('demo_admin_pending_pin') && ! $request->session()->get('demo_admin_logged_in')) {
            return redirect()->route('admin.pin')->with('error', 'Masukkan PIN terlebih dahulu.');
        }

        if (! $request->session()->get('demo_admin_logged_in')) {
            return redirect('/SmartLoginAdmin')->with('error', 'Silakan login dulu untuk mengakses panel admin.');
        }

        return $next($request);
    }
}
