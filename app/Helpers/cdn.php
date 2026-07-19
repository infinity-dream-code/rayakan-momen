<?php

if (! function_exists('cdn_image')) {
    /**
     * Build optimized Cloudinary URL for landing assets.
     * Example transform: f_auto,q_auto:eco,w_1600,c_limit
     */
    function cdn_image(string $key, string $transform = 'f_auto,q_auto:eco,w_1600,c_limit'): string
    {
        $cloud = config('undangan.cloudinary.cloud', 'dxzgu46tz');
        $path = config('undangan.cloudinary.images.'.$key);

        if (! $path) {
            return '';
        }

        return 'https://res.cloudinary.com/'.$cloud.'/image/upload/'.$transform.'/'.$path;
    }
}

if (! function_exists('cdn_srcset')) {
    /**
     * Responsive srcset for Cloudinary images (mobile-first widths).
     *
     * @param  array<int>  $widths
     */
    function cdn_srcset(string $key, array $widths = [640, 960, 1280, 1920], string $extra = 'f_auto,q_auto:eco,c_limit'): string
    {
        $parts = [];

        foreach ($widths as $w) {
            $parts[] = cdn_image($key, $extra.',w_'.$w).' '.$w.'w';
        }

        return implode(', ', $parts);
    }
}
