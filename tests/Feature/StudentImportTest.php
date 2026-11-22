<?php

namespace Tests\Feature;

use App\Filament\Imports\StudentImporter;
use App\Filament\Imports\StudentImportTemplate;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Program;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Testing\TestableImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_headers_match_importer_labels_and_guesses(): void
    {
        $contents = StudentImportTemplate::contents();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $contents);

        $firstLine = ltrim(strtok($contents, "\r\n"), "\xEF\xBB\xBF");
        $headers = str_getcsv($firstLine, ';');

        $this->assertSame(
            array_map(
                fn (ImportColumn $column): string => $column->getExampleHeader(),
                StudentImporter::getColumns(),
            ),
            $headers,
        );

        foreach (StudentImporter::getColumns() as $column) {
            $this->assertContains(
                mb_strtolower($column->getExampleHeader()),
                $column->getGuesses(),
                "Header [{$column->getExampleHeader()}] tidak akan terpetakan otomatis.",
            );
        }

        $this->assertGreaterThan(1, count(StudentImportTemplate::exampleRows()));
    }

    public function test_template_can_be_downloaded_from_students_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ListStudents::class)
            ->callAction('downloadImportTemplate')
            ->assertFileDownloaded(StudentImportTemplate::fileName());
    }

    public function test_import_row_normalizes_values_and_links_program_by_name(): void
    {
        $program = Program::factory()->create(['name' => 'Tapak Suci Dasar']);

        $headers = StudentImportTemplate::headers();

        $columnMap = [];

        foreach (StudentImporter::getColumns() as $index => $column) {
            $columnMap[$column->getName()] = $headers[$index];
        }

        $testable = TestableImport::make(StudentImporter::class, $columnMap)
            ->import([
                'Nama Lengkap' => '  Ahmad   Fauzi  ',
                'Nama Panggilan' => ' Ahmad ',
                'Tempat Lahir' => ' Padang ',
                'Tanggal Lahir' => '17/05/2010',
                'NIK' => '1371011504120001',
                'No. Kartu Keluarga' => '1371011504120002',
                'No. Telepon' => '+62 812-3456-7890',
                'Email' => ' AHMAD@EXAMPLE.COM ',
                'Jenis Kelamin' => 'Laki-laki',
                'Tingkat Sabuk' => 'Siswa Melati 1',
                'Pendidikan' => 'Sekolah Menengah Atas',
                'Alamat' => 'Jl. Melati No. 10, Padang',
                'Cabang' => 'Cabang Pusat',
                'Program' => 'tapak suci dasar',
            ])
            ->assertImported();

        $student = $testable->getRecord();

        $this->assertSame('Ahmad Fauzi', $student->full_name);
        $this->assertSame('Ahmad', $student->nickname);
        $this->assertSame('Padang', $student->birth_place);
        $this->assertSame('2010-05-17', (string) $student->birth_date);
        $this->assertSame('081234567890', $student->phone_number);
        $this->assertSame('ahmad@example.com', $student->email);
        $this->assertSame('L', $student->gender);
        $this->assertSame('siswa_melati_1', $student->belt_level);
        $this->assertSame('SMA', $student->education);
        $this->assertSame($program->id, $student->program_id);
        $this->assertNotNull($student->student_code);
        $this->assertNotNull($student->qr_token);
    }

    public function test_invalid_enum_values_are_rejected_with_validation_errors(): void
    {
        $testable = TestableImport::make(StudentImporter::class)
            ->import([
                'full_name' => 'Siti Aisyah',
                'gender' => 'X',
                'belt_level' => 'Sabuk Tidak Ada',
            ]);

        $this->assertArrayHasKey('gender', $testable->errors()->toArray());
        $this->assertArrayHasKey('belt_level', $testable->errors()->toArray());
    }
}
