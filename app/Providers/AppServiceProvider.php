<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/cdn.php');

        // public_html sering di LUAR folder project (sibling), bukan di dalamnya.
        // Contoh: /home/USER/website-undangan + /home/USER/public_html
        $candidates = [];

        // 1) Document root web server (paling akurat di hosting)
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if (is_string($docRoot) && $docRoot !== '') {
            $candidates[] = rtrim(str_replace('\\', '/', $docRoot), '/');
        }

        // 2) Sibling public_html (project di subfolder, public di home)
        $candidates[] = dirname(base_path()).DIRECTORY_SEPARATOR.'public_html';

        // 3) Fallback klasik
        $candidates[] = base_path('public_html');
        $candidates[] = base_path('public');

        foreach ($candidates as $path) {
            $path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $path), DIRECTORY_SEPARATOR);
            if ($path !== '' && is_dir($path) && is_file($path.DIRECTORY_SEPARATOR.'index.php')) {
                $this->app->usePublicPath($path);
                break;
            }
        }
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
