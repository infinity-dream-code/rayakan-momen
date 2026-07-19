<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client invitation lifecycle
    |--------------------------------------------------------------------------
    | expire_days  = otomatis jadi expired (tidak bisa diakses publik), data tetap ada
    | purge_days   = setelah ini baru muncul tombol hapus manual di admin
    */
    'expire_days' => (int) env('UNDANGAN_EXPIRE_DAYS', 90),
    'purge_days' => (int) env('UNDANGAN_PURGE_DAYS', 180),

    // Backward-compatible aliases (bulan ≈ 30 hari) — prefer *_days di atas
    'expire_months' => (int) env('UNDANGAN_EXPIRE_MONTHS', 3),
    'purge_months' => (int) env('UNDANGAN_PURGE_MONTHS', 6),

    /*
    |--------------------------------------------------------------------------
    | Public HTML cache (web client /{slug}, not landing)
    |--------------------------------------------------------------------------
    */
    'client_cache_seconds' => (int) env('UNDANGAN_CLIENT_CACHE', 300),

    'cache_key_prefix' => 'undangan:html:',

    /*
    |--------------------------------------------------------------------------
    | Secure image uploads
    |--------------------------------------------------------------------------
    | Only jpg/jpeg/png. Double extensions rejected. Auto-compress to max_kb.
    */
    'upload_max_kb' => (int) env('UPLOAD_MAX_KB', 500),
    'upload_max_dimension' => (int) env('UPLOAD_MAX_DIMENSION', 1920),

    /*
    |--------------------------------------------------------------------------
    | Public brand
    |--------------------------------------------------------------------------
    */
    'brand' => env('APP_BRAND', 'Rayakan Momen'),
    'public_domain' => env('APP_DOMAIN', 'rayakanmomen.com'),

    /*
    |--------------------------------------------------------------------------
    | Landing images (Cloudinary CDN)
    |--------------------------------------------------------------------------
    */
    'cloudinary' => [
        'cloud' => env('CLOUDINARY_CLOUD', 'dxzgu46tz'),
        'images' => [
            'hero_wedding' => 'v1784457031/hero-wedding_vacgsf.jpg',
            'hero_couple' => 'v1784457040/hero-couple_dgocbe.jpg',
            'hero_ultah' => 'v1784457036/hero-ultah_v2rign.jpg',
            'cta' => 'v1784457068/cta_ta99hf.jpg',
            'cat_wedding' => 'v1784457026/cat-wedding_npoooi.jpg',
            'cat_ultah' => 'v1784457026/cat-ultah_ag17mg.jpg',
            'cat_couple' => 'v1784457027/cat-couple_y3ofp8.jpg',
        ],
    ],

];
