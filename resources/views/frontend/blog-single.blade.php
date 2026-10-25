@extends('layout.master')

@section('hero')
    <!-- Page Header Start -->
    <div class="page-header bg-section dark-section parallaxie"
         style="background-image: url('{{ $site['assets']['page_header'] }}');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">{{ $post->title }}</h1>
                        <div class="post-single-meta wow fadeInUp">
                            <ol class="breadcrumb">
                                <li><i class="fa-regular fa-user"></i> {{ $post->author_name }}</li>
                                <li><i class="fa-regular fa-clock"></i> {{ optional($post->published_at)->translatedFormat('d M, Y') }}</li>
                            </ol>
                        </div>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
@endsection

@section('content')
    @include('partials.seo-json-ld', ['post' => $post])
    <!-- Page Single Post Start -->
    <div class="page-single-post">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @if ($post->cover_url)
                        <!-- Post Featured Image Start -->
                        <div class="post-image">
                            <figure class="image-anime reveal">
                                <img src="{{ $post->cover_url }}" alt="{{ $post->title }}">
                            </figure>
                        </div>
                        <!-- Post Featured Image End -->
                    @endif

                    <!-- Post Single Content Start -->
                    <div class="post-content">
                        <!-- Post Entry Start -->
                        <div class="post-entry">
                            {!! $post->content !!}
                        </div>
                        <!-- Post Entry End -->

                        @if ($post->category || $post->tags->isNotEmpty())
                            <!-- Post Tag Links Start -->
                            <div class="post-tag-links">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <!-- Post Tags Start -->
                                        <div class="post-tags wow fadeInUp" data-wow-delay="0.5s">
                                            @if ($post->category)
                                                <span class="tag-links">
                                                    Kategori:
                                                    <a href="{{ route('blog', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                                                </span>
                                            @endif
                                            @if ($post->tags->isNotEmpty())
                                                <span class="tag-links d-block mt-2">
                                                    Tag:
                                                    @foreach ($post->tags as $tag)
                                                        <a href="{{ route('blog', ['tag' => $tag->slug]) }}">{{ $tag->name }}</a>@if (! $loop->last), @endif
                                                    @endforeach
                                                </span>
                                            @endif
                                        </div>
                                        <!-- Post Tags End -->
                                    </div>

                                    <div class="col-lg-4">
                                        <!-- Post Social Links Start -->
                                        <div class="post-social-sharing wow fadeInUp" data-wow-delay="0.5s">
                                            <ul>
                                                <li>
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                                       target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke Facebook">
                                                        <i class="fa-brands fa-facebook-f"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                                                       target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke X">
                                                        <i class="fa-brands fa-x-twitter"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="https://wa.me/?text={{ urlencode($post->title.' '.request()->url()) }}"
                                                       target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke WhatsApp">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                                                       target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke Telegram">
                                                        <i class="fa-brands fa-telegram"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- Post Social Links End -->
                                    </div>
                                </div>
                            </div>
                            <!-- Post Tag Links End -->
                        @endif
                    </div>
                    <!-- Post Single Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Single Post End -->
@endsection
