<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        Newsletter::firstOrCreate(
            ['subject' => 'Selamat Datang di Newsletter Tapak Suci'],
            [
                'subject' => 'Selamat Datang di Newsletter Tapak Suci',
                'content' => '<p>Terima kasih telah berlangganan newsletter kami. Kami akan mengirimkan update terbaru tentang kegiatan, jadwal latihan, dan tips pencak silat.</p>',
                'status' => 'sent',
                'sent_at' => now()->subDays(7),
                'recipients_count' => 150,
                'created_by' => $admin?->id,
            ]
        );

        Newsletter::firstOrCreate(
            ['subject' => 'Jadwal Latihan Bulan Depan'],
            [
                'subject' => 'Jadwal Latihan Bulan Depan',
                'content' => '<p>Berikut adalah jadwal latihan untuk bulan depan. Mohon diperhatikan perubahan jam latihan untuk kelas pemula.</p>',
                'status' => 'draft',
                'created_by' => $admin?->id,
            ]
        );
    }
}
