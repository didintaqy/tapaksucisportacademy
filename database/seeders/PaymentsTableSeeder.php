<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

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
                Payment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'payment_type_id' => $registrationId,
                        'description' => 'Biaya pendaftaran siswa baru',
                    ],
                    [
                        'invoice_number' => 'INV-REG-'.$student->id.'-'.now()->subMonths(3)->format('Ymd'),
                        'amount' => 250000,
                        'status' => PaymentStatus::Paid,
                        'payment_method' => PaymentMethod::Cash,
                        'paid_at' => now()->subMonths(3),
                        'due_date' => now()->subMonths(3),
                    ],
                );
            }

            if ($sppId) {
                // Bulan lalu: sudah lunas
                Payment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'payment_type_id' => $sppId,
                        'period' => now()->subMonth()->format('Y-m'),
                    ],
                    [
                        'invoice_number' => 'INV-SPP-'.$student->id.'-'.now()->subMonth()->format('Ym'),
                        'description' => 'Iuran bulanan '.now()->subMonth()->translatedFormat('F Y'),
                        'amount' => 150000,
                        'status' => PaymentStatus::Paid,
                        'payment_method' => PaymentMethod::Transfer,
                        'paid_at' => now()->subMonth(),
                        'due_date' => now()->subMonth()->endOfMonth(),
                    ],
                );

                // Bulan ini: belum lunas
                Payment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'payment_type_id' => $sppId,
                        'period' => now()->format('Y-m'),
                    ],
                    [
                        'invoice_number' => 'INV-SPP-'.$student->id.'-'.now()->format('Ym'),
                        'description' => 'Iuran bulanan '.now()->translatedFormat('F Y'),
                        'amount' => 150000,
                        'status' => PaymentStatus::Unpaid,
                        'due_date' => now()->endOfMonth(),
                    ],
                );
            }

            if ($eventId) {
                Payment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'payment_type_id' => $eventId,
                        'description' => 'Biaya kejuaraan regional',
                    ],
                    [
                        'invoice_number' => 'INV-EVT-'.$student->id.'-'.now()->format('Ymd'),
                        'amount' => 300000,
                        'status' => PaymentStatus::Pending,
                        'payment_method' => PaymentMethod::Qris,
                        'due_date' => now()->addWeeks(2),
                    ],
                );
            }
        }

        $this->seedGatewayPayments($students->take(6)->values(), $eventId ?? $sppId);

        $this->command->info('Berhasil membuat contoh data pembayaran untuk setiap siswa.');
    }

    /**
     * Contoh transaksi payment gateway untuk simulasi rekonsiliasi.
     * Sebagian sengaja dibuat tidak sinkron (status lokal vs gateway).
     *
     * @param  Collection<int, Student>  $students
     */
    private function seedGatewayPayments(Collection $students, ?int $paymentTypeId): void
    {
        if ($paymentTypeId === null) {
            return;
        }

        $scenarios = [
            [PaymentStatus::Paid, 'settlement'],
            [PaymentStatus::Pending, 'settlement'], // tidak sinkron
            [PaymentStatus::Pending, 'pending'],
            [PaymentStatus::Paid, 'settlement'],
            [PaymentStatus::Expired, 'expire'],
            [PaymentStatus::Pending, 'settlement'], // tidak sinkron
        ];

        foreach ($students as $index => $student) {
            [$localStatus, $gatewayStatus] = $scenarios[$index % count($scenarios)];

            $invoice = 'INV-GW-'.$student->id.'-'.now()->format('Ym');

            Payment::updateOrCreate(
                ['invoice_number' => $invoice],
                [
                    'student_id' => $student->id,
                    'payment_type_id' => $paymentTypeId,
                    'period' => null,
                    'description' => 'Pembayaran QRIS via Midtrans',
                    'amount' => 150000,
                    'status' => $localStatus,
                    'payment_method' => PaymentMethod::Gateway,
                    'due_date' => now()->addWeek(),
                    'paid_at' => $localStatus === PaymentStatus::Paid ? now()->subDays($index + 1) : null,
                    'gateway_name' => 'midtrans',
                    'gateway_reference' => 'trx-'.$student->id.'-'.now()->format('Ymd'),
                    'gateway_payload' => [
                        'last_callback' => [
                            'order_id' => $invoice,
                            'transaction_status' => $gatewayStatus,
                            'transaction_time' => now()->subDays($index + 1)->toDateTimeString(),
                        ],
                    ],
                ],
            );
        }
    }
}
