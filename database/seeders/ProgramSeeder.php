<?php

namespace Database\Seeders;

use App\Enums\ProgramLevel;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $allBranches = ['Cabang Pusat', 'Cabang Batam Kota'];

        $programs = [
            [
                'name' => 'Kelas Dasar Tapak Suci',
                'short_description' => 'Pengenalan gerak dasar, sikap, dan nilai disiplin untuk pesilat pemula.',
                'description' => '<p>Kelas Dasar Tapak Suci dirancang untuk peserta yang baru mengenal pencak silat. Materi meliputi sikap dasar, kuda-kuda, langkah, pukulan, tangkisan, serta pembentukan disiplin dan rasa hormat.</p><h2>Materi Latihan</h2><ul><li>Kuda-kuda dan sikap pasang</li><li>Pukulan, tangkisan, dan elakan dasar</li><li>Pemanasan dan pendinginan yang benar</li><li>Etika dan adab pesilat</li></ul><p>Setiap sesi dipandu pelatih bersertifikat dengan pendekatan yang aman dan menyenangkan.</p>',
                'cover_image' => 'images/programs-item-image-1.png',
                'age_min' => 7,
                'age_max' => 12,
                'level' => ProgramLevel::Beginner,
                'session_duration_minutes' => 75,
                'sessions_per_week' => 2,
                'monthly_fee' => 150000,
                'branches' => $allBranches,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Kelas Remaja',
                'short_description' => 'Latihan teknik dan kebugaran untuk remaja dengan pendekatan kelompok.',
                'description' => '<p>Kelas Remaja memadukan teknik pencak silat dengan latihan kebugaran dan pengembangan karakter. Cocok untuk remaja yang ingin meningkatkan kepercayaan diri, kebugaran, dan keterampilan bela diri.</p><h2>Fokus Latihan</h2><ul><li>Teknik serang-bela yang lebih dinamis</li><li>Latihan fisik dan kelenturan</li><li>Sparring terkontrol</li><li>Kerja sama tim dan sportivitas</li></ul>',
                'cover_image' => 'images/programs-item-image-2.png',
                'age_min' => 13,
                'age_max' => 17,
                'level' => ProgramLevel::Beginner,
                'session_duration_minutes' => 90,
                'sessions_per_week' => 2,
                'monthly_fee' => 175000,
                'branches' => $allBranches,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Kelas Dewasa',
                'short_description' => 'Latihan bela diri dan kebugaran untuk dewasa, cocok untuk pemula.',
                'description' => '<p>Kelas Dewasa terbuka bagi peserta yang ingin belajar pencak silat sekaligus menjaga kebugaran. Materi disesuaikan dengan kemampuan masing-masing peserta.</p><h2>Manfaat</h2><ul><li>Meningkatkan stamina dan kelenturan</li><li>Melatih refleks dan keseimbangan</li><li>Mengurangi stres setelah bekerja</li><li>Belajar pertahanan diri yang praktis</li></ul>',
                'cover_image' => 'images/programs-item-image-3.png',
                'age_min' => 18,
                'age_max' => 45,
                'level' => ProgramLevel::All,
                'session_duration_minutes' => 90,
                'sessions_per_week' => 2,
                'monthly_fee' => 200000,
                'branches' => $allBranches,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Latihan Prestasi & Kejuaraan',
                'short_description' => 'Program intensif bagi pesilat yang ingin berlaga di kejuaraan.',
                'description' => '<p>Program Prestasi disiapkan untuk pesilat yang serius menekuni jalur kompetisi. Latihan mencakup teknik tanding, strategi, kondisi fisik, dan simulasi pertandingan.</p><h2>Cakupan Program</h2><ul><li>Program latihan periodisasi</li><li>Analisis teknik dan strategi tanding</li><li>Simulasi pertandingan dan evaluasi</li><li>Pendampingan menuju kejuaraan</li></ul>',
                'cover_image' => 'images/programs-item-image-4.png',
                'age_min' => 12,
                'age_max' => 30,
                'level' => ProgramLevel::Advanced,
                'session_duration_minutes' => 120,
                'sessions_per_week' => 4,
                'monthly_fee' => 300000,
                'branches' => ['Cabang Pusat'],
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Seni Tunggal Baku',
                'short_description' => 'Pendalaman jurus seni tunggal baku Tapak Suci.',
                'description' => '<p>Kelas Seni Tunggal Baku membahas penghayatan dan penampilan jurus baku Tapak Suci. Peserta dilatih untuk tampil percaya diri, presisi, dan penuh penghayatan.</p><h2>Materi</h2><ul><li>Jurus seni tunggal baku</li><li>Penjiwaan dan irama gerak</li><li>Penilaian penampilan seni</li><li>Persiapan lomba seni</li></ul>',
                'cover_image' => 'images/programs-item-image-5.png',
                'age_min' => 10,
                'age_max' => 35,
                'level' => ProgramLevel::Intermediate,
                'session_duration_minutes' => 90,
                'sessions_per_week' => 2,
                'monthly_fee' => 200000,
                'branches' => $allBranches,
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pertahanan Diri Praktis',
                'short_description' => 'Teknik bela diri praktis untuk situasi sehari-hari.',
                'description' => '<p>Program ini menekankan teknik pertahanan diri yang mudah diingat dan diterapkan dalam situasi nyata. Cocok untuk dewasa yang ingin merasa lebih aman.</p><h2>Yang Dipelajari</h2><ul><li>Membaca situasi dan menjaga jarak</li><li>Lepas dari pegangan</li><li>Teknik menghindar dan melindungi diri</li><li>Pengambilan keputusan saat darurat</li></ul>',
                'cover_image' => 'images/programs-item-image-6.png',
                'age_min' => 18,
                'age_max' => 50,
                'level' => ProgramLevel::Beginner,
                'session_duration_minutes' => 75,
                'sessions_per_week' => 1,
                'monthly_fee' => 175000,
                'branches' => ['Cabang Batam Kota'],
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Kelas Usia Dini',
                'short_description' => 'Pengenalan pencak silat melalui permainan dan gerak dasar.',
                'description' => '<p>Kelas Usia Dini mengenalkan pencak silat lewat aktivitas yang menyenangkan. Fokus pada koordinasi motorik, kedisiplinan, dan keberanian anak.</p><h2>Pendekatan</h2><ul><li>Permainan gerak dan koordinasi</li><li>Latihan keseimbangan dan kelincahan</li><li>Pembiasaan disiplin dan sopan santun</li><li>Penilaian perkembangan anak</li></ul>',
                'cover_image' => 'images/programs-item-image-7.png',
                'age_min' => 4,
                'age_max' => 6,
                'level' => ProgramLevel::Beginner,
                'session_duration_minutes' => 60,
                'sessions_per_week' => 2,
                'monthly_fee' => 125000,
                'branches' => ['Cabang Pusat'],
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Workshop & Pelatihan Pelatih',
                'short_description' => 'Pelatihan berkala untuk pelatih dan pengurus dojo.',
                'description' => '<p>Workshop ini ditujukan bagi pelatih dan pengurus yang ingin meningkatkan kualitas pembinaan. Materi mencakup metodologi latihan, keselamatan, dan manajemen dojo.</p><h2>Topik Workshop</h2><ul><li>Metodologi latihan usia dini dan remaja</li><li>Pencegahan dan penanganan cedera</li><li>Penyusunan program latihan</li><li>Manajemen kegiatan dan kejuaraan</li></ul>',
                'cover_image' => 'images/programs-item-image-8.png',
                'age_min' => 17,
                'age_max' => null,
                'level' => ProgramLevel::Advanced,
                'session_duration_minutes' => 180,
                'sessions_per_week' => 1,
                'monthly_fee' => null,
                'branches' => $allBranches,
                'is_featured' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['slug' => Str::slug($program['name'])],
                array_merge($program, [
                    'is_active' => true,
                    'meta_title' => $program['name'].' — Tapak Suci Sport Academy',
                    'meta_description' => $program['short_description'],
                ]),
            );
        }

        $this->command->info('Program latihan siap ('.count($programs).' program).');
    }
}
