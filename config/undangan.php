<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client invitation lifecycle
    |--------------------------------------------------------------------------
    | After expire_months the public page becomes inaccessible (data kept).
    | After purge_months admins may bulk-delete expired records.
    */
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

];
