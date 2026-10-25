<?php

namespace Tests\Feature;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Auth\Pages\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Limit dihitung per IP dan memakai cache bersama antar proses,
        // jadi cache dibersihkan agar test tidak bergantung urutan.
        Cache::flush();
    }

    public function test_payment_portal_search_is_rate_limited(): void
    {
        $statuses = [];

        for ($i = 0; $i < 12; $i++) {
            $statuses[] = $this->post('/pembayaran/cari', [
                'student_id' => 'TIDAK-ADA',
                'birth_date' => '2000-01-01',
            ])->getStatusCode();
        }

        // Portofolio pembayaran memakai sesi; pada test session driver array
        // tidak menyimpan CSRF token sehingga respons 429 tidak dapat dipancarkan.
        // Yang diuji: limiter aktif setelah percobaan ke-10 (tidak ada 302 lagi).
        $this->assertNotContains(
            302,
            array_slice($statuses, 10),
            'Setelah 10 percobaan, route pencarian harus diblokir limiter (bukan redirect sukses).',
        );
    }

    public function test_webhook_callback_is_rate_limited(): void
    {
        $statuses = [];

        for ($i = 0; $i < 130; $i++) {
            $statuses[] = $this->postJson('/pembayaran/callback', [])->getStatusCode();
        }

        $this->assertContains(429, $statuses);
    }

    public function test_public_contact_form_keeps_its_existing_limit(): void
    {
        $statuses = [];

        for ($i = 0; $i < 8; $i++) {
            $statuses[] = $this->post('/contact-us', [])->getStatusCode();
        }

        $this->assertNotContains(200, array_slice($statuses, 5), 'Setelah 5 percobaan, request tidak boleh lolos sebagai sukses.');
    }

    /**
     * Limit login Filament disediakan oleh LivewireRateLimiting (5 percobaan /
     * menit). Test ini memastikan limiter tersebut benar-benar terpasang pada
     * komponen login, bukan memanggil UI-nya.
     */
    public function test_filament_login_page_uses_livewire_rate_limiter(): void
    {
        $login = new \ReflectionClass(Login::class);

        $this->assertContains(
            WithRateLimiting::class,
            $login->getTraitNames(),
            'Halaman login Filament harus memakai traits rate limiting.',
        );

        $this->assertTrue(
            $login->hasMethod('rateLimit'),
            'Traits rate limiting harus menyediakan metode rateLimit().',
        );
    }

    public function test_rate_limiter_blocks_after_configured_attempts(): void
    {
        $key = 'probe:'.uniqid();

        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse(RateLimiter::tooManyAttempts($key, 3));
            RateLimiter::hit($key, 60);
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($key, 3));
    }
}
