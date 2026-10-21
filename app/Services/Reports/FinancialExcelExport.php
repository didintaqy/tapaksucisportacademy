<?php

namespace App\Services\Reports;

use App\Enums\PaymentMethod;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialExcelExport
{
    public function __construct(private readonly FinancialReportService $reports) {}

    public function income(
        Carbon $from,
        Carbon $to,
        ?int $typeId = null,
        ?PaymentMethod $method = null,
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $this->writeIncomeSummarySheet($spreadsheet, $from, $to, $typeId, $method);
        $this->writeIncomeDetailSheet($spreadsheet, $from, $to, $typeId, $method);

        return $this->download(
            $spreadsheet,
            'rekap-pemasukan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx',
        );
    }

    public function outstanding(?int $typeId = null, ?string $search = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $this->writeOutstandingSummarySheet($spreadsheet, $typeId, $search);
        $this->writeOutstandingDetailSheet($spreadsheet, $typeId, $search);

        return $this->download($spreadsheet, 'laporan-tunggakan-'.now()->format('Ymd').'.xlsx');
    }

    private function writeIncomeSummarySheet(
        Spreadsheet $spreadsheet,
        Carbon $from,
        Carbon $to,
        ?int $typeId,
        ?PaymentMethod $method,
    ): void {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Rekap');

        $row = $this->writeTitle($sheet, 'REKAP PEMASUKAN', $from->translatedFormat('d F Y').' — '.$to->translatedFormat('d F Y'));

        $summary = $this->reports->incomeSummary($from, $to, $typeId, $method);

        $this->writeMetricCards($sheet, $row, [
            ['Total Pemasukan', 'Rp '.number_format($summary['total'], 0, ',', '.')],
            ['Jumlah Transaksi', number_format($summary['count'], 0, ',', '.')],
            ['Rata-rata per Transaksi', 'Rp '.number_format($summary['average'], 0, ',', '.')],
        ]);

        $row += 3;

        $row = $this->writeSectionTitle($sheet, $row, 'Rekap per Bulan');

        $this->writeHeaderRow($sheet, $row, ['Periode', 'Transaksi', 'Total']);
        $row++;

        $firstDataRow = $row;

        foreach ($this->reports->incomeByMonth($from, $to, $typeId, $method) as $month) {
            $sheet->setCellValue("A{$row}", $month['label']);
            $sheet->setCellValue("B{$row}", $month['transactions']);
            $sheet->setCellValue("C{$row}", $month['total']);
            $row++;
        }

        $sheet->getStyle("C{$firstDataRow}:C".($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $this->writeTableBorders($sheet, $firstDataRow, $row - 1, 'C');

        $row++;
        $row = $this->writeSectionTitle($sheet, $row, 'Rekap per Jenis Pembayaran');

        $this->writeHeaderRow($sheet, $row, ['Jenis Pembayaran', 'Transaksi', 'Total']);
        $row++;

        $firstTypeRow = $row;

        foreach ($this->reports->incomeByType($from, $to, $typeId, $method) as $typeRow) {
            $sheet->setCellValue("A{$row}", $typeRow->paymentType?->name ?? 'Tanpa jenis');
            $sheet->setCellValue("B{$row}", (int) $typeRow->transactions);
            $sheet->setCellValue("C{$row}", (float) $typeRow->total);
            $row++;
        }

        $sheet->getStyle("C{$firstTypeRow}:C".($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $this->writeTableBorders($sheet, $firstTypeRow, $row - 1, 'C');

        $this->autoSize($sheet, ['A', 'B', 'C']);
    }

    private function writeIncomeDetailSheet(
        Spreadsheet $spreadsheet,
        Carbon $from,
        Carbon $to,
        ?int $typeId,
        ?PaymentMethod $method,
    ): void {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Detail Transaksi');

        $row = $this->writeTitle($sheet, 'DETAIL TRANSAKSI PEMASUKAN');

        $this->writeHeaderRow($sheet, $row, [
            'No',
            'Tanggal Bayar',
            'No. Invoice',
            'Kode Siswa',
            'Nama Siswa',
            'Jenis Pembayaran',
            'Metode',
            'Nominal',
        ]);

        $headerRow = $row;
        $row++;

        $number = 1;

        $query = $this->reports
            ->incomeQuery($from, $to, $typeId, $method)
            ->with(['student', 'paymentType'])
            ->orderBy('paid_at');

        foreach ($query->cursor() as $payment) {
            $sheet->setCellValue("A{$row}", $number++);
            $sheet->setCellValue("B{$row}", $payment->paid_at?->format('d/m/Y H:i'));
            $sheet->setCellValue("C{$row}", $payment->invoice_number);
            $sheet->setCellValue("D{$row}", $payment->student?->student_code);
            $sheet->setCellValue("E{$row}", $payment->student?->full_name);
            $sheet->setCellValue("F{$row}", $payment->paymentType?->name);
            $sheet->setCellValue("G{$row}", $payment->payment_method?->getLabel());
            $sheet->setCellValue("H{$row}", (float) $payment->amount);
            $row++;
        }

        if ($row > $headerRow + 1) {
            $sheet->getStyle('H'.($headerRow + 1).':H'.($row - 1))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $this->writeTableBorders($sheet, $headerRow + 1, $row - 1, 'H');
        }

        $sheet->freezePane('A'.($headerRow + 1));

        $this->autoSize($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
    }

    private function writeOutstandingSummarySheet(
        Spreadsheet $spreadsheet,
        ?int $typeId,
        ?string $search,
    ): void {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Rekap Tunggakan');

        $row = $this->writeTitle(
            $sheet,
            'LAPORAN TUNGGAKAN PER SISWA',
            'Per '.now()->translatedFormat('d F Y').($search ? ' — Filter: "'.$search.'"' : ''),
        );

        $summary = $this->reports->outstandingSummary($typeId, $search);

        $this->writeMetricCards($sheet, $row, [
            ['Total Tunggakan', 'Rp '.number_format($summary['total'], 0, ',', '.')],
            ['Jumlah Tagihan', number_format($summary['count'], 0, ',', '.')],
            ['Siswa Menunggak', number_format($summary['students'], 0, ',', '.')],
            ['Lewat Jatuh Tempo', number_format($summary['overdue'], 0, ',', '.')],
        ]);

        $row += 3;
        $row = $this->writeSectionTitle($sheet, $row, 'Daftar Tunggakan');

        $this->writeHeaderRow($sheet, $row, [
            'No',
            'Kode Siswa',
            'Nama Siswa',
            'Cabang',
            'Telepon',
            'Jumlah Tagihan',
            'Jatuh Tempo Terlama',
            'Total Tunggakan',
        ], '9F1239');

        $headerRow = $row;
        $row++;

        $number = 1;

        foreach ($this->reports->outstandingByStudent($typeId, $search) as $studentRow) {
            $sheet->setCellValue("A{$row}", $number++);
            $sheet->setCellValue("B{$row}", $studentRow->student?->student_code);
            $sheet->setCellValue("C{$row}", $studentRow->student?->full_name ?? 'Siswa terhapus');
            $sheet->setCellValue("D{$row}", $studentRow->student?->branch);
            $sheet->setCellValue("E{$row}", $studentRow->student?->phone_number);
            $sheet->setCellValue("F{$row}", (int) $studentRow->bills);
            $sheet->setCellValue(
                "G{$row}",
                $studentRow->oldest_due_date
                    ? Carbon::parse($studentRow->oldest_due_date)->format('d/m/Y')
                    : null,
            );
            $sheet->setCellValue("H{$row}", (float) $studentRow->total);
            $row++;
        }

        if ($row > $headerRow + 1) {
            $sheet->getStyle('H'.($headerRow + 1).':H'.($row - 1))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $this->writeTableBorders($sheet, $headerRow + 1, $row - 1, 'H');
        }

        $sheet->freezePane('A'.($headerRow + 1));

        $this->autoSize($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
    }

    private function writeOutstandingDetailSheet(
        Spreadsheet $spreadsheet,
        ?int $typeId,
        ?string $search,
    ): void {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Detail Tagihan');

        $row = $this->writeTitle($sheet, 'DETAIL TAGIHAN BELUM LUNAS');

        $this->writeHeaderRow($sheet, $row, [
            'No',
            'No. Invoice',
            'Kode Siswa',
            'Nama Siswa',
            'Jenis Pembayaran',
            'Periode',
            'Jatuh Tempo',
            'Status',
            'Nominal',
        ], '9F1239');

        $headerRow = $row;
        $row++;

        $number = 1;

        $query = $this->reports
            ->outstandingQuery($typeId, $search)
            ->with(['student', 'paymentType'])
            ->orderBy('due_date');

        foreach ($query->cursor() as $payment) {
            $sheet->setCellValue("A{$row}", $number++);
            $sheet->setCellValue("B{$row}", $payment->invoice_number);
            $sheet->setCellValue("C{$row}", $payment->student?->student_code);
            $sheet->setCellValue("D{$row}", $payment->student?->full_name);
            $sheet->setCellValue("E{$row}", $payment->paymentType?->name);
            $sheet->setCellValue("F{$row}", $payment->period);
            $sheet->setCellValue("G{$row}", $payment->due_date?->format('d/m/Y'));
            $sheet->setCellValue("H{$row}", $payment->status->getLabel());
            $sheet->setCellValue("I{$row}", (float) $payment->amount);
            $row++;
        }

        if ($row > $headerRow + 1) {
            $sheet->getStyle('I'.($headerRow + 1).':I'.($row - 1))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $this->writeTableBorders($sheet, $headerRow + 1, $row - 1, 'I');
        }

        $sheet->freezePane('A'.($headerRow + 1));

        $this->autoSize($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I']);
    }

    private function writeTitle(Worksheet $sheet, string $title, ?string $subtitle = null): int
    {
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'TAPAK SUCI SPORT ACADEMY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1E3A8A');

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', $title);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', $subtitle ?? '');
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('4B5563');

        return 5;
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $metrics
     */
    private function writeMetricCards(Worksheet $sheet, int $row, array $metrics): void
    {
        $column = 'A';

        foreach ($metrics as [$label, $value]) {
            $columnEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($column) + 1);

            $sheet->mergeCells("{$column}{$row}:{$columnEnd}{$row}");
            $sheet->setCellValue("{$column}{$row}", $label);
            $sheet->getStyle("{$column}{$row}:{$columnEnd}{$row}")
                ->getFont()->setSize(8)->getColor()->setRGB('6B7280');
            $sheet->getStyle("{$column}{$row}:{$columnEnd}{$row}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells("{$column}".($row + 1).":{$columnEnd}".($row + 1));
            $sheet->setCellValue("{$column}".($row + 1), $value);
            $sheet->getStyle("{$column}".($row + 1).":{$columnEnd}".($row + 1))
                ->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle("{$column}".($row + 1).":{$columnEnd}".($row + 1))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $column = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnEnd) + 1);
        }
    }

    private function writeSectionTitle(Worksheet $sheet, int $row, string $title): int
    {
        $sheet->setCellValue("A{$row}", $title);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('1E3A8A');

        return $row + 1;
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function writeHeaderRow(Worksheet $sheet, int $row, array $headers, string $rgb = '1E3A8A'): void
    {
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue("{$column}{$row}", $header);
        }

        $range = "A{$row}:{$lastColumn}{$row}";

        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($rgb);
        $sheet->getStyle($range)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function writeTableBorders(Worksheet $sheet, int $firstRow, int $lastRow, string $lastColumn): void
    {
        $sheet->getStyle("A{$firstRow}:{$lastColumn}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()
            ->setRGB('E5E7EB');
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function autoSize(Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ],
        );
    }
}
