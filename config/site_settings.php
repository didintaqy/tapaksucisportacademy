<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Retensi Karantina Gambar Pengaturan Situs
    |--------------------------------------------------------------------------
    |
    | Saat admin mengganti gambar di Pengaturan Situs, berkas lama tidak
    | langsung dihapus melainkan dipindahkan ke karantina agar bisa
    | dipulihkan (php artisan site-settings:prune-media --restore=...).
    |
    | Nilai ini menentukan berapa lama batch karantina disimpan sebelum
    | dihapus permanen oleh scheduler harian. Isi 0 untuk menyimpan selamanya
    | (tidak disarankan pada disk kecil).
    |
    */

    'trash_retention_days' => (int) env('SITE_MEDIA_TRASH_RETENTION_DAYS', 30),

];
