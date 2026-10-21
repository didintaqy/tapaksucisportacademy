<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Reports\FinancialExcelExport;
use App\Services\Reports\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller
{
    public function __construct(
        private readonly FinancialReportService $reports,
        private readonly FinancialExcelExport $excel,
    ) {}

    public function exportIncome(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Payment::class);

        [$from, $to, $typeId, $method] = $this->incomeFilters($request);

        $query = $this->reports
            ->incomeQuery($from, $to, $typeId, $method)
            ->with(['student', 'paymentType'])
            ->orderBy('paid_at');

        $filename = 'rekap-pemasukan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

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

            $query->chunk(200, function ($payments) use ($handle, &$number): void {
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

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportIncomeExcel(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Payment::class);

        [$from, $to, $typeId, $method] = $this->incomeFilters($request);

        return $this->excel->income($from, $to, $typeId, $method);
    }

    public function exportIncomePdf(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);

        [$from, $to, $typeId, $method] = $this->incomeFilters($request);

        $html = view('reports.income-pdf', [
            'from' => $from,
            'to' => $to,
            'summary' => $this->reports->incomeSummary($from, $to, $typeId, $method),
            'byType' => $this->reports->incomeByType($from, $to, $typeId, $method),
            'byMonth' => $this->reports->incomeByMonth($from, $to, $typeId, $method),
            'generatedAt' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        return $pdf->download('rekap-pemasukan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.pdf');
    }

    public function exportOutstanding(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Payment::class);

        [$typeId, $search] = $this->outstandingFilters($request);

        $rows = $this->reports->outstandingByStudent($typeId, $search);
        $summary = $this->reports->outstandingSummary($typeId, $search);

        $filename = 'laporan-tunggakan-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($rows, $summary): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Kode Siswa', 'Nama Siswa', 'Cabang', 'Telepon', 'Jumlah Tagihan', 'Jatuh Tempo Terlama', 'Total Tunggakan']);

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

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportOutstandingExcel(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Payment::class);

        [$typeId, $search] = $this->outstandingFilters($request);

        return $this->excel->outstanding($typeId, $search);
    }

    public function exportOutstandingPdf(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);

        [$typeId, $search] = $this->outstandingFilters($request);

        $html = view('reports.outstanding-pdf', [
            'rows' => $this->reports->outstandingByStudent($typeId, $search),
            'summary' => $this->reports->outstandingSummary($typeId, $search),
            'search' => $search,
            'generatedAt' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        return $pdf->download('laporan-tunggakan-'.now()->format('Ymd').'.pdf');
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: ?int, 3: ?PaymentMethod}
     */
    private function incomeFilters(Request $request): array
    {
        $from = Carbon::parse($request->query('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->query('to', now()->toDateString()))->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $typeId = filled($request->query('type')) ? (int) $request->query('type') : null;
        $method = filled($request->query('method'))
            ? PaymentMethod::tryFrom((string) $request->query('method'))
            : null;

        return [$from, $to, $typeId, $method];
    }

    /**
     * @return array{0: ?int, 1: ?string}
     */
    private function outstandingFilters(Request $request): array
    {
        $typeId = filled($request->query('type')) ? (int) $request->query('type') : null;
        $search = filled($request->query('search')) ? trim((string) $request->query('search')) : null;

        return [$typeId, $search];
    }
}
