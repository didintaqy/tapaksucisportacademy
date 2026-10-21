<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class ReconcileGatewayPayments extends Command
{
    protected $signature = 'payments:reconcile-gateway
        {--apply : Terapkan perubahan status ke database}
        {--limit=0 : Batasi jumlah pembayaran yang diperiksa (0 = tanpa batas)}';

    protected $description = 'Rekonsiliasi status pembayaran lokal dengan payload gateway terakhir';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $limit = max(0, (int) $this->option('limit'));

        $query = Payment::query()->withGateway()->orderBy('updated_at');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $payments = $query->get();

        $mismatches = 0;
        $updated = 0;
        $withoutCallback = 0;
        $inSync = 0;

        foreach ($payments as $payment) {
            $expected = $payment->expectedStatusFromGateway();

            if ($expected === null) {
                $withoutCallback++;

                continue;
            }

            if ($payment->status === $expected) {
                $inSync++;

                continue;
            }

            $mismatches++;

            $this->line(sprintf(
                '%s: %s → %s (gateway: %s)',
                $payment->invoice_number,
                $payment->status->getLabel(),
                $expected->getLabel(),
                $payment->gatewayTransactionStatus() ?? '-',
            ));

            if (! $apply) {
                continue;
            }

            $payment->changeStatus(
                $expected,
                'Rekonsiliasi otomatis dengan payment gateway',
                [
                    'gateway' => $payment->gateway_name,
                    'gateway_status' => $payment->gatewayTransactionStatus(),
                ],
            );

            $updated++;
        }

        $this->info(sprintf(
            'Rekonsiliasi selesai: %d diperiksa, %d selaras, %d tidak sinkron, %d tanpa callback, %d diperbarui%s.',
            $payments->count(),
            $inSync,
            $mismatches,
            $withoutCallback,
            $updated,
            $apply ? '' : ' (dry-run)',
        ));

        return self::SUCCESS;
    }
}
