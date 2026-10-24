@extends('layout.master')

@section('hero')
    @include('layout.herosection.page-header', ['page' => $page, 'breadcrumb' => $breadcrumb ?? 'Kontak Kami'])
@endsection

@section('content')
    @php
        $contact = $site['contact'];
        $hasPhone = filled($contact['phone'] ?? null);
        $hasWhatsapp = filled($site['whatsapp_url'] ?? null);
        $hasEmail = filled($contact['email'] ?? null);
        $hasAddress = filled($contact['address'] ?? null);
        $hasHours = filled($contact['hours'] ?? null);
        $hasMap = filled($site['map_embed_url'] ?? null) || filled($site['map_url'] ?? null);
    @endphp

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="contact-us-image-box wow fadeInUp" data-wow-delay="0.2s">
                        @if (filled($site['assets']['contact'] ?? null))
                            <div class="contact-us-image">
                                <figure class="image-anime">
                                    <img src="{{ $site['assets']['contact'] }}" alt="Kontak {{ $site['identity']['name'] }}" class="img-fluid">
                                </figure>
                            </div>
                        @endif

                        @if ($hasPhone || $hasWhatsapp || $hasEmail || $hasAddress || $hasHours)
                            <div class="contact-info-list">
                                @if ($hasPhone)
                                    <div class="contact-info-item">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="contact-info-item-content">
                                            <h3>Telepon</h3>
                                            <p><a href="tel:{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a></p>
                                        </div>
                                    </div>
                                @endif

                                @if ($hasWhatsapp)
                                    <div class="contact-info-item">
                                        <div class="icon-box">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </div>
                                        <div class="contact-info-item-content">
                                            <h3>WhatsApp</h3>
                                            <p>
                                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">
                                                    {{ $contact['whatsapp'] }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if ($hasEmail)
                                    <div class="contact-info-item">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="contact-info-item-content">
                                            <h3>E-mail</h3>
                                            <p><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></p>
                                        </div>
                                    </div>
                                @endif

                                @if ($hasAddress)
                                    <div class="contact-info-item">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="contact-info-item-content">
                                            <h3>Alamat</h3>
                                            <p>{!! nl2br(e($contact['address'])) !!}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($hasHours)
                                    <div class="contact-info-item">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>
                                        <div class="contact-info-item-content">
                                            <h3>Jam Operasional</h3>
                                            <p>{!! nl2br(e($contact['hours'])) !!}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="contact-us-form">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Hubungi Kami</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Kirim pesan dan kami akan segera merespons</h2>
                        </div>

                        <div class="contact-form">
                            <form id="contactForm" action="{{ route('contact-us.store') }}" method="POST" class="wow fadeInUp" data-wow-delay="0.2s">
                                @csrf
                                <div class="position-absolute" style="left: -9999px;" aria-hidden="true">
                                    <label for="website">Website</label>
                                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Nama Anda" required value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email Anda" required value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Nomor Telepon" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <label for="message" class="form-label">Pesan <span class="text-danger">*</span></label>
                                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" id="message" rows="5" placeholder="Tulis pesan Anda di sini..." required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="contact-form-btn">
                                            <button type="submit" class="btn-default"><span>Kirim Pesan</span></button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if (session('success'))
                                <div class="alert alert-success mt-4" role="alert">
                                    <i class="fa-solid fa-circle-check me-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    @if ($hasMap)
        <!-- Google Map Start -->
        <div class="google-map">
            <div class="container">
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Lokasi Kami</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">
                                Lokasi latihan {{ $site['identity']['name'] }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if (filled($site['map_embed_url'] ?? null))
                            <div class="google-map-iframe map-embed-responsive wow fadeInUp">
                                <iframe src="{{ $site['map_embed_url'] }}"
                                        allowfullscreen
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        title="Peta lokasi {{ $site['identity']['name'] }}"></iframe>
                            </div>
                        @endif

                        @if (filled($site['map_url'] ?? null))
                            <div class="text-center mt-4 wow fadeInUp">
                                <a href="{{ $site['map_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-default">
                                    Buka di Google Maps
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Google Map End -->
    @endif
@endsection
