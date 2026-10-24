@extends('layout.master')

@section('content')
    @php
        $home = $site['home'];
        $contact = $site['contact'];
        $hasWhatsapp = filled($site['whatsapp_url'] ?? null);
        $hasPhone = filled($contact['phone'] ?? null);
    @endphp

    <!-- Page Programs Start -->
    <div class="page-programs">
        <div class="container">
            @if ($branches->isNotEmpty())
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 align-items-center wow fadeInUp">
                            <span class="text-muted me-1">Cabang:</span>
                            <a href="{{ route('program') }}"
                               class="btn btn-sm {{ ! $activeBranch ? 'btn-primary' : 'btn-outline-primary' }}">
                                Semua
                            </a>
                            @foreach ($branches as $branch)
                                <a href="{{ route('program', ['branch' => $branch]) }}"
                                   class="btn btn-sm {{ $activeBranch === $branch ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $branch }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($programs->isEmpty())
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="wow fadeInUp">Belum ada program latihan yang tersedia.</p>
                        <a href="{{ route('contact-us') }}" class="btn-default mt-3">Tanyakan Program</a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($programs as $program)
                        <div class="col-xl-3 col-md-6">
                            @include('frontend.partials.program-card', [
                                'program' => $program,
                                'delay' => min($loop->index * 0.2, 1.4),
                            ])
                        </div>
                    @endforeach
                </div>
            @endif

            @if (! empty($home['pricing_note']) || filled($site['registration']['fee_label'] ?? null))
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="p-4 rounded-3 bg-light wow fadeInUp">
                            <h3 class="h5 mb-2">Informasi Biaya</h3>
                            @if (filled($site['registration']['fee_label'] ?? null))
                                <p class="mb-2">
                                    Biaya pendaftaran: <strong>{{ $site['registration']['fee_label'] }}</strong>
                                </p>
                            @endif
                            @if (! empty($home['pricing_note']))
                                <p class="mb-0 text-muted">{{ $home['pricing_note'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Page Programs End -->

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

    @if ($testimonials->isNotEmpty())
        <!-- Our Testimonials Section Start -->
        <div class="our-testimonials bg-section dark-section parallaxie">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Testimoni</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Pengalaman siswa dan orang tua</h2>
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
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Pertanyaan seputar program dan pendaftaran</h2>
                            </div>
                            <div class="our-faqs-btn wow fadeInUp" data-wow-delay="0.4s">
                                <a href="{{ route('faqs') }}" class="btn-default">Lihat Semua FAQ</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        @include('frontend.partials.faq-accordion', [
                            'faqs' => $faqs,
                            'accordionId' => 'program-faq-accordion',
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
            <div class="row align-items-center">
                <div class="col-xl-8">
                    <div class="our-appointment-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Pendaftaran</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $home['cta_heading'] }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $home['cta_description'] }}</p>
                        </div>
                        <div class="mt-4 wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ route('registration') }}" class="btn-default btn-highlighted">Daftar Sekarang</a>
                            @if ($hasWhatsapp)
                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-default ms-2">
                                    Chat WhatsApp
                                </a>
                            @elseif ($hasPhone)
                                <a href="tel:{{ $contact['phone_href'] }}" class="btn-default ms-2">Telepon Admin</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="text-center text-xl-end wow fadeInUp">
                        <a href="{{ route('contact-us') }}" class="btn-default">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA Section End -->
@endsection
