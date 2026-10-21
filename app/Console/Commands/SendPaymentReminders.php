<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders
        {--days=3 : Kirim pengingat untuk tagihan yang jatuh tempo dalam N hari ke depan}
        {--limit=0 : Batasi jumlah tagihan yang diproses (0 = tanpa batas)}
        {--dry-run : Tampilkan tagihan yang akan diingatkan tanpa mengirim email}';

    protected $description = 'Kirim email pengingat untuk tagihan yang belum lunas';

    public function handle(): int
    {
        $days = max(0, (int) $this->option('days'));
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $query = Payment::query()
            ->dueForReminder($days)
            ->whereHas('student', fn ($studentQuery) => $studentQuery->whereNotNull('email'))
            ->with(['student', 'paymentType'])
            ->orderBy('due_date');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $payments = $query->get();

        if ($payments->isEmpty()) {
            $this->info('Tidak ada tagihan yang perlu diingatkan.');

            return self::SUCCESS;
        }

        $processed = 0;
        $withoutEmail = 0;

        foreach ($payments as $payment) {
            $email = $payment->student?->email;

            if (blank($email)) {
                $withoutEmail++;

                continue;
            }

            $daysUntilDue = $payment->due_date
                ? (int) now()->startOfDay()->diffInDays($payment->due_date->copy()->startOfDay(), false)
                : 0;

            if ($dryRun) {
                $this->line(sprintf(
                    '[dry-run] %s → %s (%s, jatuh tempo %s)',
                    $payment->invoice_number,
                    $email,
                    $payment->formattedAmount(),
                    $payment->due_date?->format('d/m/Y') ?? '-',
                ));

                $processed++;

                continue;
            }

            Mail::to($email)->queue(new PaymentReminderMail($payment, $daysUntilDue));

            $payment->markAsReminded();

            $processed++;
        }

        $mode = $dryRun ? 'dry-run' : 'terkirim';

        $this->info("Pengingat tagihan ({$mode}): {$processed} diproses, {$withoutEmail} tanpa email.");

        return self::SUCCESS;
    }
}
