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

/*
| PHP built-in server + folder public/panel/: SCRIPT_NAME jadi /panel/index.php
| untuk URI /panel/xxx, sehingga path Laravel salah (jadi "undangan" saja → 404).
| Paksa entry utama agar local (artisan serve) dan cPanel konsisten.
*/
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
if (str_ends_with($scriptName, '/panel/index.php')) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__.DIRECTORY_SEPARATOR.'index.php';
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
