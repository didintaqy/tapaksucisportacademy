@extends('layout.master')

@section('title', 'Testimoni')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'Testimoni', 'breadcrumb' => 'Testimoni'])
@endsection

@section('content')
    <!-- Page Testimonials Start -->
    <div class="page-testimonials">
        <div class="container">
            @if ($testimonials->isEmpty())
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Belum ada testimoni yang ditampilkan.</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($testimonials as $testimonial)
                        <div class="col-xl-4 col-md-6">
                            <!-- Testimonial Item Start -->
                            <div class="testimonial-item wow fadeInUp" data-wow-delay="{{ ($loop->index % 3) * 0.2 }}s">
                                <div class="testimonial-item-header">
                                    <div class="testimonial-author">
                                        <div class="testimonial-author-image">
                                            <figure class="image-anime">
                                                <img src="{{ asset('images/author-'.(($loop->index % 3) + 1).'.jpg') }}"
                                                    alt="{{ $testimonial->name }}">
                                            </figure>
                                        </div>
                                        <div class="testimonial-author-content">
                                            <h3>{{ $testimonial->name }}</h3>
                                            <p>{{ $testimonial->relation ?: 'Anggota Tapak Suci' }}</p>
                                        </div>
                                    </div>
                                    <div class="testimonial-item-quote">
                                        <img src="{{ asset('images/testimonial-item-quote.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="testimonial-item-body">
                                    <div class="testimonial-rating">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <i class="fa-solid fa-star{{ $star <= $testimonial->rating ? '' : ' text-muted' }}"></i>
                                        @endfor
                                    </div>

                                    <div class="testimonial-item-body-content">
                                        <p>&ldquo;{{ $testimonial->content }}&rdquo;</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Testimonial Item End -->
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row section-row">
                <div class="col-xl-8 offset-xl-2">
                    <!-- Testimonial Form Start -->
                    <div class="contact-us-form">
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Bagikan Pengalaman Anda</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Testimoni akan tampil setelah disetujui admin</h2>
                        </div>

                        <div class="contact-form">
                            <form action="{{ route('testimonials.store') }}" method="POST" class="wow fadeInUp">
                                @csrf
                                <div class="position-absolute" style="left: -9999px;" aria-hidden="true">
                                    <label for="website">Website</label>
                                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nama Anda" required value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label for="relation" class="form-label">Keterangan</label>
                                        <input type="text" name="relation" id="relation"
                                            class="form-control @error('relation') is-invalid @enderror"
                                            placeholder="Contoh: Orang Tua Siswa" value="{{ old('relation') }}">
                                        @error('relation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                        <select name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" required>
                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                <option value="{{ $rating }}" @selected((int) old('rating', 5) === $rating)>
                                                    {{ $rating }} bintang
                                                </option>
                                            @endfor
                                        </select>
                                        @error('rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <label for="content" class="form-label">Testimoni <span class="text-danger">*</span></label>
                                        <textarea name="content" id="content" rows="5"
                                            class="form-control @error('content') is-invalid @enderror"
                                            placeholder="Tulis pengalaman Anda selama berlatih..." required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="contact-form-btn">
                                            <button type="submit" class="btn-default"><span>Kirim Testimoni</span></button>
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

                            @if (session('error'))
                                <div class="alert alert-danger mt-4" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Testimonial Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Testimonials End -->
@endsection
