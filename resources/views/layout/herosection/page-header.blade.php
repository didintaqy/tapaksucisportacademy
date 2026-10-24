    <!-- Page Header Start -->
    <div class="page-header bg-section dark-section parallaxie"
         style="background-image: url('{{ $site['assets']['page_header'] }}');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">{{ $page ?? 'Halaman' }}</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $breadcrumb ?? $page ?? 'Halaman' }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
