<div class="hero hero-video bg-section dark-section parallaxie"
     style="background-image: url('{{ $site['assets']['hero'] }}');">
    @if (filled($site['hero']['video_url'] ?? null))
        <!-- Selfhosted Video Start -->
        <div class="hero-bg-video">
            <video autoplay muted loop playsinline id="myvideo">
                <source src="{{ $site['hero']['video_url'] }}" type="video/mp4">
            </video>
        </div>
        <!-- Selfhosted Video End -->
    @endif

    <div class="container">
        <div class="row align-items-end">
            <div class="col-xl-8">
                <!-- Hero Content Start -->
                <div class="hero-content">
                    <div class="section-title">
                        @if (filled($site['hero']['eyebrow'] ?? null))
                            <h3 class="wow fadeInUp">{{ $site['hero']['eyebrow'] }}</h3>
                        @endif
                        <h1 class="text-anime-style-3" data-cursor="-opaque">{{ $site['hero']['title'] }}</h1>
                        @if (filled($site['hero']['description'] ?? null))
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $site['hero']['description'] }}</p>
                        @endif
                    </div>

                    <div class="hero-btn wow fadeInUp" data-wow-delay="0.4s">
                        <a href="{{ route('registration') }}" class="btn-default btn-highlighted">
                            {{ $site['hero']['primary_label'] ?: 'Daftar Sekarang' }}
                        </a>
                        @if (filled($site['hero']['secondary_label'] ?? null))
                            <a href="{{ route('program') }}" class="btn-default ms-2">
                                {{ $site['hero']['secondary_label'] }}
                            </a>
                        @endif
                    </div>
                </div>
                <!-- Hero Content End -->
            </div>

            <div class="col-xl-4">
                @if (($stats['students'] ?? 0) > 0 || ($stats['coaches'] ?? 0) > 0)
                    <!-- Hero Info Box Start -->
                    <div class="hero-info-box wow fadeInUp">
                        @if (($stats['students'] ?? 0) > 0)
                            <div class="hero-info-item hero-info-client-box mb-3">
                                <div class="hero-client-box-content">
                                    <h2><span class="counter">{{ $stats['students'] }}</span>+</h2>
                                    <h3>Siswa terdaftar</h3>
                                </div>
                                <div class="hero-client-box-btn mt-3">
                                    <a href="{{ route('about') }}" aria-label="Tentang kami">
                                        <img src="{{ asset('images/arrow-white.svg') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if (($stats['coaches'] ?? 0) > 0)
                            <div class="hero-info-item hero-info-client-box">
                                <div class="hero-client-box-content">
                                    <h2><span class="counter">{{ $stats['coaches'] }}</span></h2>
                                    <h3>Pelatih aktif</h3>
                                </div>
                                <div class="hero-client-box-btn mt-3">
                                    <a href="{{ route('contact-us') }}" aria-label="Hubungi kami">
                                        <img src="{{ asset('images/arrow-white.svg') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- Hero Info Box End -->
                @endif
            </div>
        </div>
    </div>
</div>
