<?php

namespace Tests\Feature;

use App\Filament\Resources\Students\Pages\EditStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresi: foto lama harus tetap terlihat dan tidak terhapus saat form
 * edit dibuka lalu disimpan tanpa mengganti foto.
 */
class ImageEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_missing_photo_file_does_not_erase_stored_path(): void
    {
        // Simulasi berkas hilang/menumpuk di server lain: path tersimpan
        // tetap harus dipertahankan agar data tidak rusak saat disimpan.
        $student = Student::factory()->create(['photo' => 'students/sudah-tidak-ada.jpg']);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertFormSet(['photo' => 'students/sudah-tidak-ada.jpg'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('students/sudah-tidak-ada.jpg', $student->fresh()->photo);
    }

    public function test_legacy_theme_photo_path_is_not_dropped_when_editing(): void
    {
        // Path aset tema lama (public/images), bukan file di disk publik.
        $student = Student::factory()->create(['photo' => 'images/team-1.jpg']);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertFormSet(['photo' => 'images/team-1.jpg'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('images/team-1.jpg', $student->fresh()->photo);
    }
}
