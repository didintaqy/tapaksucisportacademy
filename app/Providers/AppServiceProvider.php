<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class);

        $this->app->bind(
            PaymentGateway::class,
            fn ($app) => $app->make(PaymentGatewayManager::class)->driver(),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $seo = [
                'home' => ['title' => 'Tapak Suci Sport Academy', 'description' => 'Website resmi Tapak Suci Sport Academy. Profil, galeri, blog, pendaftaran siswa, dan portal pembayaran.', 'keywords' => 'pencak silat, tapak suci, latihan silat, pendaftaran silat, akademi bela diri'],
                'about' => ['title' => 'Tentang Kami | Tapak Suci Sport Academy', 'description' => 'Sejarah, visi, misi, dan struktur organisasi Tapak Suci Sport Academy.', 'keywords' => 'tentang tapak suci, sejarah silat, visi misi'],
                'program' => ['title' => 'Program Latihan | Tapak Suci Sport Academy', 'description' => 'Daftar program latihan pencak silat Tapak Suci untuk berbagai usia dan tingkat.', 'keywords' => 'program silat, kelas silat, latihan anak, latihan dewasa'],
                'gallery' => ['title' => 'Galeri Kegiatan | Tapak Suci Sport Academy', 'description' => 'Foto-foto kegiatan latihan, kejuaraan, ujian kenaikan tingkat, dan pembinaan mental.', 'keywords' => 'galeri silat, foto latihan, foto kejuaraan'],
                'blog' => ['title' => 'Artikel & Berita | Tapak Suci Sport Academy', 'description' => 'Artikel tips, berita kegiatan, dan pengumuman resmi dari Tapak Suci Sport Academy.', 'keywords' => 'artikel silat, berita silat, tips pencak silat'],
                'contact-us' => ['title' => 'Hubungi Kami | Tapak Suci Sport Academy', 'description' => 'Kirim pesan, lihat lokasi latihan, dan hubungi admin Tapak Suci Sport Academy.', 'keywords' => 'kontak silat, alamat latihan, nomor telepon silat'],
                'testimonials' => ['title' => 'Testimoni | Tapak Suci Sport Academy', 'description' => 'Testimoni orang tua siswa, alumni, dan peserta latihan Tapak Suci Sport Academy.', 'keywords' => 'testimoni silat, review silat, pengalaman bela diri'],
                'faqs' => ['title' => 'FAQ | Tapak Suci Sport Academy', 'description' => 'Pertanyaan yang sering diajukan tentang pendaftaran, latihan, biaya, dan jadwal.', 'keywords' => 'faq silat, pertanyaan umum pencak silat'],
                'schedules' => ['title' => 'Jadwal Latihan | Tapak Suci Sport Academy', 'description' => 'Jadwal latihan pencak silat per cabang dan kelas di Tapak Suci Sport Academy.', 'keywords' => 'jadwal silat, jadwal latihan, cabang silat'],
                'registration' => ['title' => 'Pendaftaran Siswa | Tapak Suci Sport Academy', 'description' => 'Daftarkan putra/putri Anda di Tapak Suci Sport Academy. Formulir pendaftaran online cepat dan mudah.', 'keywords' => 'daftar silat, pendaftaran pencak silat, biaya silat'],
            ];

            $currentRoute = request()->route()?->getName() ?? 'home';
            $meta = $seo[$currentRoute] ?? $seo['home'];

            $view->with([
                'seoTitle' => $meta['title'],
                'seoDescription' => $meta['description'],
                'seoKeywords' => $meta['keywords'],
                'seoImage' => asset('images/og-default.jpg'),
            ]);
        });
    }
}
