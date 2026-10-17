<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use Illuminate\Database\Seeder;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->warn('Tabel students masih kosong. Jalankan StudentsTableSeeder terlebih dahulu.');

            return;
        }

        $types = PaymentType::pluck('id', 'slug');
        $sppId = $types['iuran-bulanan'] ?? null;
        $registrationId = $types['biaya-pendaftaran'] ?? null;
        $eventId = $types['biaya-kejuaraan'] ?? null;

        foreach ($students as $student) {
            if ($registrationId) {
                Payment::create([
                    'student_id' => $student->id,
                    'payment_type_id' => $registrationId,
                    'description' => 'Biaya pendaftaran siswa baru',
                    'amount' => 250000,
                    'status' => PaymentStatus::Paid,
                    'payment_method' => PaymentMethod::Cash,
                    'paid_at' => now()->subMonths(3),
                    'due_date' => now()->subMonths(3),
                ]);
            }

            if ($sppId) {
                // Bulan lalu: sudah lunas
                Payment::create([
                    'student_id' => $student->id,
                    'payment_type_id' => $sppId,
                    'period' => now()->subMonth()->format('Y-m'),
                    'description' => 'Iuran bulanan '.now()->subMonth()->translatedFormat('F Y'),
                    'amount' => 150000,
                    'status' => PaymentStatus::Paid,
                    'payment_method' => PaymentMethod::Transfer,
                    'paid_at' => now()->subMonth(),
                    'due_date' => now()->subMonth()->endOfMonth(),
                ]);

                // Bulan ini: belum lunas
                Payment::create([
                    'student_id' => $student->id,
                    'payment_type_id' => $sppId,
                    'period' => now()->format('Y-m'),
                    'description' => 'Iuran bulanan '.now()->translatedFormat('F Y'),
                    'amount' => 150000,
                    'status' => PaymentStatus::Unpaid,
                    'due_date' => now()->endOfMonth(),
                ]);
            }

            if ($eventId) {
                Payment::create([
                    'student_id' => $student->id,
                    'payment_type_id' => $eventId,
                    'description' => 'Biaya kejuaraan regional',
                    'amount' => 300000,
                    'status' => PaymentStatus::Pending,
                    'payment_method' => PaymentMethod::Qris,
                    'due_date' => now()->addWeeks(2),
                ]);
            }
        }

        $this->command->info('Berhasil membuat contoh data pembayaran untuk setiap siswa.');
    }
}
