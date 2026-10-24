@extends('layout.master')
@section('content')
    <!-- Page Blog Section Start -->
    <div class="page-blog">
        <div class="container">
            @if ($categories->isNotEmpty() || $activeTag)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 align-items-center wow fadeInUp">
                            <span class="text-muted me-1">Filter:</span>
                            <a href="{{ route('blog') }}"
                               class="btn btn-sm {{ ! $activeCategory && ! $activeTag ? 'btn-primary' : 'btn-outline-primary' }}">
                                Semua
                            </a>
                            @foreach ($categories as $category)
                                <a href="{{ route('blog', ['category' => $category->slug]) }}"
                                   class="btn btn-sm {{ $activeCategory?->id === $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                            @if ($activeTag)
                                <span class="badge bg-secondary ms-2">Tag: {{ $activeTag->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($posts->isEmpty())
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="wow fadeInUp">Belum ada artikel yang dipublikasikan.</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($posts as $post)
                        <div class="col-xl-4 col-md-6">
                            <div class="post-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 1.6) }}s">
                                <!-- Post Featured Image Start-->
                                <div class="post-featured-image">
                                    <a href="{{ route('blog.show', $post->slug) }}" data-cursor-text="View">
                                        <figure class="image-anime">
                                            <img src="{{ $post->cover_url ?: $site['assets']['page_header'] }}" alt="{{ $post->title }}">
                                        </figure>
                                    </a>
                                </div>
                                <!-- Post Featured Image End -->

                                <!-- Post Item Body Start -->
                                <div class="post-item-body">
                                    <!-- Post Item Meta Start -->
                                    <div class="post-item-meta">
                                        <ul>
                                            <li><img src="{{ asset('images/icon-date.svg') }}" alt="">{{ $post->published_at?->translatedFormat('d M Y') }}</li>
                                            <li><img src="{{ asset('images/icon-person.svg') }}" alt="">{{ $post->author_name }}</li>
                                        </ul>
                                    </div>
                                    <!-- Post Item Meta End -->

                                    <!-- Post Item Content -->
                                    <div class="post-item-content">
                                        @if ($post->category)
                                            <a href="{{ route('blog', ['category' => $post->category->slug]) }}"
                                               class="badge bg-primary text-decoration-none mb-2">{{ $post->category->name }}</a>
                                        @endif
                                        <h2><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                                    </div>
                                    <!-- Post Item End -->

                                    <!-- Post Readmore Button Start-->
                                    <div class="post-item-btn">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="readmore-btn">Baca selengkapnya</a>
                                    </div>
                                    <!-- Post Readmore Button End-->
                                </div>
                                <!-- Post Item Body End -->
                            </div>
                        </div>
                    @endforeach

                    <div class="col-lg-12">
                        @include('frontend.partials.pagination', ['paginator' => $posts])
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Page Blog Section End -->
@endsection
