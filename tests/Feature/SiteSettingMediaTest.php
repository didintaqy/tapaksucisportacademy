<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Services\SiteSettingMedia;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingMediaTest extends TestCase
{
    use RefreshDatabase;

    private function media(): SiteSettingMedia
    {
        return app(SiteSettingMedia::class);
    }

    public function test_referenced_paths_only_include_managed_directories(): void
    {
        app(SiteSettings::class)->update([
            'identity' => [
                'logo' => 'site/logo.png',
                'favicon' => 'images/favicon.png',
            ],
            'hero' => ['image' => 'https://cdn.example.com/hero.jpg'],
            'about' => ['image' => 'bukan-direktori/terkelola.jpg'],
            'footer' => ['background_image' => 'backgrounds/footer.jpg'],
            'home' => [
                'why_items' => [
                    ['image' => 'why-choose-items/a.jpg'],
                    ['image' => ''],
                ],
            ],
        ]);

        $paths = $this->media()->referencedPaths();

        $this->assertSame([
            'backgrounds/footer.jpg',
            'site/logo.png',
            'why-choose-items/a.jpg',
        ], $paths);
    }

    public function test_is_prunable_path_rejects_external_absolute_and_traversal_paths(): void
    {
        $media = $this->media();

        $this->assertTrue($media->isPrunablePath('site/logo.png'));
        $this->assertTrue($media->isPrunablePath('backgrounds/footer.jpg'));

        $this->assertFalse($media->isPrunablePath(null));
        $this->assertFalse($media->isPrunablePath(''));
        $this->assertFalse($media->isPrunablePath('images/logo.svg'));
        $this->assertFalse($media->isPrunablePath('https://cdn.example.com/a.jpg'));
        $this->assertFalse($media->isPrunablePath('//cdn.example.com/a.jpg'));
        $this->assertFalse($media->isPrunablePath('/etc/passwd'));
        $this->assertFalse($media->isPrunablePath('C:\\Windows\\logo.png'));
        $this->assertFalse($media->isPrunablePath('site/../../.env'));
        $this->assertFalse($media->isPrunablePath('gallery/a.jpg'));
    }

    public function test_prune_removes_only_orphaned_files_in_managed_directories(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        app(SiteSettings::class)->update([
            'identity' => ['logo' => 'site/logo.png'],
            'footer' => ['background_image' => 'backgrounds/footer.jpg'],
        ]);

        Storage::disk('public')->put('site/logo.png', 'x');
        Storage::disk('public')->put('site/lama.png', 'x');
        Storage::disk('public')->put('backgrounds/footer.jpg', 'x');
        Storage::disk('public')->put('backgrounds/lama.jpg', 'x');
        Storage::disk('public')->put('why-choose/yatim.jpg', 'x');
        Storage::disk('public')->put('site/.gitignore', "*\n!\.gitignore\n");

        // Direktori milik fitur lain tidak boleh disentuh.
        Storage::disk('public')->put('gallery/milik-galeri.jpg', 'x');

        $removed = $this->media()->prune();

        $this->assertSame([
            'backgrounds/lama.jpg',
            'site/lama.png',
            'why-choose/yatim.jpg',
        ], $removed);

        Storage::disk('public')->assertExists('site/logo.png');
        Storage::disk('public')->assertExists('backgrounds/footer.jpg');
        Storage::disk('public')->assertExists('site/.gitignore');
        Storage::disk('public')->assertExists('gallery/milik-galeri.jpg');

        Storage::disk('public')->assertMissing('site/lama.png');
        Storage::disk('public')->assertMissing('backgrounds/lama.jpg');
        Storage::disk('public')->assertMissing('why-choose/yatim.jpg');
    }

    public function test_prune_dry_run_does_not_delete_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('site/yatim.png', 'x');

        $removed = $this->media()->prune(dryRun: true);

        $this->assertSame(['site/yatim.png'], $removed);
        Storage::disk('public')->assertExists('site/yatim.png');
    }

    public function test_prune_moves_orphans_to_quarantine_instead_of_deleting(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/yatim.png', 'isi-gambar');

        $removed = $this->media()->prune();

        $this->assertSame(['site/yatim.png'], $removed);
        Storage::disk('public')->assertMissing('site/yatim.png');

        $batches = $this->media()->quarantinedBatches();
        $this->assertCount(1, $batches);

        $batch = array_key_first($batches);
        $this->assertSame(['site/yatim.png'], $batches[$batch]);
        Storage::disk('local')->assertExists(
            SiteSettingMedia::TRASH_DIRECTORY.'/'.$batch.'/site/yatim.png',
        );
        $this->assertSame(
            'isi-gambar',
            Storage::disk('local')->get(SiteSettingMedia::TRASH_DIRECTORY.'/'.$batch.'/site/yatim.png'),
        );
    }

    public function test_prune_can_delete_permanently_without_quarantine(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/yatim.png', 'x');

        $this->media()->prune(quarantine: false);

        Storage::disk('public')->assertMissing('site/yatim.png');
        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_restore_returns_a_quarantined_file_to_the_public_disk(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/lama.png', 'isi');
        $this->media()->prune();

        $restored = $this->media()->restore(['site/lama.png']);

        $this->assertSame(['site/lama.png'], $restored);
        Storage::disk('public')->assertExists('site/lama.png');
        $this->assertSame('isi', Storage::disk('public')->get('site/lama.png'));
        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_restore_refuses_paths_outside_the_quarantine_or_managed_directories(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/lama.png', 'x');
        $this->media()->prune();

        $this->assertSame([], $this->media()->restore(['../../.env']));
        $this->assertSame([], $this->media()->restore(['gallery/a.jpg']));
        $this->assertSame([], $this->media()->restore(['site/tidak-ada.png']));
    }

    public function test_restore_does_not_overwrite_an_existing_referenced_file(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/lama.png', 'versi-karantina');
        $this->media()->prune();

        // Berkas dengan nama sama muncul kembali dan sedang dipakai.
        Storage::disk('public')->put('site/lama.png', 'versi-aktif');

        $this->assertSame([], $this->media()->restore(['site/lama.png']));
        $this->assertSame('versi-aktif', Storage::disk('public')->get('site/lama.png'));
    }

    public function test_empty_trash_removes_all_quarantined_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/a.png', 'x');
        Storage::disk('public')->put('backgrounds/b.jpg', 'x');
        $this->media()->prune();

        $this->assertSame(2, $this->media()->emptyTrash());
        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_prune_trash_removes_only_batches_older_than_the_retention(): void
    {
        Storage::fake('local');

        $old = now()->subDays(40)->format('Ymd-His');
        $recent = now()->subDays(5)->format('Ymd-His');

        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$old.'/site/lama.png', 'x');
        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$recent.'/site/baru.png', 'x');

        $deleted = $this->media()->pruneTrash(30);

        $this->assertSame([$old], $deleted);

        $remaining = $this->media()->quarantinedBatches();
        $this->assertArrayHasKey($recent, $remaining);
        $this->assertArrayNotHasKey($old, $remaining);
    }

    public function test_prune_trash_keeps_batches_with_unrecognised_folder_names(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/manual-backup/site/a.png', 'x');

        $this->assertSame([], $this->media()->pruneTrash(1));
        $this->assertArrayHasKey('manual-backup', $this->media()->quarantinedBatches());
    }

    public function test_prune_trash_respects_dry_run(): void
    {
        Storage::fake('local');

        $old = now()->subDays(90)->format('Ymd-His');
        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$old.'/site/a.png', 'x');

        $this->assertSame([$old], $this->media()->pruneTrash(30, dryRun: true));
        $this->assertArrayHasKey($old, $this->media()->quarantinedBatches());
    }

    public function test_prune_trash_with_zero_days_disables_retention(): void
    {
        Storage::fake('local');

        $old = now()->subDays(365)->format('Ymd-His');
        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$old.'/site/a.png', 'x');

        $this->assertSame([], $this->media()->pruneTrash(0));
        $this->assertArrayHasKey($old, $this->media()->quarantinedBatches());
    }

    public function test_prune_command_honours_keep_days_option(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $old = now()->subDays(10)->format('Ymd-His');
        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$old.'/site/a.png', 'x');

        $this->artisan('site-settings:prune-media --keep-days=7')
            ->expectsOutputToContain('kedaluwarsa')
            ->assertSuccessful();

        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_prune_command_falls_back_to_config_retention(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        config(['site_settings.trash_retention_days' => 3]);

        $old = now()->subDays(10)->format('Ymd-His');
        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/'.$old.'/site/a.png', 'x');

        $this->artisan('site-settings:prune-media')->assertSuccessful();

        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_list_shows_trash_size_and_retention(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put(SiteSettingMedia::TRASH_DIRECTORY.'/20260101-000000/site/a.png', '12345');

        $this->artisan('site-settings:prune-media --list')
            ->expectsOutputToContain('1 berkas')
            ->expectsOutputToContain('Retensi')
            ->assertSuccessful();
    }

    public function test_saving_settings_removes_the_previous_image_file(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/lama.png', 'x');

        app(SiteSettings::class)->update([
            'identity' => ['logo' => 'site/lama.png'],
        ]);

        // Filament menaruh berkas baru lebih dulu, lalu menyimpan state.
        Storage::disk('public')->put('site/baru.png', 'x');

        app(SiteSettings::class)->update([
            'identity' => ['logo' => 'site/baru.png'],
        ]);

        Storage::disk('public')->assertMissing('site/lama.png');
        Storage::disk('public')->assertExists('site/baru.png');

        // Data menunjuk ke berkas yang benar.
        $this->assertSame('site/baru.png', SiteSetting::query()->find(1)?->data['identity']['logo']);
    }

    public function test_prune_command_reports_and_deletes_orphans(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('backgrounds/yatim.jpg', 'x');

        $this->artisan('site-settings:prune-media --dry-run')
            ->expectsOutputToContain('backgrounds/yatim.jpg')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('backgrounds/yatim.jpg');

        $this->artisan('site-settings:prune-media')
            ->expectsOutputToContain('backgrounds/yatim.jpg')
            ->assertSuccessful();

        Storage::disk('public')->assertMissing('backgrounds/yatim.jpg');
    }

    public function test_prune_command_succeeds_when_nothing_is_orphaned(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->artisan('site-settings:prune-media')
            ->expectsOutputToContain('Tidak ada gambar yatim')
            ->assertSuccessful();
    }

    public function test_prune_command_quarantines_by_default_and_lists_it(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/yatim.png', 'x');

        $this->artisan('site-settings:prune-media')
            ->expectsOutputToContain('mengarantina')
            ->assertSuccessful();

        Storage::disk('public')->assertMissing('site/yatim.png');

        $this->artisan('site-settings:prune-media --list')
            ->expectsOutputToContain('site/yatim.png')
            ->assertSuccessful();
    }

    public function test_prune_command_permanent_flag_skips_quarantine(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/yatim.png', 'x');

        $this->artisan('site-settings:prune-media --permanent')
            ->expectsOutputToContain('menghapus permanen')
            ->assertSuccessful();

        $this->assertSame([], $this->media()->quarantinedBatches());
    }

    public function test_prune_command_restores_a_quarantined_file(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/yatim.png', 'isi');
        $this->artisan('site-settings:prune-media')->assertSuccessful();

        $this->artisan('site-settings:prune-media --restore=site/yatim.png')
            ->expectsOutputToContain('dipulihkan')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('site/yatim.png');
        $this->assertSame('isi', Storage::disk('public')->get('site/yatim.png'));
    }

    public function test_prune_command_restore_fails_for_unknown_path(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->artisan('site-settings:prune-media --restore=site/tidak-ada.png')
            ->expectsOutputToContain('Tidak ada berkas yang dipulihkan')
            ->assertFailed();
    }

    public function test_prune_command_empties_the_trash(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        Storage::disk('public')->put('site/a.png', 'x');
        Storage::disk('public')->put('backgrounds/b.jpg', 'x');
        $this->artisan('site-settings:prune-media')->assertSuccessful();

        $this->artisan('site-settings:prune-media --empty-trash')
            ->expectsOutputToContain('2 berkas dihapus permanen')
            ->assertSuccessful();

        $this->assertSame([], $this->media()->quarantinedBatches());
    }
}
