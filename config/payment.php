<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Gateway yang dipakai secara default saat membuat tagihan pembayaran.
    | Nilai "manual" berarti belum terhubung ke provider manapun (pembayaran
    | ditandai pending dan dikonfirmasi manual oleh admin).
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'manual'),

    'currency' => env('PAYMENT_CURRENCY', 'IDR'),

    /*
    |--------------------------------------------------------------------------
    | Informasi Merchant
    |--------------------------------------------------------------------------
    */

    'merchant' => [
        'name' => env('PAYMENT_MERCHANT_NAME', 'Tapak Suci Sport Academy'),

        // Payload QRIS statis milik merchant (opsional). Jika kosong, sistem
        // akan membuat payload dinamis sederhana sebagai placeholder.
        'qris_payload' => env('PAYMENT_MERCHANT_QRIS_PAYLOAD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Driver Gateway
    |--------------------------------------------------------------------------
    |
    | Tambahkan kredensial provider di sini. Driver baru cukup didaftarkan
    | pada PaymentGatewayManager.
    |
    */

    'gateways' => [

        'manual' => [
            'driver' => 'manual',
        ],

        'midtrans' => [
            'driver' => 'midtrans',
            'server_key' => env('MIDTRANS_SERVER_KEY'),
            'client_key' => env('MIDTRANS_CLIENT_KEY'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        ],

        'xendit' => [
            'driver' => 'xendit',
            'secret_key' => env('XENDIT_SECRET_KEY'),
            'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
        ],

    ],

];
