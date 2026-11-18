<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Contracts\WhatsAppGateway;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\SiteSettings;
use App\Services\WhatsApp\WhatsAppManager;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

        $this->app->singleton(WhatsAppManager::class);

        $this->app->bind(
            WhatsAppGateway::class,
            fn ($app) => $app->make(WhatsAppManager::class)->driver(),
        );
    }

    /**
     * URL untuk preview file lama. Mendukung path disk publik, aset tema
     * (`images/...`), URL eksternal, dan path absolut.
     */
    private static function fileUploadUrl(string $file, string $diskUrl): string
    {
        if (Str::startsWith($file, ['http://', 'https://', '//'])) {
            return $file;
        }

        if (Str::startsWith($file, 'images/')) {
            return asset($file);
        }

        if (Str::startsWith($file, '/')) {
            return url($file);
        }

        return rtrim($diskUrl, '/').'/'.ltrim($file, '/');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pastikan URL berkas publik mengikuti skema request. Tanpa ini,
        // Storage::url() memakai APP_URL dan bisa menghasilkan http:// pada
        // halaman HTTPS (mixed content) sehingga gambar upload tidak tampil.
        if (! app()->runningInConsole() && request()->isSecure()) {
            config(['filesystems.disks.public.url' => rtrim(secure_url('/storage'), '/')]);
        }

        // FileUpload default: jangan buang path lama hanya karena berkasnya
        // tidak ada di disk publik. Filament secara default menghapus path
        // seperti `images/...` (aset tema) saat form edit dimuat, lalu
        // menyimpan NULL ketika disimpan — foto lama hilang.
        FileUpload::configureUsing(function (FileUpload $component): void {
            $component->fetchFileInformation(false);

            $component->getUploadedFileUsing(function (FileUpload $component, string $file): ?array {
                if (blank($file)) {
                    return null;
                }

                $disk = $component->getDisk();
                $exists = false;

                if (! Str::startsWith($file, ['http://', 'https://', '//'])) {
                    try {
                        $exists = $disk->exists($file);
                    } catch (\Throwable) {
                        $exists = false;
                    }
                }

                return [
                    'name' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                    'size' => $exists ? (int) $disk->size($file) : 0,
                    'type' => $exists ? $disk->mimeType($file) : null,
                    'url' => self::fileUploadUrl($file, $disk->url('/')),
                ];
            });
        });

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
