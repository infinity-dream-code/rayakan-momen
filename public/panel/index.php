<?php

/**
 * Entry /panel/ → dashboard Laravel (hindari "Index of /panel/").
 */
require __DIR__.'/../bootstrap-path.php';

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/panel', PHP_URL_PATH) ?: '/panel';
$uriPath = rawurldecode($uriPath);

// /panel atau /panel/ → dashboard
if ($uriPath === '/panel' || $uriPath === '/panel/') {
    rayakan_handle('/panel', __DIR__);
    exit;
}

// /panel/xxx → teruskan path lengkap ke Laravel
if (str_starts_with($uriPath, '/panel/')) {
    rayakan_handle(rtrim($uriPath, '/') ?: '/panel', __DIR__);
    exit;
}

rayakan_handle('/panel', __DIR__);
