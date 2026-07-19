<?php

/**
 * Shared bootstrap untuk entry cadangan (masuk/panel) saat public terpisah.
 */
function rayakan_laravel_path(?string $fromDir = null): string
{
    $fromDir = $fromDir ?: __DIR__;
    $candidates = [
        $fromDir.'/laravel-path.php',
        $fromDir.'/../laravel-path.php',
        $fromDir.'/../../laravel-path.php',
    ];

    $configFile = null;
    foreach ($candidates as $file) {
        $real = realpath($file);
        if ($real && is_file($real)) {
            $configFile = $real;
            break;
        }
    }

    $laravelPath = null;
    if ($configFile) {
        $laravelPath = require $configFile;
        $publicRoot = dirname($configFile);
    } else {
        $publicRoot = $fromDir;
    }

    if (! is_string($laravelPath) || $laravelPath === '') {
        $laravelPath = dirname($publicRoot);
    }

    return rtrim(str_replace('\\', '/', $laravelPath), '/');
}

function rayakan_public_path(?string $fromDir = null): string
{
    $fromDir = $fromDir ?: __DIR__;
    $candidates = [
        $fromDir.'/laravel-path.php',
        $fromDir.'/../laravel-path.php',
        $fromDir.'/../../laravel-path.php',
    ];
    foreach ($candidates as $file) {
        $real = realpath($file);
        if ($real && is_file($real)) {
            return str_replace('\\', '/', dirname($real));
        }
    }

    return str_replace('\\', '/', $fromDir);
}

function rayakan_handle(string $uri, ?string $fromDir = null): void
{
    $laravelPath = rayakan_laravel_path($fromDir);
    $publicPath = rayakan_public_path($fromDir);

    if (! is_file($laravelPath.'/vendor/autoload.php')) {
        http_response_code(500);
        echo 'Laravel project tidak ditemukan. Edit laravel-path.php di public_html agar mengarah ke folder project (yang berisi vendor/). Path sekarang: '.htmlspecialchars($laravelPath);
        exit;
    }

    if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require $laravelPath.'/vendor/autoload.php';

    $app = require_once $laravelPath.'/bootstrap/app.php';

    /** @var \Illuminate\Contracts\Http\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $query = $_SERVER['QUERY_STRING'] ?? '';
    $path = str_starts_with($uri, '/') ? $uri : '/'.$uri;
    $fullUri = $path.($query !== '' ? '?'.$query : '');

    $request = Illuminate\Http\Request::create(
        $fullUri,
        $method,
        $method === 'GET' ? [] : $_POST,
        $_COOKIE,
        $_FILES,
        array_merge($_SERVER, [
            'SCRIPT_FILENAME' => $publicPath.'/index.php',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php'.$path,
            'REQUEST_URI' => '/index.php'.$path.($query !== '' ? '?'.$query : ''),
        ]),
        in_array($method, ['POST', 'PUT', 'PATCH'], true) ? file_get_contents('php://input') : null
    );

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
}
