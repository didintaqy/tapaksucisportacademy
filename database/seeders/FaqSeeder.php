<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Pendaftaran',
                'question' => 'Bagaimana cara mendaftar sebagai siswa baru?',
                'answer' => 'Isi formulir pendaftaran online di website ini, lalu admin akan menghubungi Anda untuk verifikasi data dan penjadwalan latihan percobaan.',
                'sort_order' => 1,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Apakah ada biaya pendaftaran?',
                'answer' => 'Ya, ada biaya administrasi pendaftaran dan iuran bulanan. Rincian biaya akan dijelaskan oleh admin saat proses verifikasi.',
                'sort_order' => 2,
            ],
            [
                'category' => 'Latihan',
                'question' => 'Apakah pemula tanpa pengalaman silat bisa ikut?',
                'answer' => 'Tentu. Program dimulai dari dasar, termasuk sikap dasar, kuda-kuda, dan gerakan dasar Tapak Suci. Pelatih akan menyesuaikan materi dengan kemampuan siswa.',
                'sort_order' => 3,
            ],
            [
                'category' => 'Latihan',
                'question' => 'Apa yang perlu dibawa saat latihan pertama?',
                'answer' => 'Cukup gunakan pakaian olahraga yang nyaman dan membawa air minum. Seragam latihan dapat dipakai setelah siswa resmi terdaftar.',
                'sort_order' => 4,
            ],
            [
                'category' => 'Latihan',
                'question' => 'Berapa kali latihan dalam seminggu?',
                'answer' => 'Jadwal latihan rutin umumnya dua kali seminggu. Silakan lihat halaman jadwal latihan untuk waktu dan lokasi terbaru.',
                'sort_order' => 5,
            ],
            [
                'category' => 'Umum',
                'question' => 'Apakah orang tua boleh mendampingi saat latihan?',
                'answer' => 'Boleh. Orang tua dapat menunggu di area yang tersedia dan berkoordinasi dengan pelatih mengenai perkembangan anak.',
                'sort_order' => 6,
            ],
            [
                'category' => 'Umum',
                'question' => 'Bagaimana cara menghubungi admin?',
                'answer' => 'Anda dapat menghubungi kami melalui halaman Kontak Kami, telepon, atau email yang tertera di website.',
                'sort_order' => 7,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                [...$faq, 'is_published' => true],
            );
        }
    }
}
