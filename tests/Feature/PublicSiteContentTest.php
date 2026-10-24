<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Enums\TestimonialStatus;
use App\Models\Category;
use App\Models\Coach;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Program;
use App\Models\Student;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteContentTest extends TestCase
{
    use RefreshDatabase;

    private function updateSettings(array $data): void
    {
        app(SiteSettings::class)->update($data);
    }

    public function test_homepage_renders_database_content_instead_of_template_copy(): void
    {
        $this->updateSettings([
            'identity' => ['name' => 'Akademi Uji Nusantara'],
            'hero' => ['title' => 'Judul Hero Dari Pengaturan', 'description' => 'Deskripsi hero dinamis.'],
            'contact' => [
                'phone' => '081200001111',
                'whatsapp' => '081200001111',
                'email' => 'halo@akademi.test',
                'address' => 'Jl. Latihan No. 10',
            ],
        ]);

        $program = Program::factory()->create([
            'name' => 'Program Dinamis',
            'monthly_fee' => 250000,
            'is_active' => true,
        ]);
        Coach::factory()->create(['name' => 'Pelatih Aktif', 'is_active' => true]);
        Coach::factory()->inactive()->create(['name' => 'Pelatih Nonaktif']);

        Testimonial::factory()->create([
            'name' => 'Siswa Disetujui',
            'status' => TestimonialStatus::Approved,
            'published_at' => now(),
        ]);
        Testimonial::factory()->create([
            'name' => 'Siswa Menunggu',
            'status' => TestimonialStatus::Pending,
        ]);

        Faq::factory()->create(['question' => 'Pertanyaan dinamis?', 'is_published' => true]);
        Faq::factory()->create(['question' => 'Pertanyaan draf?', 'is_published' => false]);

        $admin = User::factory()->admin()->create();
        Post::factory()->create([
            'title' => 'Artikel Dinamis',
            'status' => PostStatus::Published,
            'published_at' => now(),
            'author_id' => $admin->id,
            'category_id' => Category::factory()->create()->id,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Akademi Uji Nusantara')
            ->assertSee('Judul Hero Dari Pengaturan')
            ->assertSee('Program Dinamis')
            ->assertSee('Rp 250.000')
            ->assertSee('Pelatih Aktif')
            ->assertDontSee('Pelatih Nonaktif')
            ->assertSee('Siswa Disetujui')
            ->assertDontSee('Siswa Menunggu')
            ->assertSee('Pertanyaan dinamis?')
            ->assertDontSee('Pertanyaan draf?')
            ->assertSee('Artikel Dinamis')
            ->assertDontSee('Ethan Walker')
            ->assertDontSee('4.9')
            ->assertDontSee('4200 Review');
    }

    public function test_homepage_hides_sections_without_database_content(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Pelatih Kami')
            ->assertDontSee('Judul Testimoni')
            ->assertDontSee('Pertanyaan yang Sering Diajukan')
            ->assertDontSee('Artikel Terbaru');
    }

    public function test_contact_page_shows_dynamic_contact_and_only_renders_a_valid_map(): void
    {
        $this->updateSettings([
            'contact' => [
                'phone' => '081200003333',
                'whatsapp' => '081200003333',
                'email' => 'kontak@akademi.test',
                'address' => 'Jl. Contoh No. 5',
                'hours' => "Senin-Jumat: 16.00-20.00\nSabtu: 08.00-12.00",
                'maps_embed' => 'https://www.google.com/maps/embed?pb=valid',
                'maps_url' => 'https://maps.app.goo.gl/contoh',
            ],
        ]);

        $this->get(route('contact-us'))
            ->assertOk()
            ->assertSee('081200003333')
            ->assertSee('kontak@akademi.test')
            ->assertSee('Jl. Contoh No. 5')
            ->assertSee('Senin-Jumat: 16.00-20.00', false)
            ->assertSee('google.com/maps/embed?pb=valid', false)
            ->assertSee('Buka di Google Maps')
            ->assertDontSee('+62 812 3456 7890')
            ->assertDontSee('info@tapaksuci.academy')
            ->assertDontSee('Jl. Contoh No. 123, Jakarta');
    }

    public function test_contact_page_hides_map_when_embed_is_invalid(): void
    {
        $this->updateSettings([
            'contact' => [
                'maps_embed' => 'https://example.com/maps/embed?pb=invalid',
                'maps_url' => '',
            ],
        ]);

        $this->get(route('contact-us'))
            ->assertOk()
            ->assertDontSee('Lokasi Kami')
            ->assertDontSee('<iframe', false);
    }

    public function test_about_page_shows_profile_and_hides_empty_team_and_testimonial_sections(): void
    {
        $this->updateSettings([
            'identity' => ['name' => 'Akademi Uji'],
            'about' => [
                'description' => 'Profil akademi dari pengaturan.',
                'history' => 'Sejarah singkat akademi.',
                'vision' => 'Visi dari pengaturan.',
                'mission' => 'Misi dari pengaturan.',
                'values' => [
                    ['title' => 'Nilai Uji', 'description' => 'Deskripsi nilai uji.'],
                ],
            ],
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Profil akademi dari pengaturan.')
            ->assertSee('Sejarah singkat akademi.')
            ->assertSee('Visi dari pengaturan.')
            ->assertSee('Misi dari pengaturan.')
            ->assertSee('Nilai Uji')
            ->assertDontSee('Pelatih yang mendampingi setiap tahap latihan')
            ->assertDontSee('Pengalaman siswa, orang tua, dan alumni')
            ->assertDontSee('Ethan Walker');
    }

    public function test_registration_page_uses_registration_fee_and_requirements_from_settings(): void
    {
        $this->updateSettings([
            'registration' => [
                'fee' => 125000,
                'fee_note' => 'Termasuk seragam latihan.',
                'requirements' => "Fotokopi kartu keluarga\nPas foto 3x4",
            ],
        ]);

        $this->get(route('registration'))
            ->assertOk()
            ->assertSee('Rp 125.000')
            ->assertSee('Termasuk seragam latihan.')
            ->assertSee('Fotokopi kartu keluarga')
            ->assertSee('Pas foto 3x4')
            ->assertDontSee('+62 812 3456 7890');
    }

    public function test_controller_seo_is_not_overwritten_by_site_defaults(): void
    {
        $this->updateSettings([
            'seo' => [
                'title' => 'SEO Default Situs',
                'description' => 'Deskripsi default situs.',
            ],
        ]);

        $program = Program::factory()->create([
            'name' => 'Program SEO',
            'meta_title' => 'Judul SEO Program',
            'meta_description' => 'Deskripsi SEO program.',
            'is_active' => true,
        ]);

        $this->get(route('program.show', $program->slug))
            ->assertOk()
            ->assertSee('<title>Judul SEO Program</title>', false)
            ->assertSee('name="description" content="Deskripsi SEO program."', false)
            ->assertDontSee('<title>SEO Default Situs</title>', false);
    }

    public function test_public_pages_do_not_expose_student_personal_data(): void
    {
        Student::factory()->create([
            'full_name' => 'Siswa Rahasia',
            'nik' => '3171234567890001',
            'phone_number' => '081299998888',
            'email' => 'rahasia@siswa.test',
            'address' => 'Jl. Rahasia No. 99',
        ]);

        foreach ([route('home'), route('about')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee('Siswa Rahasia')
                ->assertDontSee('3171234567890001')
                ->assertDontSee('081299998888')
                ->assertDontSee('rahasia@siswa.test')
                ->assertDontSee('Jl. Rahasia No. 99');
        }
    }

    public function test_sitemap_does_not_include_admin_routes(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('home'))
            ->assertDontSee('/admin');
    }
}
