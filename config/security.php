<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sumber Daya Eksternal untuk Content-Security-Policy
    |--------------------------------------------------------------------------
    |
    | Isi hanya host yang benar-benar dipakai. Nilai ini digunakan middleware
    | App\Http\Middleware\SecurityHeaders.
    |
    */

    'script_sources' => array_values(array_filter([
        'https://www.googletagmanager.com',
        'https://analytics.tapaksucisportacademy.com',
    ])),

    'style_sources' => [],

    'font_sources' => [],

    'img_sources' => [
        'https://www.google-analytics.com',
        'https://*.googleusercontent.com',
    ],

    'connect_sources' => [
        'https://www.google-analytics.com',
        'https://analytics.tapaksucisportacademy.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Frame yang Diizinkan
    |--------------------------------------------------------------------------
    |
    | Peta Google Maps pada halaman kontak dirender dalam iframe.
    |
    */

    'frame_sources' => [
        'https://www.google.com',
        'https://maps.google.com',
    ],

];
