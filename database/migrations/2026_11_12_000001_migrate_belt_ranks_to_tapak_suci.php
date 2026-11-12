<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Map sabuk lama (enum lama) ke tingkatan Tapak Suci baru.
     * Hanya baris dengan nilai lama yang diubah; data yang sudah memakai
     * tingkatan baru atau nilai tidak dikenal dibiarkan (aturan: jangan
     * merubah data yang sudah benar).
     */
    public function up(): void
    {
        $map = [
            'putih' => 'siswa_dasar',
            'kuning' => 'siswa_melati_1',
            'hijau' => 'siswa_melati_2',
            'biru' => 'siswa_melati_3',
            'merah' => 'siswa_melati_4',
            'coklat' => 'kader_dasar',
            'hitam_1' => 'kader_muda',
            'hitam_2' => 'kader_madya',
            'hitam_3' => 'kader_kepala',
            'hitam_4' => 'kader_utama',
            'hitam_5' => 'pendekar_muda',
        ];

        foreach ($map as $old => $new) {
            DB::table('students')->where('belt_level', $old)->update(['belt_level' => $new]);
            DB::table('belt_promotions')->where('current_belt', $old)->update(['current_belt' => $new]);
            DB::table('belt_promotions')->where('target_belt', $old)->update(['target_belt' => $new]);
        }
    }

    public function down(): void
    {
        // Tidak dapat dipulihkan secara otomatis; mapping bersifat satu arah.
    }
};
