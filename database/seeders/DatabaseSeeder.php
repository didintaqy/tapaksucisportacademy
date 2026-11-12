<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Entry point `php artisan db:seed` tanpa `--class`.
 *
 * Secara default hanya menjalankan seeder yang aman untuk produksi, karena
 * perintah ini juga dipakai pada deploy dan instalasi produksi. Data demo
 * (siswa, pembayaran, akun contoh, konten fiktif) hanya dibuat di lingkungan
 * lokal melalui `DatabaseSeeder` dengan argumen `--class`.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Aplikasi produksi/staging tidak boleh menerima data demo.
     */
    private const NON_DEMO_ENVIRONMENTS = ['production', 'staging'];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if ($this->isDemoEnvironment()) {
            $this->call([
                UsersTableSeeder::class,
                SiteSettingSeeder::class,
                ProgramSeeder::class,
                StudentsTableSeeder::class,
                StudentPerformanceTableSeeder::class,
                PaymentTypeSeeder::class,
                PaymentsTableSeeder::class,
                GallerySeeder::class,
                CategorySeeder::class,
                TagSeeder::class,
                PostSeeder::class,
                NewsletterSeeder::class,
                TestimonialSeeder::class,
                FaqSeeder::class,
                TrainingScheduleSeeder::class,
                StudentRegistrationSeeder::class,
                EventSeeder::class,
                AttendanceSeeder::class,
                BeltPromotionSeeder::class,
                FitnessBenchmarkSeeder::class,
                AchievementSeeder::class,
            ]);

            return;
        }

        // ProductionSeeder akan melempar exception bila ADMIN_EMAIL /
        // ADMIN_PASSWORD belum diisi, sehingga salah konfigurasi langsung
        // terlihat alih-alih membuat akun tanpa kata sandi.
        $this->call(ProductionSeeder::class);
    }

    private function isDemoEnvironment(): bool
    {
        return ! in_array(app()->environment(), self::NON_DEMO_ENVIRONMENTS, true);
    }
}
