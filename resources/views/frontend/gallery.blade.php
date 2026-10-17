@extends('layout.master')
@section('content')
    <!-- Photo Gallery Start -->
    <div class="page-gallery">
        <div class="container">
            @if ($galleries->isEmpty())
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="wow fadeInUp">Belum ada foto kegiatan yang dipublikasikan.</p>
                    </div>
                </div>
            @else
                <!-- gallery section start -->
                <div class="row gallery-items page-gallery-box">
                    @foreach ($galleries as $gallery)
                        <div class="col-lg-4 col-6">
                            <!-- Image Gallery start -->
                            <div class="photo-gallery wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 1.6) }}s">
                                <a href="{{ $gallery->image_url }}" data-cursor-text="View">
                                    <figure class="image-anime">
                                        <img src="{{ asset($gallery->image_url) }}" alt="{{ $gallery->title ?? 'Galeri Tapak Suci' }}">
                                    </figure>
                                </a>
                            </div>
                            <!-- Image Gallery end -->
                        </div>
                    @endforeach
                </div>
                <!-- gallery section end -->
            @endif
        </div>
    </div>
    <!-- Photo Gallery End -->
@endsection
