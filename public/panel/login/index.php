<?php

/**
 * Entry fisik agar /panel/login/ bisa dibuka tanpa rewrite LiteSpeed.
 * Upload folder: public/panel/login/index.php
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$publicPath = dirname(__DIR__, 2); // public/
// __DIR__ = public/panel/login → dirname 1 = public/panel, dirname 2 = public

if (file_exists($maintenance = $publicPath.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require $publicPath.'/../vendor/autoload.php';

$app = require_once $publicPath.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = '/panel/login';

// Ambil query string jika ada
$query = $_SERVER['QUERY_STRING'] ?? '';
if ($query !== '') {
    $uri .= '?'.$query;
}

$request = Request::create(
    $uri,
    $method,
    $method === 'GET' ? [] : $_POST,
    $_COOKIE,
    $_FILES,
    array_merge($_SERVER, [
        'SCRIPT_FILENAME' => $publicPath.'/index.php',
        'SCRIPT_NAME' => '/index.php',
        'PHP_SELF' => '/index.php/panel/login',
        'REQUEST_URI' => '/index.php/panel/login'.($query !== '' ? '?'.$query : ''),
    ]),
    $method === 'POST' ? file_get_contents('php://input') : null
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
