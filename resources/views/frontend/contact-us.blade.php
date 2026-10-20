@extends('layout.master')

@section('title', 'Kontak Kami')

@section('hero')
    @include('layout.herosection.page-header', ['title' => 'Kontak Kami'])
@endsection

@section('content')
    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <!-- Contact Us Image Box Start -->
                    <div class="contact-us-image-box wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Contact Us Image Start -->
                        <div class="contact-us-image">
                            <figure class="image-anime">
                                <img src="{{ asset('images/contact-us-img.jpg') }}" alt="Kontak Kami" class="img-fluid">
                            </figure>
                        </div>
                        <!-- Contact Us Image End -->

                        <!-- Contact Info List Start -->
                        <div class="contact-info-list">
                            <!-- Contact Info Item Start  -->
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="contact-info-item-content">
                                    <h3>Telepon</h3>
                                    <p><a href="tel:+6281234567890">+62 812 3456 7890</a></p>
                                </div>
                            </div>
                            <!-- Contact Info Item End  -->

                            <!-- Contact Info Item Start  -->
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="contact-info-item-content">
                                    <h3>E-mail</h3>
                                    <p><a href="mailto:info@tapaksuci.academy">info@tapaksuci.academy</a></p>
                                </div>
                            </div>
                            <!-- Contact Info Item End  -->

                            <!-- Contact Info Item Start  -->
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="contact-info-item-content">
                                    <h3>Alamat</h3>
                                    <p>Jl. Contoh No. 123, Jakarta, Indonesia</p>
                                </div>
                            </div>
                            <!-- Contact Info Item End  -->
                        </div>
                        <!-- Contact Info List End -->
                    </div>
                    <!-- Contact Us Image Box End -->
                </div>

                <div class="col-xl-6">
                    <!-- Contact Us Form Start -->
                    <div class="contact-us-form">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Hubungi Kami</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Kirim pesan dan kami akan segera merespons</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Form Start -->
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
                        <!-- Contact Form End -->
                    </div>
                    <!-- Contact Us Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    <!-- Google Map Start -->
    <div class="google-map">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Lokasi Kami</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Tempat latihan terdekat untuk pelatihan karate profesional</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Google Map Start -->
                    <div class="google-map-iframe">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.31084089133!2d106.82719607476791!3d-6.175392493762236!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f2a6b6c6c6c7%3A0x1234567890abcdef!2sJl.%20Contoh%20No.%20123%2C%20Jakarta!5e0!3m2!1sen!2sid!4v1703158537552!5m2!1sen!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <!-- Google Map End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Google Map End -->
@endsection