<?php

/**
 * Path ke project Laravel (folder berisi vendor/, app/, .env).
 *
 * - Lokal Laragon: biasanya null (project = induk folder public/).
 * - Server cPanel: project di /home/rayakanm/website-undangan,
 *   public di /home/rayakanm/public_html.
 */

$candidates = [
    // Server Rayakan Momen (cPanel)
    '/home/rayakanm/website-undangan',

    // Kalau public_html/laravel-path.php → induk home + /website-undangan
    dirname(__DIR__).'/website-undangan',

    // Struktur lama: project = induk public_html / public
    dirname(__DIR__),
];

foreach ($candidates as $path) {
    $path = rtrim(str_replace('\\', '/', $path), '/');
    if ($path !== '' && is_file($path.'/vendor/autoload.php')) {
        return $path;
    }
}

// Fallback: biarkan index.php pakai dirname(__DIR__)
return null;
