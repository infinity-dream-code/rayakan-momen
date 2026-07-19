<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/cdn.php');
    }

    public function boot(): void
    {
        // Hosting LiteSpeed: pretty URL sering 404. Hanya index.php/path yang jalan.
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $viaIndexPhp = str_contains($uri, '/index.php')
            || str_ends_with($script, '/index.php')
            || str_ends_with($script, 'index.php');

        if (! $viaIndexPhp) {
            return;
        }

        $root = rtrim((string) config('app.url'), '/');
        if ($root === '' || str_ends_with($root, '/index.php')) {
            return;
        }

        // asset()/CSS/JS tetap di domain root (bukan /index.php/css/...)
        config(['app.asset_url' => $root]);

        // route()/redirect()/form action → /index.php/panel/...
        URL::forceRootUrl($root.'/index.php');
    }
}
