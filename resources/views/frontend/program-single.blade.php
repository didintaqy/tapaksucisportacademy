@extends('layout.master')

@section('hero')
    <!-- Page Header Start -->
    <div class="page-header bg-section dark-section parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">{{ $program->name }}</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('program') }}">Program</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $program->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
@endsection

@section('content')
    <!-- Page Program Single Start -->
    <div class="page-program-single">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="page-single-sidebar">
                        <!-- Program List Start -->
                        <div class="page-category-list wow fadeInUp">
                            <h3>Program Lainnya</h3>
                            <ul>
                                @foreach ($otherPrograms as $other)
                                    <li>
                                        <a href="{{ route('program.show', $other->slug) }}">{{ $other->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Program List End -->

                        <!-- Sidebar CTA Start -->
                        <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
                            <div class="sidebar-cta-body">
                                <div class="sidebar-cta-logo">
                                    <img src="{{ asset('images/logo.svg') }}" alt="Tapak Suci Sport Academy">
                                </div>
                                <div class="sidebar-cta-content">
                                    <h3>Siap Bergabung?</h3>
                                </div>
                                <div class="sidebar-cta-btn">
                                    <a href="{{ route('registration', ['program' => $program->id]) }}" class="btn-default btn-highlighted">
                                        Daftar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Sidebar CTA End -->
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="program-single-content">
                        @if ($program->cover_url)
                            <div class="page-single-image">
                                <figure class="image-anime reveal">
                                    <img src="{{ $program->cover_url }}" alt="{{ $program->name }}">
                                </figure>
                            </div>
                        @endif

                        <div class="program-entry">
                            <span class="badge bg-primary mb-3">{{ $program->level->getLabel() }}</span>

                            @if ($program->short_description)
                                <p class="wow fadeInUp"><strong>{{ $program->short_description }}</strong></p>
                            @endif

                            {!! $program->description !!}
                        </div>

                        <!-- Detail Program Start -->
                        <div class="program-highlights-box mt-5">
                            <h2 class="text-anime-style-3">Detail Program</h2>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 40%">Usia Peserta</th>
                                            <td>{{ $program->age_range_label }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tingkat</th>
                                            <td>{{ $program->level->getLabel() }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Durasi &amp; Frekuensi</th>
                                            <td>{{ $program->duration_label }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Iuran Bulanan</th>
                                            <td>{{ $program->monthly_fee_label }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cabang</th>
                                            <td>
                                                @forelse ($program->branches ?? [] as $branch)
                                                    <span class="badge bg-light text-dark me-1">{{ $branch }}</span>
                                                @empty
                                                    Semua cabang
                                                @endforelse
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Detail Program End -->

                        <!-- Jadwal Start -->
                        <div class="program-benefits-box mt-5">
                            <h2 class="text-anime-style-3">Jadwal Latihan</h2>

                            @if ($program->schedules->isEmpty())
                                <p class="text-muted">Jadwal untuk program ini belum tersedia. Silakan hubungi admin.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Hari</th>
                                                <th>Jam</th>
                                                <th>Cabang</th>
                                                <th>Lokasi</th>
                                                <th>Pelatih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($program->schedules as $schedule)
                                                <tr>
                                                    <td>{{ $schedule->day_name }}</td>
                                                    <td>{{ $schedule->time_range }}</td>
                                                    <td>{{ $schedule->branch }}</td>
                                                    <td>{{ $schedule->location }}</td>
                                                    <td>{{ $schedule->coach?->name ?? $schedule->coach_name ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <!-- Jadwal End -->

                        <div class="mt-4">
                            <a href="{{ route('registration', ['program' => $program->id]) }}" class="btn-default">
                                Daftar Program Ini
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Program Single End -->
@endsection
