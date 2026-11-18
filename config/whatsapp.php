<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver WhatsApp
    |--------------------------------------------------------------------------
    |
    | "log" hanya menulis pesan ke log dan `notification_logs` (default, aman
    | tanpa kredensial). Tambahkan driver provider lain di `drivers` dan
    | daftarkan pada WhatsAppManager.
    |
    */

    'default' => env('WHATSAPP_DRIVER', 'log'),

    'from' => env('WHATSAPP_FROM'),

    'drivers' => [

        'log' => [
            'driver' => 'log',
        ],

        'fonnte' => [
            'driver' => 'fonnte',
            'token' => env('FONNTE_TOKEN'),
        ],

        'wablas' => [
            'driver' => 'wablas',
            'token' => env('WABLAS_TOKEN'),
            'secret' => env('WABLAS_SECRET'),
        ],

    ],

];
