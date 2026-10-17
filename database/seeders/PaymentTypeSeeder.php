<?php

namespace Database\Seeders;

use App\Enums\PaymentPeriodType;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Iuran Bulanan (SPP)',
                'slug' => 'iuran-bulanan',
                'description' => 'Iuran latihan rutin yang dibayarkan setiap bulan.',
                'default_amount' => 150000,
                'period_type' => PaymentPeriodType::Monthly,
                'sort_order' => 1,
            ],
            [
                'name' => 'Biaya Pendaftaran',
                'slug' => 'biaya-pendaftaran',
                'description' => 'Biaya administrasi pendaftaran siswa baru.',
                'default_amount' => 250000,
                'period_type' => PaymentPeriodType::OneTime,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ujian Kenaikan Tingkat',
                'slug' => 'ujian-kenaikan-tingkat',
                'description' => 'Biaya ujian kenaikan sabuk / tingkatan.',
                'default_amount' => 200000,
                'period_type' => PaymentPeriodType::Event,
                'sort_order' => 3,
            ],
            [
                'name' => 'Biaya Kejuaraan / Event',
                'slug' => 'biaya-kejuaraan',
                'description' => 'Biaya pendaftaran kejuaraan atau kegiatan akademi.',
                'default_amount' => 300000,
                'period_type' => PaymentPeriodType::Event,
                'sort_order' => 4,
            ],
        ];

        foreach ($types as $type) {
            PaymentType::updateOrCreate(
                ['slug' => $type['slug']],
                $type + ['is_active' => true],
            );
            $this->command->info('Jenis pembayaran '.$type['name'].' siap.');
        }
    }
}
