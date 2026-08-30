<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client invitation lifecycle
    |--------------------------------------------------------------------------
    | Nonaktif hanya manual (toggle di daftar undangan).
    | purge_days = undangan nonaktif lama (access_state expired) bisa dihapus massal.
    */
    'expire_days' => null, // legacy — tidak dipakai (nonaktif manual)
    'purge_days' => (int) env('UNDANGAN_PURGE_DAYS', 180),

    // Backward-compatible aliases — prefer *_days di atas
    'expire_months' => null,
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
    // 1280px cukup untuk undangan; lebih kecil = update foto jauh lebih cepat di shared hosting
    'upload_max_dimension' => (int) env('UPLOAD_MAX_DIMENSION', 1280),

    /*
    |--------------------------------------------------------------------------
    | Public brand
    |--------------------------------------------------------------------------
    */
    'brand' => env('APP_BRAND', 'Rayakan Momen'),
    'public_domain' => env('APP_DOMAIN', 'rayakanmomen.com'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp kontak bisnis
    |--------------------------------------------------------------------------
    */
    'wa_number' => env('WA_NUMBER', '6285777433886'),
    'wa_display' => env('WA_DISPLAY', '0857-7743-3886'),

    /*
    |--------------------------------------------------------------------------
    | RSVP Dashboard (link share tanpa login)
    |--------------------------------------------------------------------------
    | Token URL = enkripsi slug. Key jangan diganti kalau link lama masih dipakai.
    */
    'rsvp_dashboard_key' => env('RSVP_DASHBOARD_KEY', 'Rama Sat119'),

    /*
    |--------------------------------------------------------------------------
    | Landing images (Cloudinary CDN)
    |--------------------------------------------------------------------------
    */
    'cloudinary' => [
        'cloud' => env('CLOUDINARY_CLOUD', 'dxzgu46tz'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
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
