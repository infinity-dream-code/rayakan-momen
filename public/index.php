<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Lokasi project Laravel
|--------------------------------------------------------------------------
| public_html bisa terpisah dari project. Atur di laravel-path.php
| (lihat laravel-path.example.php).
*/
$laravelPath = require __DIR__.'/laravel-path.php';
if (! is_string($laravelPath) || $laravelPath === '') {
    $laravelPath = dirname(__DIR__); // default: sibling structure public/../
}
$laravelPath = rtrim(str_replace('\\', '/', $laravelPath), '/');

if (! is_file($laravelPath.'/vendor/autoload.php')) {
    http_response_code(500);
    echo 'Laravel project tidak ditemukan. Edit <code>laravel-path.php</code> di public_html supaya mengarah ke folder project (yang berisi vendor/). Path sekarang: '.htmlspecialchars($laravelPath);
    exit;
}

if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelPath.'/vendor/autoload.php';

$app = require_once $laravelPath.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
