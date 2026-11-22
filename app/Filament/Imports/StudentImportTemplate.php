<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;

/**
 * Membuat template CSV untuk impor siswa.
 *
 * Header diambil langsung dari label ImportColumn agar saat diunggah,
 * kolom template otomatis terpetakan ke kolom importer yang benar.
 */
final class StudentImportTemplate
{
    private const DELIMITER = ';';

    public static function fileName(): string
    {
        return 'template-impor-siswa-'.now()->format('Y-m-d').'.csv';
    }

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return array_map(
            fn (ImportColumn $column): string => $column->getExampleHeader(),
            StudentImporter::getColumns(),
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function exampleRows(): array
    {
        $columns = array_map(
            fn (ImportColumn $column): array => array_values($column->getExamples()),
            StudentImporter::getColumns(),
        );

        $rowCount = max(1, ...array_map('count', $columns));

        $rows = [];

        for ($index = 0; $index < $rowCount; $index++) {
            $rows[] = array_map(
                fn (array $examples): string => (string) ($examples[$index] ?? ''),
                $columns,
            );
        }

        return $rows;
    }

    public static function contents(): string
    {
        $handle = fopen('php://temp', 'r+');

        // BOM UTF-8 agar huruf beraksen dan simbol terbaca benar di Excel.
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, self::headers(), self::DELIMITER, '"', '\\', "\r\n");

        foreach (self::exampleRows() as $row) {
            fputcsv($handle, $row, self::DELIMITER, '"', '\\', "\r\n");
        }

        rewind($handle);

        $contents = (string) stream_get_contents($handle);

        fclose($handle);

        return $contents;
    }
}
