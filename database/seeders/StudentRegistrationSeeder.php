<?php

namespace Database\Seeders;

use App\Enums\RegistrationStatus;
use App\Models\Program;
use App\Models\StudentRegistration;
use Illuminate\Database\Seeder;

class StudentRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrations = [
            [
                'full_name' => 'Muhammad Rizky Pratama',
                'nickname' => 'Rizky',
                'gender' => 'L',
                'birth_place' => 'Batam',
                'birth_date' => '2015-06-12',
                'education' => 'SD',
                'parent_name' => 'Andi Pratama',
                'phone' => '081234567801',
                'email' => 'andi@example.com',
                'address' => 'Perumahan Cendana Blok F No. 4, Batam',
                'branch' => 'Cabang Pusat',
                'notes' => 'Belum pernah ikut silat.',
                'status' => RegistrationStatus::New,
            ],
            [
                'full_name' => 'Aisyah Putri',
                'nickname' => 'Aisyah',
                'gender' => 'P',
                'birth_place' => 'Batam',
                'birth_date' => '2013-11-03',
                'education' => 'SMP',
                'parent_name' => 'Siti Aminah',
                'phone' => '081234567802',
                'email' => 'siti@example.com',
                'address' => 'Batam Kota, dekat Gedung Serbaguna',
                'branch' => 'Cabang Batam Kota',
                'notes' => null,
                'status' => RegistrationStatus::Contacted,
            ],
            [
                'full_name' => 'Fikri Ramadhan',
                'nickname' => 'Fikri',
                'gender' => 'L',
                'birth_place' => 'Tanjung Pinang',
                'birth_date' => '2010-02-20',
                'education' => 'SMA',
                'parent_name' => 'Hendra Ramadhan',
                'phone' => '081234567803',
                'email' => null,
                'address' => 'Jl. Melati No. 10, Batam',
                'branch' => 'Cabang Pusat',
                'notes' => 'Ingin ikut kejuaraan.',
                'status' => RegistrationStatus::Accepted,
            ],
        ];

        $programIds = Program::query()->pluck('id', 'slug');
        $programMap = [
            'Muhammad Rizky Pratama' => 'kelas-dasar-tapak-suci',
            'Aisyah Putri' => 'kelas-remaja',
            'Fikri Ramadhan' => 'kelas-remaja',
        ];

        foreach ($registrations as $registration) {
            $slug = $programMap[$registration['full_name']] ?? null;

            StudentRegistration::updateOrCreate(
                ['phone' => $registration['phone'], 'full_name' => $registration['full_name']],
                [...$registration, 'program_id' => $slug ? ($programIds[$slug] ?? null) : null],
            );
        }
    }
}
