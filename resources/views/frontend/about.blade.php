@extends('layout.master')

@section('content')
    @php
        $about = $site['about'];
        $contact = $site['contact'];
        $hasPhone = filled($contact['phone'] ?? null);
        $hasWhatsapp = filled($site['whatsapp_url'] ?? null);
        $hasContact = $hasPhone || $hasWhatsapp || filled($contact['email'] ?? null) || filled($contact['address'] ?? null);
        $hasStats = ($stats['students'] ?? 0) > 0
            || ($stats['programs'] ?? 0) > 0
            || ($stats['coaches'] ?? 0) > 0
            || ($stats['testimonials'] ?? 0) > 0;
    @endphp

    <!-- About Us Section Start -->
    <div class="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6">
                    <div class="about-us-image-box wow fadeInUp">
                        <div class="about-us-image">
                            <figure class="image-anime">
                                <img src="{{ $site['assets']['about'] }}" alt="{{ $site['identity']['name'] }}">
                            </figure>
                        </div>

                        @if (filled($about['video_url'] ?? null))
                            <div class="about-us-info-video-box">
                                <div class="about-us-info-video-image">
                                    <figure>
                                        <img src="{{ $site['assets']['about_video'] }}" alt="">
                                    </figure>
                                    <div class="video-play-button">
                                        <a href="{{ $about['video_url'] }}" class="popup-video" data-cursor-text="Putar">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="about-us-info-video-content">
                                    <h3>Profil</h3>
                                    <p>{{ $site['identity']['short_name'] }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="about-us-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $about['eyebrow'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $about['title'] }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{!! nl2br(e($about['description'])) !!}</p>
                        </div>

                        @if (filled($about['vision']) || filled($about['mission']))
                            <div class="about-us-item-list wow fadeInUp" data-wow-delay="0.4s">
                                @if (filled($about['mission']))
                                    <div class="about-us-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-about-us-item-1.svg') }}" alt="">
                                        </div>
                                        <div class="about-us-item-content">
                                            <h3>Misi Kami</h3>
                                            <p>{!! nl2br(e($about['mission'])) !!}</p>
                                        </div>
                                    </div>
                                @endif

                                @if (filled($about['vision']))
                                    <div class="about-us-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-about-us-item-2.svg') }}" alt="">
                                        </div>
                                        <div class="about-us-item-content">
                                            <h3>Visi Kami</h3>
                                            <p>{!! nl2br(e($about['vision'])) !!}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="about-us-footer wow fadeInUp" data-wow-delay="0.6s">
                            <div class="about-us-btn">
                                <a href="{{ route('contact-us') }}" class="btn-default">Hubungi Kami</a>
                            </div>

                            @if ($hasPhone || $hasWhatsapp)
                                <div class="about-contact-box">
                                    <div class="icon-box">
                                        <img src="{{ asset('images/icon-phone-white.svg') }}" alt="">
                                    </div>
                                    <div class="about-contact-box-content">
                                        <p>Butuh bantuan?</p>
                                        <h3>
                                            @if ($hasWhatsapp)
                                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp</a>
                                            @else
                                                <a href="tel:{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a>
                                            @endif
                                        </h3>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About Us Section End -->

    @if (filled($about['history'] ?? null))
        <!-- History Section Start -->
        <div class="what-we-do bg-section">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Sejarah</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Perjalanan {{ $site['identity']['short_name'] }}</h2>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <p>{!! nl2br(e($about['history'])) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- History Section End -->
    @endif

    @if (! empty($about['values']))
        <!-- Values Section Start -->
        <div class="our-programs bg-section">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Nilai</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $about['values_heading'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($about['values'] as $value)
                        <div class="col-xl-4 col-md-6">
                            <div class="about-us-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 0.8) }}s">
                                <div class="icon-box">
                                    <img src="{{ asset('images/icon-about-us-item-'.(($loop->index % 2) + 1).'.svg') }}" alt="">
                                </div>
                                <div class="about-us-item-content">
                                    <h3>{{ $value['title'] ?? '' }}</h3>
                                    <p>{{ $value['description'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Values Section End -->
    @endif

    @if ($hasStats)
        <!-- Our Fact Section Start -->
        <div class="our-fact">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6">
                        <div class="our-fact-content">
                            <div class="section-title">
                                <h3 class="wow fadeInUp">Data Akademi</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Angka yang tumbuh bersama proses latihan</h2>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">
                                    Statistik berikut dihitung langsung dari data akademi dan tidak memuat data pribadi siswa.
                                </p>
                            </div>

                            <div class="our-fact-content-footer wow fadeInUp" data-wow-delay="0.6s">
                                <div class="our-fact-content-btn">
                                    <a href="{{ route('registration') }}" class="btn-default">Daftar Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="fact-counter-item-list wow fadeInUp">
                            @if (($stats['students'] ?? 0) > 0)
                                <div class="fact-counter-item">
                                    <div class="our-fact-counter-body">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-our-fact-1.svg') }}" alt="">
                                        </div>
                                        <div class="fact-counter-item-content">
                                            <h2><span class="counter">{{ $stats['students'] }}</span>+</h2>
                                            <p>Siswa terdaftar</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (($stats['programs'] ?? 0) > 0)
                                <div class="fact-counter-item active">
                                    <div class="our-fact-counter-body">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-our-fact-2.svg') }}" alt="">
                                        </div>
                                        <div class="fact-counter-item-content">
                                            <h2><span class="counter">{{ $stats['programs'] }}</span></h2>
                                            <p>Program latihan</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (($stats['coaches'] ?? 0) > 0)
                                <div class="fact-counter-item">
                                    <div class="our-fact-counter-body">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-our-fact-3.svg') }}" alt="">
                                        </div>
                                        <div class="fact-counter-item-content">
                                            <h2><span class="counter">{{ $stats['coaches'] }}</span></h2>
                                            <p>Pelatih aktif</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (($stats['testimonials'] ?? 0) > 0)
                                <div class="fact-counter-item">
                                    <div class="our-fact-counter-body">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-our-fact-4.svg') }}" alt="">
                                        </div>
                                        <div class="fact-counter-item-content">
                                            <h2><span class="counter">{{ $stats['testimonials'] }}</span></h2>
                                            <p>Testimoni disetujui</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Fact Section End -->
    @endif

    @if ($coaches->isNotEmpty())
        <!-- Our Team Section Start -->
        <div class="our-team bg-section">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Pelatih</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Pelatih yang mendampingi setiap tahap latihan</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($coaches as $coach)
                        <div class="col-xl-3 col-md-6">
                            @include('frontend.partials.coach-card', [
                                'coach' => $coach,
                                'delay' => min($loop->index * 0.2, 0.8),
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Our Team Section End -->
    @endif

    @if ($testimonials->isNotEmpty())
        <!-- Our Testimonials Section Start -->
        <div class="our-testimonials bg-section dark-section parallaxie">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Testimoni</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Pengalaman siswa, orang tua, dan alumni</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="testimonial-slider">
                            <div class="swiper">
                                <div class="swiper-wrapper" data-cursor-text="Geser">
                                    @foreach ($testimonials as $testimonial)
                                        <div class="swiper-slide">
                                            @include('frontend.partials.testimonial-card', ['testimonial' => $testimonial])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Testimonials Section End -->
    @endif

    @if ($faqs->isNotEmpty())
        <!-- Our Faqs Section Start -->
        <div class="our-faqs">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="our-faqs-content">
                            <div class="section-title">
                                <h3 class="wow fadeInUp">FAQ</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Pertanyaan yang sering diajukan</h2>
                            </div>
                            <div class="our-faqs-btn wow fadeInUp" data-wow-delay="0.4s">
                                <a href="{{ route('faqs') }}" class="btn-default">Lihat Semua FAQ</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        @include('frontend.partials.faq-accordion', [
                            'faqs' => $faqs,
                            'accordionId' => 'about-faq-accordion',
                        ])
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Faqs Section End -->
    @endif

    <!-- CTA Section Start -->
    <div class="our-appointment bg-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-7">
                    <div class="our-appointment-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $site['identity']['short_name'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $site['home']['cta_heading'] }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $site['home']['cta_description'] }}</p>
                        </div>
                        <div class="mt-4 wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ route('registration') }}" class="btn-default btn-highlighted">Daftar Sekarang</a>
                            @if ($hasWhatsapp)
                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-default ms-2">
                                    Chat WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($hasContact)
                    <div class="col-xl-5">
                        <div class="appointment-info-item-list wow fadeInUp" data-wow-delay="0.4s">
                            @if ($hasPhone)
                                <div class="appointment-info-item">
                                    <div class="icon-box">
                                        <img src="{{ asset('images/icon-phone-white.svg') }}" alt="">
                                    </div>
                                    <div class="appointment-info-item-content">
                                        <h3>Telepon</h3>
                                        <p><a href="tel:{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a></p>
                                    </div>
                                </div>
                            @endif

                            @if (filled($contact['email'] ?? null))
                                <div class="appointment-info-item">
                                    <div class="icon-box">
                                        <img src="{{ asset('images/icon-mail-white.svg') }}" alt="">
                                    </div>
                                    <div class="appointment-info-item-content">
                                        <h3>Email</h3>
                                        <p><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></p>
                                    </div>
                                </div>
                            @endif

                            @if (filled($contact['address'] ?? null))
                                <div class="appointment-info-item">
                                    <div class="icon-box">
                                        <img src="{{ asset('images/icon-location-white.svg') }}" alt="">
                                    </div>
                                    <div class="appointment-info-item-content">
                                        <h3>Lokasi Latihan</h3>
                                        <p>{{ $contact['address'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @if (filled($contact['hours'] ?? null))
                                <div class="appointment-info-item">
                                    <div class="icon-box">
                                        <img src="{{ asset('images/icon-clock-white.svg') }}" alt="">
                                    </div>
                                    <div class="appointment-info-item-content">
                                        <h3>Jam Operasional</h3>
                                        <p>{!! nl2br(e($contact['hours'])) !!}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- CTA Section End -->
@endsection
