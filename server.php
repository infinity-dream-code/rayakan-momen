<?php

/**
 * Router untuk `php artisan serve`.
 *
 * Folder public/panel/ (entry cPanel) bikin PHP built-in server salah set
 * SCRIPT_NAME=/panel/index.php untuk /panel/xxx → route Laravel 404.
 * Samakan perilaku dengan Apache: semua /panel* lewat panel/index.php.
 */

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'
);

if ($uri === '/panel' || str_starts_with($uri, '/panel/')) {
    require $publicPath.'/panel/index.php';

    return true;
}

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
