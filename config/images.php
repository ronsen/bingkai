<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Upload and Processing Configuration
    |--------------------------------------------------------------------------
    */

    'disk' => env('IMAGE_STORAGE_DISK', 'public'),

    'max_dimension' => (int) env('IMAGE_MAX_DIMENSION', 1920),

    'quality' => (int) env('IMAGE_QUALITY', 85),

    'show_gps_default' => (bool) env('IMAGE_SHOW_GPS_DEFAULT', false),

    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],

    'max_upload_size' => (int) env('IMAGE_MAX_UPLOAD_SIZE', 25600), // KB (~25MB)

    'cache_ttl' => (int) env('IMAGE_CACHE_TTL', 3600), // Seconds (1 hour default)
];
