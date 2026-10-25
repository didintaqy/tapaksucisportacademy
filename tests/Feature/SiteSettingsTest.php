<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): SiteSettings
    {
        return app(SiteSettings::class);
    }

    public function test_defaults_are_available_without_a_database_row(): void
    {
        $settings = $this->settings();

        $this->assertSame('Tapak Suci Sport Academy', $settings->get('identity.name'));
        $this->assertSame('', $settings->get('contact.phone'));
        $this->assertNull($settings->viewData()['map_embed_url']);
        $this->assertSame([], $settings->viewData()['social_links']);
    }

    public function test_update_persists_data_and_refreshes_cache(): void
    {
        $settings = $this->settings();

        $this->assertSame('Tapak Suci Sport Academy', $settings->get('identity.name'));

        $settings->update([
            'identity' => ['name' => 'Akademi Uji'],
            'contact' => ['whatsapp' => '0812-3456-7890'],
        ]);

        $this->assertDatabaseHas('site_settings', ['id' => 1]);
        $this->assertSame('Akademi Uji', $this->settings()->get('identity.name'));
        $this->assertSame('https://wa.me/6281234567890', $this->settings()->viewData()['whatsapp_url']);

        Cache::forget(SiteSettings::CACHE_KEY);
        $this->settings()->flushMemory();

        $this->assertSame('Akademi Uji', $this->settings()->get('identity.name'));
    }

    public function test_map_embed_accepts_a_full_google_maps_iframe(): void
    {
        $this->settings()->update([
            'contact' => [
                'maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=abc123" width="600" height="450"></iframe>',
            ],
        ]);

        $this->assertSame(
            'https://www.google.com/maps/embed?pb=abc123',
            $this->settings()->viewData()['map_embed_url'],
        );
    }

    public function test_map_embed_rejects_non_google_or_non_maps_urls(): void
    {
        $this->settings()->update([
            'contact' => ['maps_embed' => 'https://example.com/maps/embed?pb=abc'],
        ]);
        $this->assertNull($this->settings()->viewData()['map_embed_url']);

        $this->settings()->update([
            'contact' => ['maps_embed' => 'https://www.google.com/search?q=jakarta'],
        ]);
        $this->assertNull($this->settings()->viewData()['map_embed_url']);

        $this->settings()->update([
            'contact' => ['maps_embed' => 'javascript:alert(1)//maps'],
        ]);
        $this->assertNull($this->settings()->viewData()['map_embed_url']);
    }

    public function test_whatsapp_url_normalizes_local_and_international_numbers(): void
    {
        $settings = $this->settings();

        $this->assertSame('https://wa.me/6281234567890', $settings->whatsappUrl('0812 3456 7890'));
        $this->assertSame('https://wa.me/6281234567890', $settings->whatsappUrl('+62 812 3456 7890'));
        $this->assertSame('https://wa.me/6281234567890', $settings->whatsappUrl('81234567890'));
        $this->assertNull($settings->whatsappUrl('123'));
        $this->assertNull($settings->whatsappUrl(''));
    }

    public function test_media_url_handles_theme_assets_storage_paths_and_external_urls(): void
    {
        $settings = $this->settings();

        $this->assertStringEndsWith('/images/logo.svg', $settings->mediaUrl('images/logo.svg'));
        $this->assertStringContainsString('/storage/site/logo.png', $settings->mediaUrl('site/logo.png'));
        $this->assertSame('https://cdn.example.com/logo.png', $settings->mediaUrl('https://cdn.example.com/logo.png'));
        $this->assertSame('https://fallback.test/logo.svg', $settings->mediaUrl(null, 'https://fallback.test/logo.svg'));
    }

    public function test_view_data_exposes_derived_contact_and_fee_values(): void
    {
        $this->settings()->update([
            'contact' => [
                'phone' => '+62 812 0000 1111',
                'maps_url' => 'https://maps.app.goo.gl/contoh',
                'maps_embed' => 'https://www.google.com/maps/embed?pb=abc',
            ],
            'registration' => ['fee' => 150000],
            'social' => [
                'instagram' => 'https://instagram.com/tapaksuci',
                'facebook' => 'bukan-url',
            ],
        ]);

        $data = $this->settings()->viewData();

        $this->assertSame('+6281200001111', $data['contact']['phone_href']);
        $this->assertSame('https://maps.app.goo.gl/contoh', $data['map_url']);
        $this->assertSame('https://www.google.com/maps/embed?pb=abc', $data['map_embed_url']);
        $this->assertSame('Rp 150.000', $data['registration']['fee_label']);
        $this->assertCount(1, $data['social_links']);
        $this->assertSame('instagram', $data['social_links'][0]['key']);
    }

    public function test_why_choose_images_follow_the_uploaded_settings(): void
    {
        $settings = $this->settings();

        $this->assertStringEndsWith('/images/why-choose-image-1.jpg', $settings->viewData()['assets']['why_1']);

        $settings->update([
            'home' => [
                'why_image_1' => 'why-choose/utama.jpg',
                'why_image_2' => 'https://cdn.example.com/kedua.jpg',
                'why_items' => [
                    ['title' => 'Satu', 'image' => 'why-choose-items/satu.jpg'],
                    ['title' => 'Dua', 'image' => ''],
                ],
            ],
        ]);

        $assets = $this->settings()->viewData()['assets'];

        $this->assertStringContainsString('/storage/why-choose/utama.jpg', $assets['why_1']);
        $this->assertSame('https://cdn.example.com/kedua.jpg', $assets['why_2']);
        $this->assertStringContainsString('/storage/why-choose-items/satu.jpg', $assets['why_item_1']);
        $this->assertStringEndsWith('/images/why-choose-item-img-2.jpg', $assets['why_item_2']);
    }

    public function test_background_variables_use_uploaded_images_and_skip_empty_ones(): void
    {
        $settings = $this->settings();

        $this->assertStringEndsWith('/images/footer-bg.jpg', $settings->viewData()['assets']['footer_bg']);

        $settings->update([
            'footer' => ['background_image' => 'backgrounds/footer.jpg'],
            'home' => [
                'testimonials_background' => 'https://cdn.example.com/testimoni.jpg',
                'dark_section_background' => 'backgrounds/pola.png',
            ],
        ]);

        $assets = $this->settings()->viewData()['assets'];

        $this->assertStringContainsString('/storage/backgrounds/footer.jpg', $assets['footer_bg']);
        $this->assertSame('https://cdn.example.com/testimoni.jpg', $assets['testimonials_bg']);
        $this->assertStringContainsString('/storage/backgrounds/pola.png', $assets['dark_section_bg']);
        $this->assertStringEndsWith('/images/core-feature-image-bg.png', $assets['core_feature_bg']);
    }

    public function test_layout_renders_background_css_variables(): void
    {
        $this->settings()->update([
            'footer' => ['background_image' => 'backgrounds/footer.jpg'],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('--bg-footer:', false);
        $response->assertSee('--bg-hero:', false);
    }

    public function test_site_setting_model_clears_the_cache_when_saved(): void
    {
        SiteSetting::query()->create(['data' => ['identity' => ['name' => 'Pertama']]]);

        $this->settings()->flushMemory();
        $this->assertSame('Pertama', $this->settings()->get('identity.name'));

        SiteSetting::query()->find(1)->update(['data' => ['identity' => ['name' => 'Kedua']]]);

        $this->settings()->flushMemory();
        $this->assertSame('Kedua', $this->settings()->get('identity.name'));
    }
}
