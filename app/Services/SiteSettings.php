<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SiteSettings
{
    public const CACHE_KEY = 'site_settings.v1';

    private ?array $memory = null;

    /**
     * Nilai default yang aman dipakai sebelum pengelola mengisi data resmi.
     *
     * Tidak ada alamat, nomor telepon, harga, atau nama pelatih fiktif di sini.
     * Field kontak dibiarkan kosong dan section terkait disembunyikan di frontend.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'identity' => [
                'name' => 'Tapak Suci Sport Academy',
                'short_name' => 'Tapak Suci',
                'tagline' => 'Akademi Pencak Silat Tapak Suci',
                'description' => 'Akademi pencak silat Tapak Suci untuk anak, remaja, dan dewasa. Kami membina disiplin, keberanian, keterampilan, dan karakter melalui latihan yang terarah.',
                'logo' => 'images/logo.svg',
                'favicon' => 'images/favicon.png',
                'page_header_image' => 'images/page-header-bg.jpg',
                'years_experience' => '',
            ],
            'contact' => [
                'address' => '',
                'phone' => '',
                'whatsapp' => '',
                'email' => '',
                'hours' => '',
                'maps_url' => '',
                'maps_embed' => '',
                'image' => 'images/contact-us-img.jpg',
            ],
            'social' => [
                'facebook' => '',
                'instagram' => '',
                'youtube' => '',
                'tiktok' => '',
                'x' => '',
            ],
            'hero' => [
                'eyebrow' => 'Selamat Datang di Tapak Suci Sport Academy',
                'title' => 'Membentuk disiplin, keberanian, dan keterampilan pencak silat',
                'description' => 'Latihan pencak silat Tapak Suci untuk anak, remaja, dan dewasa dengan bimbingan pelatih, program yang terstruktur, dan suasana latihan yang suportif.',
                'primary_label' => 'Daftar Sekarang',
                'secondary_label' => 'Lihat Program',
                'video_url' => 'https://www.youtube.com/watch?v=DqGgYgbCGBo',
                'image' => 'images/hero-bg.jpg',
            ],
            'home' => [
                'about_eyebrow' => 'Tentang Kami',
                'about_title' => 'Tumbuh kuat, disiplin, dan percaya diri bersama Tapak Suci',
                'about_description' => 'Kami percaya bahwa pencak silat bukan hanya tentang gerakan, tetapi juga tentang pembentukan karakter. Setiap latihan dirancang untuk melatih keberanian, rasa hormat, dan ketekunan.',
                'program_heading' => 'Program Latihan',
                'program_description' => 'Pilih program yang paling sesuai dengan usia, tingkat kemampuan, dan tujuan latihan.',
                'why_heading' => 'Mengapa Memilih Kami',
                'why_description' => 'Pendekatan latihan yang terarah, aman, dan tetap menjunjung nilai budaya pencak silat.',
                'why_items' => [
                    [
                        'title' => 'Pembinaan karakter',
                        'description' => 'Latihan disiplin, rasa hormat, dan tanggung jawab menjadi bagian dari setiap sesi.',
                    ],
                    [
                        'title' => 'Latihan terstruktur',
                        'description' => 'Materi disusun bertahap sesuai usia dan kemampuan agar siswa berkembang dengan percaya diri.',
                    ],
                    [
                        'title' => 'Suasana yang suportif',
                        'description' => 'Pelatih mendampingi siswa dan membangun lingkungan latihan yang aman serta menyenangkan.',
                    ],
                ],
                'steps_heading' => 'Cara Memulai',
                'steps_description' => 'Proses bergabung dibuat sederhana agar Anda dapat segera mencoba latihan.',
                'steps' => [
                    [
                        'title' => 'Konsultasi & pendaftaran',
                        'description' => 'Isi formulir pendaftaran atau hubungi admin untuk menanyakan program yang sesuai.',
                    ],
                    [
                        'title' => 'Pilih program',
                        'description' => 'Tentukan program berdasarkan usia, jadwal, dan tujuan latihan.',
                    ],
                    [
                        'title' => 'Mulai latihan',
                        'description' => 'Ikuti jadwal latihan rutin bersama pelatih dan siswa lainnya.',
                    ],
                    [
                        'title' => 'Evaluasi & naik tingkat',
                        'description' => 'Perkembangan siswa dipantau secara berkala dan disiapkan untuk ujian kenaikan tingkat.',
                    ],
                ],
                'team_heading' => 'Pelatih Kami',
                'team_description' => 'Profil pelatih yang mendampingi perjalanan latihan siswa.',
                'features_heading' => 'Keunggulan Latihan',
                'features_description' => 'Hal-hal yang kami jaga dalam setiap proses latihan.',
                'features' => [
                    [
                        'title' => 'Pembinaan menyeluruh',
                        'description' => 'Teknik, kebugaran, dan karakter dibina secara seimbang.',
                        'points' => ['Disiplin & rasa hormat', 'Koordinasi & kebugaran', 'Percaya diri'],
                    ],
                    [
                        'title' => 'Latihan sesuai usia',
                        'description' => 'Materi dan pendekatan disesuaikan dengan kelompok usia peserta.',
                        'points' => ['Kelas anak & remaja', 'Kelas dewasa', 'Jalur prestasi'],
                    ],
                ],
                'pricing_heading' => 'Program & Iuran',
                'pricing_description' => 'Iuran mengikuti program yang dipilih. Detail biaya akan dikonfirmasi admin saat pendaftaran.',
                'pricing_note' => 'Iuran dapat berbeda antarprogram dan dapat berubah sesuai kebijakan akademi.',
                'pricing_benefits' => [
                    'Informasi biaya transparan',
                    'Konsultasi program sebelum mendaftar',
                    'Pendampingan pelatih selama latihan',
                ],
                'testimonials_heading' => 'Testimoni',
                'testimonials_description' => 'Pengalaman dari siswa, orang tua, dan alumni yang telah berlatih bersama kami.',
                'faq_heading' => 'Pertanyaan yang Sering Diajukan',
                'faq_description' => 'Beberapa pertanyaan umum tentang pendaftaran, latihan, dan biaya.',
                'blog_heading' => 'Artikel Terbaru',
                'blog_description' => 'Informasi dan tips seputar latihan pencak silat.',
                'cta_heading' => 'Siap Memulai Latihan?',
                'cta_description' => 'Hubungi admin atau isi formulir pendaftaran untuk mengetahui jadwal dan program yang tersedia.',
                'feature_image' => 'images/cour-feature-image.png',
                'why_image_1' => 'images/why-choose-image-1.jpg',
                'why_image_2' => 'images/why-choose-image-2.jpg',
                'testimonials_background' => 'images/testimonial-bg.jpg',
                'dark_section_background' => 'images/dark-section-bg-shape.png',
                'core_feature_background' => 'images/core-feature-image-bg.png',
            ],
            'about' => [
                'eyebrow' => 'Tentang Kami',
                'title' => 'Tumbuh kuat, disiplin, dan percaya diri bersama Tapak Suci',
                'description' => 'Tapak Suci Sport Academy membina siswa melalui latihan pencak silat yang menekankan keterampilan, disiplin, dan akhlak. Kami terbuka untuk anak, remaja, dan dewasa yang ingin belajar bela diri dengan cara yang terarah.',
                'history' => '',
                'vision' => 'Menjadi akademi pencak silat yang membina pesilat berprestasi, berkarakter, dan menjunjung nilai-nilai Tapak Suci.',
                'mission' => 'Menyelenggarakan latihan yang terstruktur, aman, dan menyenangkan; membina disiplin serta akhlak; serta mendukung pengembangan prestasi dan kesehatan siswa.',
                'values_heading' => 'Nilai yang Kami Bina',
                'values' => [
                    [
                        'title' => 'Disiplin & akhlak',
                        'description' => 'Pesilat belajar menghormati pelatih, sesama siswa, dan aturan latihan.',
                    ],
                    [
                        'title' => 'Keterampilan teknik',
                        'description' => 'Materi diberikan bertahap, mulai dari dasar hingga teknik yang lebih lanjut.',
                    ],
                    [
                        'title' => 'Kesehatan & kebugaran',
                        'description' => 'Latihan fisik mendukung kebugaran, koordinasi, dan ketahanan tubuh.',
                    ],
                ],
                'image' => 'images/about-us-image.jpg',
                'video_image' => 'images/about-us-video-image.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=DqGgYgbCGBo',
            ],
            'registration' => [
                'fee' => '',
                'fee_note' => 'Biaya pendaftaran dan iuran bulanan akan dikonfirmasi admin saat verifikasi.',
                'requirements' => '',
            ],
            'seo' => [
                'title' => 'Tapak Suci Sport Academy — Akademi Pencak Silat',
                'description' => 'Website resmi Tapak Suci Sport Academy. Informasi program latihan pencak silat, pendaftaran, jadwal, dan kegiatan akademi.',
                'keywords' => 'pencak silat, tapak suci, latihan silat, pendaftaran silat, akademi bela diri',
                'image' => 'images/hero-bg.jpg',
            ],
            'footer' => [
                'description' => 'Akademi pencak silat Tapak Suci untuk anak, remaja, dan dewasa. Membina disiplin, keterampilan, dan karakter.',
                'cta_title' => 'Mulai perjalanan latihan Anda',
                'cta_description' => 'Bergabunglah bersama siswa lain dan temukan manfaat latihan pencak silat yang terarah.',
                'cta_label' => 'Daftar Sekarang',
                'copyright' => 'Seluruh hak cipta dilindungi.',
                'background_image' => 'images/footer-bg.jpg',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->memory !== null) {
            return $this->memory;
        }

        $stored = Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => SiteSetting::query()->find(1)?->data ?? [],
        );

        return $this->memory = $this->mergeDefaults(
            is_array($stored) ? $stored : [],
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): void
    {
        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            ['data' => $data],
        );

        $this->flush();

        // Data baru sudah tersimpan sebelum berkas lama dihapus, sehingga
        // kegagalan penghapusan tidak pernah membuat data menunjuk ke
        // gambar yang sudah hilang. Kegagalan filesystem juga tidak boleh
        // menggagalkan penyimpanan pengaturan.
        try {
            app(SiteSettingMedia::class)->prune();
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->flushMemory();
    }

    public function flushMemory(): void
    {
        $this->memory = null;
    }

    /**
     * Data siap pakai untuk view: default + aset + URL turunan.
     *
     * @return array<string, mixed>
     */
    public function viewData(): array
    {
        $data = $this->all();

        $data['assets'] = [
            'logo' => $this->mediaUrl(data_get($data, 'identity.logo'), asset('images/logo.svg')),
            'favicon' => $this->mediaUrl(data_get($data, 'identity.favicon'), asset('images/favicon.png')),
            'page_header' => $this->mediaUrl(data_get($data, 'identity.page_header_image'), asset('images/page-header-bg.jpg')),
            'hero' => $this->mediaUrl(data_get($data, 'hero.image'), asset('images/hero-bg.jpg')),
            'about' => $this->mediaUrl(data_get($data, 'about.image'), asset('images/about-us-image.jpg')),
            'about_video' => $this->mediaUrl(data_get($data, 'about.video_image'), asset('images/about-us-video-image.jpg')),
            'contact' => $this->mediaUrl(data_get($data, 'contact.image'), asset('images/contact-us-img.jpg')),
            'feature' => $this->mediaUrl(data_get($data, 'home.feature_image'), asset('images/cour-feature-image.png')),
            'og_image' => $this->mediaUrl(data_get($data, 'seo.image'), data_get($data, 'identity.logo') ? $this->mediaUrl(data_get($data, 'identity.logo')) : asset('images/logo.svg')),
            'why_1' => $this->mediaUrl(data_get($data, 'home.why_image_1'), asset('images/why-choose-image-1.jpg')),
            'why_2' => $this->mediaUrl(data_get($data, 'home.why_image_2'), asset('images/why-choose-image-2.jpg')),
            'why_item_1' => $this->mediaUrl(data_get($data, 'home.why_items.0.image'), asset('images/why-choose-item-img-1.jpg')),
            'why_item_2' => $this->mediaUrl(data_get($data, 'home.why_items.1.image'), asset('images/why-choose-item-img-2.jpg')),
            'footer_bg' => $this->mediaUrl(data_get($data, 'footer.background_image'), asset('images/footer-bg.jpg')),
            'testimonials_bg' => $this->mediaUrl(data_get($data, 'home.testimonials_background'), asset('images/testimonial-bg.jpg')),
            'dark_section_bg' => $this->mediaUrl(data_get($data, 'home.dark_section_background'), asset('images/dark-section-bg-shape.png')),
            'core_feature_bg' => $this->mediaUrl(data_get($data, 'home.core_feature_background'), asset('images/core-feature-image-bg.png')),
        ];

        // Process why_items images to full URLs
        if (isset($data['home']['why_items']) && is_array($data['home']['why_items'])) {
            foreach ($data['home']['why_items'] as &$item) {
                if (! empty($item['image'])) {
                    $item['image'] = $this->mediaUrl($item['image']);
                }
            }
        }

        $data['map_embed_url'] = $this->mapEmbedUrl();
        $data['map_url'] = $this->safeUrl(data_get($data, 'contact.maps_url'));
        $data['whatsapp_url'] = $this->whatsappUrl(data_get($data, 'contact.whatsapp'));
        $data['contact']['phone_href'] = $this->phoneHref(data_get($data, 'contact.phone'));
        $data['registration']['fee_label'] = $this->money(data_get($data, 'registration.fee'));
        $data['social_links'] = $this->socialLinks($data['social'] ?? []);

        return $data;
    }

    public function mediaUrl(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'images/')) {
            return asset($path);
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        return url(Storage::disk('public')->url($path));
    }

    /**
     * Ambil URL embed dari potongan <iframe> Google Maps atau URL embed biasa.
     */
    public function mapEmbedUrl(): ?string
    {
        $value = trim((string) $this->get('contact.maps_embed'));

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']/i', $value, $matches)) {
            $value = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }

        $url = $this->safeUrl($value);

        if ($url === null) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if (! is_string($host) || ! $this->isGoogleMapsHost($host)) {
            return null;
        }

        if (! str_contains($path, '/maps')) {
            return null;
        }

        return $url;
    }

    public function whatsappUrl(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if (strlen($digits) < 9) {
            return null;
        }

        return 'https://wa.me/'.$digits;
    }

    public function money(mixed $amount): ?string
    {
        if ($amount === null || $amount === '' || ! is_numeric($amount)) {
            return null;
        }

        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }

    /**
     * @param  array<string, string>  $social
     * @return array<int, array{key: string, label: string, icon: string, url: string}>
     */
    private function socialLinks(array $social): array
    {
        $networks = [
            'facebook' => ['label' => 'Facebook', 'icon' => 'fa-facebook-f'],
            'instagram' => ['label' => 'Instagram', 'icon' => 'fa-instagram'],
            'youtube' => ['label' => 'YouTube', 'icon' => 'fa-youtube'],
            'tiktok' => ['label' => 'TikTok', 'icon' => 'fa-tiktok'],
            'x' => ['label' => 'X', 'icon' => 'fa-x-twitter'],
        ];

        $links = [];

        foreach ($networks as $key => $meta) {
            $url = $this->safeUrl($social[$key] ?? null);

            if ($url !== null) {
                $links[] = [
                    'key' => $key,
                    'label' => $meta['label'],
                    'icon' => $meta['icon'],
                    'url' => $url,
                ];
            }
        }

        return $links;
    }

    private function phoneHref(?string $phone): ?string
    {
        $value = preg_replace('/(?!^\+)\D+/', '', (string) $phone) ?? '';

        return $value !== '' ? $value : null;
    }

    private function safeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }

    private function isGoogleMapsHost(string $host): bool
    {
        $host = strtolower($host);

        return in_array($host, [
            'google.com',
            'www.google.com',
            'maps.google.com',
            'google.co.id',
            'www.google.co.id',
            'maps.google.co.id',
        ], true);
    }

    /**
     * Gabungkan default dengan data tersimpan tanpa menimpa list secara aneh.
     *
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private function mergeDefaults(array $stored): array
    {
        return $this->mergeRecursive($this->defaults(), $stored);
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private function mergeRecursive(array $defaults, array $stored): array
    {
        foreach ($stored as $key => $value) {
            $default = $defaults[$key] ?? null;

            if (
                is_array($value)
                && is_array($default)
                && ! array_is_list($value)
                && ! array_is_list($default)
            ) {
                $defaults[$key] = $this->mergeRecursive($default, $value);

                continue;
            }

            $defaults[$key] = $value;
        }

        return $defaults;
    }
}
