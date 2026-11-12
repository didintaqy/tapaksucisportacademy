@php
    $hoursLines = collect(preg_split('/\r\n|\r|\n/', (string) ($site['contact']['hours'] ?? '')))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();
    $hasContactInfo = filled($site['contact']['phone'] ?? null)
        || filled($site['contact']['email'] ?? null)
        || filled($site['contact']['address'] ?? null);
    $copyright = trim((string) ($site['footer']['copyright'] ?? ''));
@endphp

<div class="main-footer bg-section dark-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Footer Header Start -->
                <div class="footer-header">
                    <div class="footer-header-content">
                        <h3>{{ $site['footer']['cta_title'] }}</h3>
                        <p>{{ $site['footer']['cta_description'] }}</p>
                    </div>
                    <div class="footer-header-btn">
                        <a href="{{ route('registration') }}" class="btn-default btn-highlighted">
                            {{ $site['footer']['cta_label'] ?: 'Daftar Sekarang' }}
                        </a>
                    </div>
                </div>
                <!-- Footer Header End -->
            </div>

            <div class="col-lg-4">
                <!-- About Footer Start -->
                <div class="about-footer">
                    <div class="footer-logo">
                        <img src="{{ $site['assets']['logo'] }}" alt="{{ $site['identity']['name'] }}">
                    </div>

                    <div class="about-footer-content">
                        <p>{{ $site['footer']['description'] }}</p>
                    </div>

                    @if (! empty($site['social_links']))
                        <div class="footer-social-icons">
                            <ul>
                                @foreach ($site['social_links'] as $social)
                                    <li>
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                           aria-label="{{ $social['label'] }}">
                                            <i class="fa-brands {{ $social['icon'] }}"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <!-- About Footer End -->
            </div>

            <div class="col-lg-8">
                <div class="footer-links-box">
                    <div class="footer-links">
                        <h3>Tautan Cepat</h3>
                        <ul>
                            <li><a href="{{ route('home') }}">Beranda</a></li>
                            <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                            <li><a href="{{ route('program') }}">Program</a></li>
                            <li><a href="{{ route('schedules') }}">Jadwal Latihan</a></li>
                            <li><a href="{{ route('blog') }}">Blog</a></li>
                            <li><a href="{{ route('gallery') }}">Galeri</a></li>
                            <li><a href="{{ route('achievements') }}">Prestasi Atlet</a></li>
                            <li><a href="{{ route('testimonials') }}">Testimoni</a></li>
                            <li><a href="{{ route('faqs') }}">FAQ</a></li>
                            <li><a href="{{ route('calendar') }}">Kalender Kegiatan</a></li>
                            <li><a href="{{ route('student.portal') }}">Portal Siswa</a></li>
                            <li><a href="{{ route('registration') }}">Pendaftaran</a></li>
                            <li><a href="{{ route('contact-us') }}">Kontak</a></li>
                        </ul>
                    </div>

                    @if ($hoursLines->isNotEmpty())
                        <div class="footer-links footer-schedule-box">
                            <h3>Jam Operasional</h3>
                            @foreach ($hoursLines as $line)
                                <div class="footer-schedule-item">
                                    <p>{{ $line }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($hasContactInfo || filled($site['whatsapp_url'] ?? null))
                        <div class="footer-links footer-contact-box">
                            <h3>Informasi Kontak</h3>

                            @if (filled($site['contact']['phone'] ?? null))
                                <div class="footer-contact-item">
                                    <p>Telepon:</p>
                                    <p><a href="tel:{{ $site['contact']['phone_href'] }}">{{ $site['contact']['phone'] }}</a></p>
                                </div>
                            @endif

                            @if (filled($site['whatsapp_url'] ?? null))
                                <div class="footer-contact-item">
                                    <p>WhatsApp:</p>
                                    <p><a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">{{ $site['contact']['whatsapp'] }}</a></p>
                                </div>
                            @endif

                            @if (filled($site['contact']['email'] ?? null))
                                <div class="footer-contact-item">
                                    <p>Email:</p>
                                    <p><a href="mailto:{{ $site['contact']['email'] }}">{{ $site['contact']['email'] }}</a></p>
                                </div>
                            @endif

                            @if (filled($site['contact']['address'] ?? null))
                                <div class="footer-contact-item">
                                    <p>Alamat:</p>
                                    <p>{{ $site['contact']['address'] }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright Start -->
    <div class="footer-copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer-copyright-text">
                        <p>
                            &copy; {{ now()->year }} {{ $site['identity']['name'] }}.
                            {{ $copyright }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Copyright End -->
</div>
