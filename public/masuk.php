<?php

/**
 * Pintu masuk darurat jika rewrite URL bermasalah.
 * Buka: https://rayakanmomen.com/masuk.php
 */
$target = '/index.php/panel/login';

if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $scheme = 'https';
} elseif (! empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
} else {
    $scheme = 'http';
}

$host = $_SERVER['HTTP_HOST'] ?? 'rayakanmomen.com';

header('Location: '.$scheme.'://'.$host.$target, true, 302);
exit;
