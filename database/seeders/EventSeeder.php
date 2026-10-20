<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $events = [
            // Bulan ini - latihan rutin
            [
                'title' => 'Latihan Rutin Senin',
                'description' => 'Latihan dasar dan teknik untuk semua sabuk',
                'starts_at' => Carbon::now()->startOfMonth()->next(Carbon::MONDAY)->setTime(18, 0),
                'ends_at' => Carbon::now()->startOfMonth()->next(Carbon::MONDAY)->setTime(20, 0),
                'type' => 'latihan',
                'is_public' => true,
            ],
            [
                'title' => 'Latihan Rutin Rabu',
                'description' => 'Latihan fisik dan sparring ringan',
                'starts_at' => Carbon::now()->startOfMonth()->next(Carbon::WEDNESDAY)->setTime(18, 0),
                'ends_at' => Carbon::now()->startOfMonth()->next(Carbon::WEDNESDAY)->setTime(20, 0),
                'type' => 'latihan',
                'is_public' => true,
            ],
            [
                'title' => 'Latihan Rutin Sabtu',
                'description' => 'Latihan intensif dan persiapan UKT',
                'starts_at' => Carbon::now()->startOfMonth()->next(Carbon::SATURDAY)->setTime(8, 0),
                'ends_at' => Carbon::now()->startOfMonth()->next(Carbon::SATURDAY)->setTime(11, 0),
                'type' => 'latihan',
                'is_public' => true,
            ],

            // Bulan ini - kejuaraan
            [
                'title' => 'Kejuaraan Regional Sumatera',
                'description' => 'Kejuaraan pencak silat tingkat regional, kategori anak-anak hingga dewasa',
                'starts_at' => Carbon::now()->startOfMonth()->addDays(15)->setTime(7, 0),
                'ends_at' => Carbon::now()->startOfMonth()->addDays(17)->setTime(17, 0),
                'type' => 'kejuaraan',
                'is_public' => true,
            ],

            // Bulan ini - UKT
            [
                'title' => 'Ujian Kenaikan Tingkat (UKT) Bulanan',
                'description' => 'Ujian kenaikan sabuk untuk siswa yang memenuhi syarat jam terbang',
                'starts_at' => Carbon::now()->startOfMonth()->addDays(20)->setTime(8, 0),
                'ends_at' => Carbon::now()->startOfMonth()->addDays(20)->setTime(14, 0),
                'type' => 'ukt',
                'is_public' => true,
            ],

            // Bulan ini - pengumuman
            [
                'title' => 'Pengumuman Libur Hari Raya',
                'description' => 'Jadwal latihan tidak berlangsung pada tanggal 1-3 bulan depan',
                'starts_at' => Carbon::now()->startOfMonth()->addDays(25)->setTime(0, 0),
                'ends_at' => Carbon::now()->startOfMonth()->addDays(27)->setTime(23, 59),
                'type' => 'pengumuman',
                'is_public' => true,
            ],

            // Bulan depan - latihan
            [
                'title' => 'Latihan Persiapan Kejuaraan Nasional',
                'description' => 'Latihan khusus atlet yang lolos seleksi kejuaraan nasional',
                'starts_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(3)->setTime(17, 0),
                'ends_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(3)->setTime(20, 0),
                'type' => 'latihan',
                'is_public' => true,
            ],

            // Bulan lalu - kejuaraan (sudah lewat)
            [
                'title' => 'Kejuaraan Internal Akademi',
                'description' => 'Kompetisi internal antar cabang untuk mengukur kemampuan siswa',
                'starts_at' => Carbon::now()->subMonth()->startOfMonth()->addDays(10)->setTime(7, 0),
                'ends_at' => Carbon::now()->subMonth()->startOfMonth()->addDays(11)->setTime(16, 0),
                'type' => 'kejuaraan',
                'is_public' => true,
            ],

            // Libur nasional
            [
                'title' => 'Libur Hari Kemerdekaan RI',
                'description' => 'Tidak ada latihan, akademi tutup',
                'starts_at' => Carbon::create(now()->year, 8, 17)->startOfDay(),
                'ends_at' => Carbon::create(now()->year, 8, 17)->endOfDay(),
                'type' => 'libur',
                'is_public' => true,
            ],
            [
                'title' => 'Libur Natal & Tahun Baru',
                'description' => 'Akademi tutup untuk libur panjang',
                'starts_at' => Carbon::create(now()->year, 12, 24)->startOfDay(),
                'ends_at' => Carbon::create(now()->year, 12, 31)->endOfDay(),
                'type' => 'libur',
                'is_public' => true,
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                [
                    'title' => $event['title'],
                    'starts_at' => $event['starts_at'],
                ],
                array_merge($event, ['created_by' => $admin?->id])
            );
        }

        $this->command->info('Event seeder selesai: '.count($events).' event dibuat.');
    }
}
