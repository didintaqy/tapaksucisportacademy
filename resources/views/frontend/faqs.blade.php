@extends('layout.master')

@section('title', 'FAQ')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'FAQ', 'breadcrumb' => 'FAQ'])
@endsection

@section('content')
    @php($groups = $faqs->groupBy('category'))

    <!-- Page Faqs Start -->
    <div class="page-faqs">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <!-- Page Single Sidebar Start -->
                    <div class="page-single-sidebar">
                        <!-- Page Category List Start -->
                        <div class="page-category-list wow fadeInUp">
                            <ul>
                                @foreach ($groups as $category => $items)
                                    <li><a href="#faq-{{ $loop->index }}">{{ $category }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Page Category List End -->

                        <!-- Sidebar CTA Box Start -->
                        <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
                            <div class="sidebar-cta-body">
                                <div class="sidebar-cta-logo">
                                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Tapak Suci">
                                </div>

                                <div class="sidebar-cta-content">
                                    <h3>Masih ada pertanyaan? Kami siap membantu.</h3>
                                </div>

                                <div class="sidebar-cta-btn">
                                    <a href="{{ route('contact-us') }}" class="btn-default btn-highlighted">Hubungi Kami</a>
                                </div>

                                <div class="sidebar-cta-contact-list">
                                    <ul>
                                        <li><img src="{{ asset('images/icon-phone-white.svg') }}" alt=""> Telepon: <a href="tel:+6281234567890">+62 812 3456 7890</a></li>
                                        <li><img src="{{ asset('images/icon-mail-white.svg') }}" alt=""> Email: <a href="mailto:info@tapaksuci.academy">info@tapaksuci.academy</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Sidebar CTA Box End -->
                    </div>
                    <!-- Page Single Sidebar End -->
                </div>

                <div class="col-lg-8">
                    <!-- Page FAQs Category Start -->
                    <div class="page-faqs-catagery">
                        @forelse ($groups as $category => $items)
                            <div class="page-single-faqs" id="faq-{{ $loop->index }}">
                                <div class="section-title">
                                    <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $category }}</h2>
                                </div>

                                <!-- FAQ Accordion Start -->
                                <div class="faq-accordion our-faq-accordion" id="accordion-{{ $loop->index }}">
                                    @foreach ($items as $faq)
                                        <div class="accordion-item wow fadeInUp">
                                            <h2 class="accordion-header" id="heading-{{ $faq->id }}">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $faq->id }}"
                                                    aria-expanded="false" aria-controls="collapse-{{ $faq->id }}">
                                                    {{ $faq->question }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $faq->id }}" class="accordion-collapse collapse"
                                                role="region" aria-labelledby="heading-{{ $faq->id }}"
                                                data-bs-parent="#accordion-{{ $loop->parent->index }}">
                                                <div class="accordion-body">
                                                    <p>{!! nl2br(e($faq->answer)) !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- FAQ Accordion End -->
                            </div>
                        @empty
                            <p>Belum ada FAQ yang ditampilkan.</p>
                        @endforelse
                    </div>
                    <!-- Page FAQs Category End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Faqs End -->
@endsection
