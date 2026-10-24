<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\SiteSettings;
use Illuminate\Support\Facades\View;
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
        // Semua view publik mendapat data identitas, kontak, dan pengaturan situs.
        View::composer('*', function ($view): void {
            $view->with('site', app(SiteSettings::class)->viewData());
        });

        // SEO default berasal dari pengaturan situs, tetapi nilai dari controller
        // (mis. judul detail program/artikel) selalu diutamakan.
        View::composer('layout.master', function ($view): void {
            $settings = app(SiteSettings::class);
            $site = $view->getData()['site'] ?? $settings->viewData();

            $name = $site['identity']['name'] ?? 'Tapak Suci Sport Academy';
            $defaultDescription = $site['seo']['description'] ?? '';
            $defaultKeywords = $site['seo']['keywords'] ?? '';
            $defaultImage = $site['assets']['og_image'] ?? null;

            $labels = [
                'about' => 'Tentang Kami',
                'program' => 'Program Latihan',
                'gallery' => 'Galeri Kegiatan',
                'blog' => 'Artikel & Berita',
                'contact-us' => 'Hubungi Kami',
                'testimonials' => 'Testimoni',
                'faqs' => 'Pertanyaan Umum',
                'schedules' => 'Jadwal Latihan',
                'calendar' => 'Kalender Kegiatan',
                'registration' => 'Pendaftaran Siswa',
                'payments.portal' => 'Portal Pembayaran',
                'payments.portal.bills' => 'Tagihan Pembayaran',
                'payments.portal.checkout.show' => 'Pembayaran',
                'payments.portal.receipt' => 'Kwitansi Pembayaran',
            ];

            $route = request()->route()?->getName() ?? 'home';
            $label = $labels[$route] ?? null;

            $metaTitle = $route === 'home'
                ? ($site['seo']['title'] ?? $name)
                : ($label ? "{$label} — {$name}" : ($site['seo']['title'] ?? $name));

            $data = $view->getData();

            $view->with([
                'seoTitle' => filled($data['seoTitle'] ?? null) ? $data['seoTitle'] : $metaTitle,
                'seoDescription' => filled($data['seoDescription'] ?? null) ? $data['seoDescription'] : $defaultDescription,
                'seoKeywords' => filled($data['seoKeywords'] ?? null) ? $data['seoKeywords'] : $defaultKeywords,
                'seoImage' => filled($data['seoImage'] ?? null) ? $data['seoImage'] : $defaultImage,
                'seoSiteName' => $name,
            ]);
        });
    }
}
