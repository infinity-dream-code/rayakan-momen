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
        // Di local (php artisan serve / Laragon), jangan paksa APP_URL —
        // biar asset() ikut host+port request (mis. localhost:8000).
        // Kalau di-force ke http://localhost, CSS/JS mengarah ke port 80 → “CSS tidak ke-load”.
        if ($this->app->environment('local')) {
            return;
        }

        // Production/hosting: pastikan URL absolut pakai APP_URL (tanpa /index.php)
        $root = rtrim((string) config('app.url'), '/');
        if ($root === '') {
            return;
        }

        if (str_ends_with($root, '/index.php')) {
            $root = substr($root, 0, -strlen('/index.php'));
        }

        URL::forceRootUrl($root);

        if (str_starts_with($root, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
