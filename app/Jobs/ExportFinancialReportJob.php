<?php

namespace App\Jobs;

use App\Enums\PaymentMethod;
use App\Models\User;
use App\Services\Reports\FinancialReportService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportFinancialReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public int $userId,
        public string $reportType,
        public array $filters = [],
    ) {}

    public function handle(FinancialReportService $reports): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        $disk = Storage::disk('public');
        $timestamp = now()->format('Ymd-His');

        if ($this->reportType === 'outstanding') {
            $typeId = isset($this->filters['type']) && $this->filters['type'] !== '' ? (int) $this->filters['type'] : null;
            $search = isset($this->filters['search']) && trim((string) $this->filters['search']) !== ''
                ? trim((string) $this->filters['search'])
                : null;

            $filename = "exports/laporan-tunggakan-{$timestamp}.csv";
            $content = $this->buildOutstandingCsv($reports, $typeId, $search);
            $disk->put($filename, $content);

            Notification::make()
                ->title('Export Laporan Tunggakan selesai')
                ->body('File CSV siap diunduh.')
                ->success()
                ->actions([
                    Action::make('download')
                        ->label('Unduh CSV')
                        ->url($disk->url($filename), shouldOpenInNewTab: true),
                ])
                ->sendToDatabase($user);

            return;
        }

        $from = Carbon::parse($this->filters['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($this->filters['to'] ?? now()->toDateString())->endOfDay();
        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $typeId = isset($this->filters['type']) && $this->filters['type'] !== '' ? (int) $this->filters['type'] : null;
        $method = isset($this->filters['method']) && $this->filters['method'] !== ''
            ? PaymentMethod::tryFrom((string) $this->filters['method'])
            : null;

        $filename = "exports/rekap-pemasukan-{$from->format('Ymd')}-{$to->format('Ymd')}-{$timestamp}.csv";
        $content = $this->buildIncomeCsv($reports, $from, $to, $typeId, $method);
        $disk->put($filename, $content);

        Notification::make()
            ->title('Export Rekap Pemasukan selesai')
            ->body("Periode {$from->format('d/m/Y')} s.d. {$to->format('d/m/Y')} siap diunduh.")
            ->success()
            ->actions([
                Action::make('download')
                    ->label('Unduh CSV')
                    ->url($disk->url($filename), shouldOpenInNewTab: true),
            ])
            ->sendToDatabase($user);
    }

    private function buildIncomeCsv(
        FinancialReportService $reports,
        Carbon $from,
        Carbon $to,
        ?int $typeId,
        ?PaymentMethod $method,
    ): string {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'No',
            'Tanggal Bayar',
            'No. Invoice',
            'Kode Siswa',
            'Nama Siswa',
            'Jenis Pembayaran',
            'Metode',
            'Nominal',
        ]);

        $number = 1;
        $reports
            ->incomeQuery($from, $to, $typeId, $method)
            ->with(['student', 'paymentType'])
            ->orderBy('paid_at')
            ->chunk(200, function ($payments) use ($handle, &$number): void {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $number++,
                        optional($payment->paid_at)->format('Y-m-d H:i'),
                        $payment->invoice_number,
                        $payment->student?->student_code,
                        $payment->student?->full_name,
                        $payment->paymentType?->name,
                        $payment->payment_method?->getLabel(),
                        number_format((float) $payment->amount, 2, '.', ''),
                    ]);
                }
            });

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private function buildOutstandingCsv(
        FinancialReportService $reports,
        ?int $typeId,
        ?string $search,
    ): string {
        $rows = $reports->outstandingByStudent($typeId, $search);
        $summary = $reports->outstandingSummary($typeId, $search);

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'No',
            'Kode Siswa',
            'Nama Siswa',
            'Cabang',
            'Telepon',
            'Jumlah Tagihan',
            'Jatuh Tempo Terlama',
            'Total Tunggakan',
        ]);

        $number = 1;
        foreach ($rows as $row) {
            fputcsv($handle, [
                $number++,
                $row->student?->student_code,
                $row->student?->full_name,
                $row->student?->branch,
                $row->student?->phone_number,
                (int) $row->bills,
                $row->oldest_due_date,
                number_format((float) $row->total, 2, '.', ''),
            ]);
        }

        fputcsv($handle, []);
        fputcsv($handle, ['', '', '', '', 'TOTAL', $summary['count'], '', number_format($summary['total'], 2, '.', '')]);

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
