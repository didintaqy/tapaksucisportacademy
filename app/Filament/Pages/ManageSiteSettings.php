<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Services\SiteSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Web';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'site-settings';

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(app(SiteSettings::class)->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pengaturan Situs')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Identitas')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Identitas Akademi')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('identity.name')
                                            ->label('Nama Resmi')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('identity.short_name')
                                            ->label('Nama Pendek')
                                            ->maxLength(100),

                                        TextInput::make('identity.tagline')
                                            ->label('Tagline')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Textarea::make('identity.description')
                                            ->label('Deskripsi Singkat')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->columnSpanFull(),

                                        FileUpload::make('identity.logo')
                                            ->label('Logo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->helperText('Disarankan SVG/PNG transparan. Maksimal 2 MB.')
                                            ->columnSpanFull(),

                                        FileUpload::make('identity.favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(1024)
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml'])
                                            ->helperText('Ikon kecil browser. Maksimal 1 MB.'),

                                        FileUpload::make('identity.page_header_image')
                                            ->label('Foto Latar Header Halaman')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Dipakai pada header halaman selain beranda. Maksimal 5 MB.'),

                                        TextInput::make('identity.years_experience')
                                            ->label('Tahun Pengalaman')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Angka pada lingkaran merah di beranda, misalnya 15. Kosongkan untuk menyembunyikannya.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Kontak & Lokasi')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Informasi Kontak')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('contact.address')
                                            ->label('Alamat Lengkap')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Alamat lokasi latihan pusat.')
                                            ->columnSpanFull(),

                                        TextInput::make('contact.phone')
                                            ->label('Nomor Telepon')
                                            ->tel()
                                            ->maxLength(50),

                                        TextInput::make('contact.whatsapp')
                                            ->label('Nomor WhatsApp')
                                            ->tel()
                                            ->maxLength(50)
                                            ->helperText('Boleh diawali 0, 62, atau +62.'),

                                        TextInput::make('contact.email')
                                            ->label('Email Resmi')
                                            ->email()
                                            ->maxLength(255),

                                        Textarea::make('contact.hours')
                                            ->label('Jam Operasional')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Tulis satu baris per keterangan, misalnya: Senin–Jumat: 16.00–20.00'),

                                        FileUpload::make('contact.image')
                                            ->label('Foto Halaman Kontak')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Peta Google Maps')
                                    ->description('Isi link lokasi dan/atau kode embed. Peta hanya tampil jika embed valid.')
                                    ->columns(1)
                                    ->schema([
                                        TextInput::make('contact.maps_url')
                                            ->label('Link Google Maps')
                                            ->url()
                                            ->maxLength(2048)
                                            ->helperText('Contoh: https://maps.app.goo.gl/...'),

                                        Textarea::make('contact.maps_embed')
                                            ->label('Kode Embed / URL Embed')
                                            ->rows(4)
                                            ->maxLength(5000)
                                            ->helperText('Tempel seluruh kode <iframe> dari Google Maps, atau URL https://www.google.com/maps/embed?...'),
                                    ]),
                            ]),

                        Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Akun Resmi')
                                    ->description('Kosongkan akun yang tidak digunakan. Ikon hanya tampil jika URL diisi.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('social.facebook')->label('Facebook')->url()->maxLength(2048),
                                        TextInput::make('social.instagram')->label('Instagram')->url()->maxLength(2048),
                                        TextInput::make('social.youtube')->label('YouTube')->url()->maxLength(2048),
                                        TextInput::make('social.tiktok')->label('TikTok')->url()->maxLength(2048),
                                        TextInput::make('social.x')->label('X / Twitter')->url()->maxLength(2048),
                                    ]),
                            ]),

                        Tab::make('Beranda')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make('Hero')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('hero.eyebrow')
                                            ->label('Teks Kecil di Atas Judul')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('hero.title')
                                            ->label('Judul Hero')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Textarea::make('hero.description')
                                            ->label('Deskripsi Hero')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->columnSpanFull(),

                                        TextInput::make('hero.primary_label')
                                            ->label('Label Tombol Utama')
                                            ->maxLength(100),

                                        TextInput::make('hero.secondary_label')
                                            ->label('Label Tombol Kedua')
                                            ->maxLength(100),

                                        TextInput::make('hero.video_url')
                                            ->label('URL Video Latar (MP4)')
                                            ->url()
                                            ->maxLength(2048)
                                            ->helperText('Opsional. Jika kosong, dipakai foto hero.'),

                                        FileUpload::make('hero.image')
                                            ->label('Foto Hero')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(8192)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Dipakai jika URL video kosong. Maksimal 8 MB.'),
                                    ]),

                                Section::make('Section Tentang di Beranda')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.about_eyebrow')->label('Label Section')->maxLength(100),
                                        TextInput::make('home.about_title')->label('Judul Section')->maxLength(255),
                                        Textarea::make('home.about_description')->label('Deskripsi')->rows(3)->maxLength(1000)->columnSpanFull(),
                                    ]),

                                Section::make('Program')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.program_heading')->label('Judul Section')->maxLength(255),
                                        TextInput::make('home.program_description')->label('Deskripsi')->maxLength(500),
                                    ]),

                                Section::make('Keunggulan')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.why_heading')->label('Judul Section')->maxLength(255),
                                        TextInput::make('home.why_description')->label('Deskripsi')->maxLength(500),
                                        FileUpload::make('home.why_image_1')
                                            ->label('Foto Utama Section Keunggulan')
                                            ->image()
                                            ->disk('public')
                                            ->directory('why-choose')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Foto besar pertama di sisi kanan. Maksimal 5 MB.'),
                                        FileUpload::make('home.why_image_2')
                                            ->label('Foto Kedua Section Keunggulan')
                                            ->image()
                                            ->disk('public')
                                            ->directory('why-choose')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Foto kedua di sisi kanan. Maksimal 5 MB.'),
                                        Repeater::make('home.why_items')
                                            ->label('Daftar Keunggulan')
                                            ->schema([
                                                TextInput::make('title')->label('Judul')->required()->maxLength(150),
                                                Textarea::make('description')->label('Deskripsi')->rows(2)->maxLength(500),
                                                FileUpload::make('image')
                                                    ->label('Gambar Item')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('why-choose-items')
                                                    ->visibility('public')
                                                    ->maxSize(2048)
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                    ->helperText('Tampil sebagai ikon kecil di samping judul. Maksimal 2 MB.')
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->reorderable()
                                            ->orderColumn('sort')
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->addActionLabel('Tambah keunggulan')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Langkah Memulai')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.steps_heading')->label('Judul Section')->maxLength(255),
                                        TextInput::make('home.steps_description')->label('Deskripsi')->maxLength(500),
                                        Repeater::make('home.steps')
                                            ->label('Langkah')
                                            ->schema([
                                                TextInput::make('title')->label('Judul Langkah')->required()->maxLength(150),
                                                Textarea::make('description')->label('Deskripsi')->rows(2)->maxLength(500),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->reorderable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->addActionLabel('Tambah langkah')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Pelatih & Fitur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.team_heading')->label('Judul Section Pelatih')->maxLength(255),
                                        TextInput::make('home.team_description')->label('Deskripsi Section Pelatih')->maxLength(500),
                                        TextInput::make('home.features_heading')->label('Judul Section Keunggulan Latihan')->maxLength(255),
                                        TextInput::make('home.features_description')->label('Deskripsi Section Keunggulan Latihan')->maxLength(500),
                                        FileUpload::make('home.feature_image')
                                            ->label('Foto Section Keunggulan')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->columnSpanFull(),
                                        Repeater::make('home.features')
                                            ->label('Keunggulan Latihan')
                                            ->schema([
                                                TextInput::make('title')->label('Judul')->required()->maxLength(150),
                                                Textarea::make('description')->label('Deskripsi')->rows(2)->maxLength(500),
                                                TagsInput::make('points')->label('Poin')->placeholder('Tambah poin...'),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->reorderable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->addActionLabel('Tambah keunggulan')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Testimoni, FAQ, dan Blog')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.testimonials_heading')->label('Judul Testimoni')->maxLength(255),
                                        TextInput::make('home.testimonials_description')->label('Deskripsi Testimoni')->maxLength(500),
                                        TextInput::make('home.faq_heading')->label('Judul FAQ')->maxLength(255),
                                        TextInput::make('home.faq_description')->label('Deskripsi FAQ')->maxLength(500),
                                        TextInput::make('home.blog_heading')->label('Judul Blog')->maxLength(255),
                                        TextInput::make('home.blog_description')->label('Deskripsi Blog')->maxLength(500),
                                        TextInput::make('home.cta_heading')->label('Judul CTA')->maxLength(255),
                                        TextInput::make('home.cta_description')->label('Deskripsi CTA')->maxLength(500),
                                    ]),

                                Section::make('Gambar Latar Section')
                                    ->description('Gambar dekoratif di belakang section. Kosongkan untuk memakai gambar bawaan tema.')
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('home.testimonials_background')
                                            ->label('Latar Section Testimoni')
                                            ->image()
                                            ->disk('public')
                                            ->directory('backgrounds')
                                            ->visibility('public')
                                            ->maxSize(8192)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Beranda & halaman program.'),

                                        FileUpload::make('home.dark_section_background')
                                            ->label('Pola Latar Section Gelap')
                                            ->image()
                                            ->disk('public')
                                            ->directory('backgrounds')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Dipakai di semua section berlatar gelap.'),

                                        FileUpload::make('home.core_feature_background')
                                            ->label('Latar Ilustrasi Section Keunggulan')
                                            ->image()
                                            ->disk('public')
                                            ->directory('backgrounds')
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Kotak di belakang foto section keunggulan. Gunakan PNG transparan.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Tentang Kami')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Profil')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('about.eyebrow')->label('Label Section')->maxLength(100),
                                        TextInput::make('about.title')->label('Judul')->maxLength(255),
                                        Textarea::make('about.description')->label('Deskripsi')->rows(4)->maxLength(1500)->columnSpanFull(),
                                        Textarea::make('about.history')->label('Sejarah Singkat')->rows(5)->maxLength(3000)->helperText('Kosongkan jika belum ada. Section sejarah akan disembunyikan.')->columnSpanFull(),
                                        Textarea::make('about.vision')->label('Visi')->rows(3)->maxLength(1000),
                                        Textarea::make('about.mission')->label('Misi')->rows(3)->maxLength(1000),
                                        FileUpload::make('about.image')->label('Foto Utama')->image()->disk('public')->directory('site')->visibility('public')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                        FileUpload::make('about.video_image')->label('Foto Sampul Video')->image()->disk('public')->directory('site')->visibility('public')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                        TextInput::make('about.video_url')->label('URL Video Profil')->url()->maxLength(2048)->helperText('Opsional. YouTube atau video lain.'),
                                    ]),

                                Section::make('Nilai yang Dibina')
                                    ->schema([
                                        TextInput::make('about.values_heading')->label('Judul Section')->maxLength(255),
                                        Repeater::make('about.values')
                                            ->label('Nilai')
                                            ->schema([
                                                TextInput::make('title')->label('Judul')->required()->maxLength(150),
                                                Textarea::make('description')->label('Deskripsi')->rows(2)->maxLength(500),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->reorderable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->addActionLabel('Tambah nilai')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Program & Biaya')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Biaya Pendaftaran')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('registration.fee')
                                            ->label('Biaya Pendaftaran')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('Rp')
                                            ->helperText('Kosongkan jika belum ditetapkan; informasi biaya tidak akan tampil.'),

                                        Textarea::make('registration.fee_note')
                                            ->label('Catatan Biaya')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->columnSpanFull(),

                                        Textarea::make('registration.requirements')
                                            ->label('Persyaratan Pendaftaran')
                                            ->rows(4)
                                            ->maxLength(2000)
                                            ->helperText('Satu baris per persyaratan. Tampil di halaman pendaftaran bila diisi.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Section Program di Beranda')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('home.pricing_heading')->label('Judul Section')->maxLength(255),
                                        TextInput::make('home.pricing_description')->label('Deskripsi Section')->maxLength(500),
                                        Textarea::make('home.pricing_note')->label('Catatan Iuran')->rows(3)->maxLength(1000)->columnSpanFull(),
                                        TagsInput::make('home.pricing_benefits')
                                            ->label('Manfaat / Informasi Tambahan')
                                            ->placeholder('Tambah poin...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Meta Default')
                                    ->columns(1)
                                    ->schema([
                                        TextInput::make('seo.title')->label('Meta Title Default')->maxLength(255),
                                        Textarea::make('seo.description')->label('Meta Description Default')->rows(3)->maxLength(500),
                                        TextInput::make('seo.keywords')->label('Meta Keywords')->maxLength(500),
                                        FileUpload::make('seo.image')->label('Gambar Open Graph')->image()->disk('public')->directory('site')->visibility('public')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                    ]),
                            ]),

                        Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                Section::make('Footer')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('footer.description')->label('Deskripsi Footer')->rows(3)->maxLength(1000)->columnSpanFull(),
                                        TextInput::make('footer.cta_title')->label('Judul CTA Footer')->maxLength(255),
                                        TextInput::make('footer.cta_description')->label('Deskripsi CTA Footer')->maxLength(500),
                                        TextInput::make('footer.cta_label')->label('Label Tombol CTA')->maxLength(100),
                                        TextInput::make('footer.copyright')->label('Teks Hak Cipta')->maxLength(255)->helperText('Tahun dan nama situs ditambahkan otomatis.'),
                                    ]),

                                Section::make('Latar Footer')
                                    ->description('Gambar latar yang tampil di seluruh footer situs.')
                                    ->schema([
                                        FileUpload::make('footer.background_image')
                                            ->label('Foto Latar Footer')
                                            ->image()
                                            ->disk('public')
                                            ->directory('backgrounds')
                                            ->visibility('public')
                                            ->maxSize(8192)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Disarankan rasio lebar, minimal 1920px. Maksimal 8 MB.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        app(SiteSettings::class)->update($data);

        Notification::make()
            ->title('Pengaturan situs disimpan')
            ->body('Perubahan akan tampil di halaman publik pada kunjungan berikutnya.')
            ->success()
            ->send();
    }
}
