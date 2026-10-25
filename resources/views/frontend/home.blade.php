@extends('layout.master')

@section('content')
    @php
        $home = $site['home'];
        $about = $site['about'];
        $contact = $site['contact'];
        $hasPhone = filled($contact['phone'] ?? null);
        $hasWhatsapp = filled($site['whatsapp_url'] ?? null);
        $hasContact = $hasPhone || $hasWhatsapp || filled($contact['email'] ?? null) || filled($contact['address'] ?? null);
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

                        @if (filled($about['video_url'] ?? null) || ($stats['students'] ?? 0) > 0)
                            <div class="about-us-info-video-box">
                                @if (filled($about['video_url'] ?? null))
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
                                @endif

                                @if (($stats['students'] ?? 0) > 0)
                                    <div class="about-us-info-video-content">
                                        <h3>{{ number_format($stats['students'], 0, ',', '.') }}+</h3>
                                        <p>Siswa terdaftar</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="about-us-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $home['about_eyebrow'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['about_title'] }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $home['about_description'] }}</p>
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
                                            <p>{{ $about['mission'] }}</p>
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
                                            <p>{{ $about['vision'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="about-us-footer wow fadeInUp" data-wow-delay="0.6s">
                            <div class="about-us-btn">
                                <a href="{{ route('about') }}" class="btn-default">Selengkapnya</a>
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
                                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">Hubungi via WhatsApp</a>
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

    @if ($programs->isNotEmpty())
        <!-- Our Programs Section Start -->
        <div class="our-programs bg-section">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">{{ $home['program_heading'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['program_description'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($programs->take(4) as $program)
                        <div class="col-xl-3 col-md-6">
                            @include('frontend.partials.program-card', [
                                'program' => $program,
                                'delay' => min($loop->index * 0.2, 0.6),
                            ])
                        </div>
                    @endforeach

                    <div class="col-lg-12">
                        <div class="section-footer-text wow fadeInUp" data-wow-delay="0.4s">
                            <p>
                                Lihat seluruh program dan biaya latihan
                                <a href="{{ route('program') }}">di halaman program</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Programs Section End -->
    @endif

    @if (! empty($home['why_items']))
        <!-- Why Choose Us Section Start -->
        <div class="why-choose-us">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6">
                        <!-- Why Choose Us Content Start -->
                        <div class="why-choose-us-content">
                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h3 class="wow fadeInUp">{{ $home['why_heading'] }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['why_description'] }}</h2>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $home['why_subdescription'] ?? '' }}</p>
                            </div>
                            <!-- Section Title End -->

                            <!-- Why Choose Item List Start -->
                            <div class="why-choose-item-list wow fadeInUp" data-wow-delay="0.4s">
                                @foreach (array_slice($home['why_items'], 0, 3) as $index => $item)
                                    <!-- Why Choose Item Start -->
                                    <div class="why-choose-item">
                                        <!-- Why Choose Item Image Start -->
                                        <div class="why-choose-item-image">
                                            <figure class="image-anime">
                                                <img src="{{ $item['image'] ?? ($index === 0 ? $site['assets']['why_item_1'] : $site['assets']['why_item_2']) }}" alt="">
                                            </figure>
                                        </div>
                                        <!-- Why Choose Item Image End -->

                                        <!-- Why Choose Item Content Start -->
                                        <div class="why-choose-item-content">
                                            <h3>{{ $item['title'] ?? '' }}</h3>
                                            <p>{{ $item['description'] ?? '' }}</p>
                                        </div>
                                        <!-- Why Choose Item Content End -->
                                    </div>
                                    <!-- Why Choose Item End -->
                                @endforeach
                            </div>
                            <!-- Why Choose Item List End -->

                            <!-- Why Choose Us Footer Start -->
                            <div class="why-choose-us-footer wow fadeInUp" data-wow-delay="0.6s">
                                <!-- Why Choose Us Button Start -->
                                <div class="why-choose-btn">
                                    <a href="{{ route('registration') }}" class="btn-default">{{ $home['why_cta_label'] ?? 'Daftar Sekarang' }}</a>
                                </div>
                                <!-- Why Choose Us Button End -->

                                @if (filled(data_get($home, 'why_video_url')))
                                    <!-- Why Choose Intro Video Start -->
                                    <div class="why-choose-intro-video">
                                        <!-- Video Play Button Start -->
                                        <div class="video-play-button">
                                            <a href="{{ data_get($home, 'why_video_url') }}" class="popup-video" data-cursor-text="Putar">
                                                <i class="fa-solid fa-play"></i>
                                            </a>
                                        </div>
                                        <!-- Video Play Button End -->

                                        <!-- Why Choose Intro Video Content Start -->
                                        <div class="why-choose-intro-video-content">
                                            <h3>{{ $home['why_video_label'] ?? 'Intro Video' }}</h3>
                                        </div>
                                        <!-- Why Choose Intro Video Content End -->
                                    </div>
                                    <!-- Why Choose Intro Video End -->
                                @endif
                            </div>
                            <!-- Why Choose Us Footer End -->
                        </div>
                        <!-- Why Choose Us Content End -->
                    </div>

                    <div class="col-xl-6">
                        <!-- Why Choose Images Start -->
                        <div class="why-choose-images wow fadeInUp" data-wow-delay="0.2s">
                            <!-- Why Choose Image 1 Start -->
                            <div class="why-choose-img-1">
                                <figure class="image-anime">
                                    <img src="{{ $site['assets']['why_1'] }}" alt="">
                                </figure>
                            </div>
                            <!-- Why Choose Image 1 End -->

                            <!-- Why Choose Image 2 Start -->
                            <div class="why-choose-img-2">
                                <figure class="image-anime">
                                    <img src="{{ $site['assets']['why_2'] }}" alt="">
                                </figure>

                                @if (filled($site['identity']['years_experience'] ?? null))
                                    <!-- Contact Us Circle Start -->
                                    <div class="years-experience-circle">
                                        <a href="{{ route('contact-us') }}">
                                            <img src="{{ asset('images/years-experience-circle.svg') }}" alt="">
                                        </a>
                                    </div>
                                    <!-- Contact Us Circle End -->
                                @endif
                            </div>
                            <!-- Why Choose Image 2 End -->
                        </div>
                        <!-- Why Choose Images End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Why Choose Us Section End -->
    @endif

    @if (! empty($home['steps']))
        <!-- How It Work Section Start -->
        <div class="how-it-work bg-section dark-section parallaxie">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6">
                        <div class="how-it-work-image-box wow fadeInUp">
                            <div class="how-works-image-item-1">
                                <div class="how-works-image-1">
                                    <figure class="image-anime">
                                        <img src="{{ $site['assets']['feature'] }}" alt="">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="how-it-work-content">
                            <div class="section-title section-title-center">
                                <h3 class="wow fadeInUp">{{ $home['steps_heading'] }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['steps_description'] }}</h2>
                            </div>

                            <div class="how-it-work-steps">
                                @foreach ($home['steps'] as $step)
                                    <div class="how-work-step-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 0.8) }}s">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-how-it-work-step-'.(($loop->index % 4) + 1).'.svg') }}" alt="">
                                        </div>
                                        <div class="how-work-step-item-content">
                                            <h3>{{ $loop->iteration }}. {{ $step['title'] ?? '' }}</h3>
                                            <p>{{ $step['description'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- How It Work Section End -->
    @endif

    @if ($coaches->isNotEmpty())
        <!-- Our Team Section Start -->
        <div class="our-team">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">{{ $home['team_heading'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['team_description'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($coaches->take(4) as $coach)
                        <div class="col-xl-3 col-md-6">
                            @include('frontend.partials.coach-card', [
                                'coach' => $coach,
                                'delay' => min($loop->index * 0.2, 0.6),
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Our Team Section End -->
    @endif

    @if (! empty($home['features']))
        <!-- Core Feature Section Start -->
        <div class="core-feature bg-section dark-section parallaxie">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="core-feature-content">
                            <div class="section-title">
                                <h3 class="wow fadeInUp">{{ $home['features_heading'] }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['features_description'] }}</h2>
                            </div>

                            <div class="core-feature-item-list">
                                @foreach ($home['features'] as $feature)
                                    <div class="core-feature-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 0.8) }}s">
                                        <div class="core-feature-item-header">
                                            <div class="icon-box">
                                                <img src="{{ asset('images/icon-core-feature-'.(($loop->index % 2) + 1).'.svg') }}" alt="">
                                            </div>
                                            <div class="core-feature-item-content">
                                                <h3>{{ $feature['title'] ?? '' }}</h3>
                                                <p>{{ $feature['description'] ?? '' }}</p>
                                            </div>
                                        </div>

                                        @if (! empty($feature['points']))
                                            <div class="core-feature-item-body">
                                                <ul>
                                                    @foreach ($feature['points'] as $point)
                                                        <li>{{ $point }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="core-feature-footer wow fadeInUp" data-wow-delay="0.6s">
                                <div class="core-feature-content-btn">
                                    <a href="{{ route('registration') }}" class="btn-default btn-highlighted">Daftar Sekarang</a>
                                </div>

                                @if ($hasWhatsapp)
                                    <div class="about-contact-box">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-phone-white.svg') }}" alt="">
                                        </div>
                                        <div class="about-contact-box-content">
                                            <p>Butuh bantuan?</p>
                                            <h3><a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp</a></h3>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="core-feature-image">
                            <figure>
                                <img src="{{ $site['assets']['feature'] }}" alt="">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Core Feature Section End -->
    @endif

    @if ($programs->isNotEmpty())
        <!-- Our Pricing Section Start -->
        <div class="our-pricing">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">{{ $home['pricing_heading'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['pricing_description'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($programs->take(3) as $program)
                        <div class="col-xl-4 col-md-6">
                            <div class="pricing-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 0.6) }}s">
                                <div class="pricing-item-header">
                                    <div class="pricing-item-title-box">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-pricing-'.(($loop->index % 3) + 1).'.svg') }}" alt="">
                                        </div>
                                        <div class="pricing-item-title">
                                            <h3>{{ $program->name }}</h3>
                                        </div>
                                    </div>
                                    <div class="pricing-item-price">
                                        <h2>{{ $program->monthly_fee_label }} <sub>/ bulan</sub></h2>
                                    </div>
                                    <div class="pricing-item-content">
                                        <p>{{ $program->short_description }}</p>
                                    </div>
                                </div>

                                <div class="pricing-item-body">
                                    <div class="pricing-item-list">
                                        <h3>Informasi program:</h3>
                                        <ul>
                                            <li>Usia: {{ $program->age_range_label }}</li>
                                            <li>Durasi: {{ $program->duration_label }}</li>
                                            @if (! empty($program->branches))
                                                <li>Cabang: {{ implode(', ', $program->branches) }}</li>
                                            @endif
                                            <li>Tingkat: {{ $program->level?->getLabel() ?? '—' }}</li>
                                        </ul>
                                    </div>
                                    <div class="pricing-item-btn">
                                        <a href="{{ route('registration', ['program' => $program->id]) }}" class="btn-default">Daftar Program Ini</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if (! empty($home['pricing_benefits']))
                        <div class="col-lg-12">
                            <div class="pricing-benefit-list wow fadeInUp" data-wow-delay="0.4s">
                                <ul>
                                    @foreach ($home['pricing_benefits'] as $benefit)
                                        <li>
                                            <img src="{{ asset('images/icon-pricing-benefit-'.(($loop->index % 3) + 1).'.svg') }}" alt="">
                                            {{ $benefit }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (filled($home['pricing_note'] ?? null))
                        <div class="col-lg-12 mt-4">
                            <p class="text-center text-muted mb-0">{{ $home['pricing_note'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Our Pricing Section End -->
    @endif

    @if ($testimonials->isNotEmpty())
        <!-- Our Testimonials Section Start -->
        <div class="our-testimonials bg-section dark-section parallaxie">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">{{ $home['testimonials_heading'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['testimonials_description'] }}</h2>
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

                    <div class="col-lg-12">
                        <div class="section-footer-text wow fadeInUp" data-wow-delay="0.2s">
                            <p>
                                Bagikan pengalaman latihan Anda
                                <a href="{{ route('testimonials') }}">di halaman testimoni</a>.
                            </p>
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
                                <h3 class="wow fadeInUp">{{ $home['faq_heading'] }}</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['faq_description'] }}</h2>
                            </div>

                            <div class="our-faqs-btn wow fadeInUp" data-wow-delay="0.6s">
                                <a href="{{ route('faqs') }}" class="btn-default">Lihat Semua FAQ</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        @include('frontend.partials.faq-accordion', [
                            'faqs' => $faqs,
                            'accordionId' => 'home-faq-accordion',
                        ])
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Faqs Section End -->
    @endif

    <!-- Our CTA Section Start -->
    <div class="our-appointment bg-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-7">
                    <div class="our-appointment-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $site['identity']['short_name'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['cta_heading'] }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $home['cta_description'] }}</p>
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
    <!-- Our CTA Section End -->

    @if ($posts->isNotEmpty())
        <!-- Our Blog Section Start -->
        <div class="our-blog">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">{{ $home['blog_heading'] }}</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['blog_description'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($posts as $post)
                        <div class="col-xl-4 col-md-6">
                            <div class="post-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 0.6) }}s">
                                <div class="post-featured-image">
                                    <a href="{{ route('blog.show', $post->slug) }}" data-cursor-text="Baca">
                                        <figure class="image-anime">
                                            <img src="{{ $post->cover_url ?: $site['assets']['page_header'] }}" alt="{{ $post->title }}">
                                        </figure>
                                    </a>
                                </div>
                                <div class="post-item-body">
                                    <div class="post-item-meta">
                                        <ul>
                                            <li>
                                                <img src="{{ asset('images/icon-date.svg') }}" alt="">
                                                {{ $post->published_at?->translatedFormat('d M Y') }}
                                            </li>
                                            <li>
                                                <img src="{{ asset('images/icon-person.svg') }}" alt="">
                                                {{ $post->author_name }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="post-item-content">
                                        @if ($post->category)
                                            <span class="badge bg-primary mb-2">{{ $post->category->name }}</span>
                                        @endif
                                        <h2><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                                    </div>
                                    <div class="post-item-btn">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="readmore-btn">Baca selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="col-lg-12 mt-3">
                        <div class="section-footer-text text-center">
                            <p><a href="{{ route('blog') }}">Lihat semua artikel</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Blog Section End -->
    @endif
@endsection
