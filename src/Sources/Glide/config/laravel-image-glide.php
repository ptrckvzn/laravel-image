<?php

return [
    'source' => env('LARAVEL_IMAGE_GLIDE_SOURCE', storage_path('app/public')),
    'source_path_prefix' => env('LARAVEL_IMAGE_GLIDE_SOURCE_PATH_PREFIX', null),
    'cache' => env('LARAVEL_IMAGE_GLIDE_CACHE', storage_path('app')),
    'cache_path_prefix' => env('LARAVEL_IMAGE_GLIDE_CACHE_PATH_PREFIX', 'glide_cache_laravel_image'),
    'base_url' => env('LARAVEL_IMAGE_GLIDE_BASE_URL', config('app.url')),
    'base_path' => env('LARAVEL_IMAGE_GLIDE_BASE_PATH', 'glide'),
    'use_signed_urls' => env('LARAVEL_IMAGE_GLIDE_USE_SIGNED_URLS', false),
    'sign_key' => env('LARAVEL_IMAGE_GLIDE_SIGN_KEY'),
    'driver' => env('LARAVEL_IMAGE_GLIDE_DRIVER', 'gd'),
    'add_params_to_svgs' => false,
    'original_media_for_extensions' => ['svg'],
    'default_params' => [
        'fm' => 'jpg',
        'q' => '80',
        'fit' => 'max',
    ],
//    'lqip_default_params' => [
//        'fm' => 'gif',
//        'blur' => 100,
//        'dpr' => 1,
//    ],
];
