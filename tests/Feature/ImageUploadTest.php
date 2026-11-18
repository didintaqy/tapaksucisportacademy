<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Student;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regresi jalur upload foto: optimasi ukuran, berkas non-raster,
 * dan penyajian berkas publik saat symlink storage tidak tersedia.
 */
class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_student_photo_is_resized_and_compressed(): void
    {
        $source = $this->makeJpeg(2000, 1500);
        Storage::disk('public')->put('students/foto.jpg', file_get_contents($source));
        @unlink($source);

        Student::factory()->create(['photo' => 'students/foto.jpg']);

        $path = Storage::disk('public')->path('students/foto.jpg');
        [$width, $height] = getimagesize($path);

        $this->assertLessThanOrEqual(800, $width);
        $this->assertLessThanOrEqual(800, $height);
        $this->assertLessThan(2000, $width);
    }

    public function test_certificate_pdf_is_stored_without_image_processing(): void
    {
        Storage::disk('public')->put('achievements/piagam.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n");

        $achievement = Achievement::factory()->create([
            'certificate_file' => 'achievements/piagam.pdf',
        ]);

        $this->assertTrue($achievement->exists);
        $this->assertTrue(Storage::disk('public')->exists('achievements/piagam.pdf'));
        $this->assertStringStartsWith('%PDF', Storage::disk('public')->get('achievements/piagam.pdf'));
    }

    public function test_storage_fallback_serves_public_files(): void
    {
        Storage::disk('public')->put('site/logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $this->get('/storage/site/logo.svg')->assertOk();
    }

    public function test_storage_fallback_rejects_missing_and_traversal_paths(): void
    {
        $this->get('/storage/site/tidak-ada.png')->assertNotFound();
        $this->get('/storage/..%2F.env')->assertNotFound();
    }

    public function test_site_setting_image_is_optimized_after_save(): void
    {
        $source = $this->makeJpeg(2500, 1500);
        Storage::disk('public')->put('site/hero.jpg', file_get_contents($source));
        @unlink($source);

        $settings = app(SiteSettings::class);
        $data = $settings->all();
        data_set($data, 'hero.image', 'site/hero.jpg');

        $settings->update($data);

        [$width, $height] = getimagesize(Storage::disk('public')->path('site/hero.jpg'));

        $this->assertLessThanOrEqual(1920, $width);
        $this->assertLessThanOrEqual(1920, $height);
        $this->assertLessThan(2500, $width);
    }

    private function makeJpeg(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 120, 200));

        $path = tempnam(sys_get_temp_dir(), 'upload-test').'.jpg';
        imagejpeg($image, $path, 90);
        imagedestroy($image);

        return $path;
    }
}
