@extends('layout.master')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('hero')
    <div class="page-header bg-section dark-section parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">404</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">Halaman Tidak Ditemukan</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Page 404 Start -->
    <div class="page-404">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <h2 class="wow fadeInUp">Maaf, halaman yang Anda cari tidak tersedia.</h2>
                    <p class="wow fadeInUp" data-wow-delay="0.2s">
                        Periksa kembali URL Anda atau kunjungi halaman lain:
                    </p>

                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <a href="{{ route('home') }}" class="btn-default btn-highlighted">Kembali ke Home</a>
                            <a href="{{ route('faqs') }}" class="btn-default">Lihat FAQ</a>
                            <a href="{{ route('contact-us') }}" class="btn-default">Hubungi Kami</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page 404 End -->
@endsection