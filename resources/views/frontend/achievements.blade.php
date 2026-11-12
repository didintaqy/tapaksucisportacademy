@extends('layout.master')

@section('title', 'Prestasi & Kejuaraan Atlet')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'Prestasi & Kejuaraan', 'breadcrumb' => 'Prestasi Atlet'])
@endsection

@section('content')
    <div class="page-faqs">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Jejak Juara</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Rekam Prestasi &amp; Perolehan Medali</h2>
                    </div>
                </div>
            </div>

            <!-- Statistik Medali -->
            <div class="row g-3 mb-5">
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 border" style="background: #fffbeb; border-color: #fde68a;">
                        <div class="fs-2 fw-bold" style="color: #b45309;">{{ $summary['emas'] }}</div>
                        <div class="small text-uppercase fw-semibold" style="color: #92400e;">Medali Emas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 border" style="background: #f8fafc; border-color: #e2e8f0;">
                        <div class="fs-2 fw-bold" style="color: #334155;">{{ $summary['perak'] }}</div>
                        <div class="small text-uppercase fw-semibold" style="color: #64748b;">Medali Perak</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 border" style="background: #fff7ed; border-color: #fed7aa;">
                        <div class="fs-2 fw-bold" style="color: #c2410c;">{{ $summary['perunggu'] }}</div>
                        <div class="small text-uppercase fw-semibold" style="color: #9a3412;">Medali Perunggu</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 border" style="background: #f0fdf4; border-color: #bbf7d0;">
                        <div class="fs-2 fw-bold" style="color: #15803d;">{{ $summary['juara_umum'] }}</div>
                        <div class="small text-uppercase fw-semibold" style="color: #166534;">Gelar Juara Umum</div>
                    </div>
                </div>
            </div>

            <!-- Filter Pencarian -->
            <form method="GET" action="{{ route('achievements') }}" class="row g-2 mb-4 align-items-end">
                <div class="col-md-4">
                    <label for="q" class="form-label small fw-semibold">Cari Kejuaraan / Atlet</label>
                    <input type="text" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama kejuaraan, atlet, atau kategori...">
                </div>
                <div class="col-6 col-md-3">
                    <label for="level" class="form-label small fw-semibold">Tingkat</label>
                    <select id="level" name="level" class="form-select">
                        <option value="">Semua Tingkat</option>
                        @foreach ($levels as $val => $label)
                            <option value="{{ $val }}" @selected($selectedLevel === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="medal" class="form-label small fw-semibold">Capaian</label>
                    <select id="medal" name="medal" class="form-select">
                        <option value="">Semua Medali</option>
                        @foreach ($medals as $val => $label)
                            <option value="{{ $val }}" @selected($selectedMedal === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label for="year" class="form-label small fw-semibold">Tahun</label>
                    <select id="year" name="year" class="form-select">
                        <option value="">Semua</option>
                        @foreach ($years as $yr)
                            <option value="{{ $yr }}" @selected((string) $selectedYear === (string) $yr)>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                    @if ($search !== '' || $selectedLevel || $selectedMedal || $selectedYear)
                        <a href="{{ route('achievements') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Daftar Prestasi -->
            <div class="row g-4">
                @forelse ($achievements as $achievement)
                    <div class="col-xl-4 col-md-6">
                        <div class="program-item wow fadeInUp h-100">
                            <div class="program-item-content">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                                    <span class="badge bg-dark">{{ $achievement->level_label }}</span>
                                    <span class="badge {{ match($achievement->medal?->value) {
                                        'emas' => 'bg-warning text-dark',
                                        'perak' => 'bg-secondary',
                                        'perunggu' => 'bg-info text-dark',
                                        'juara_umum' => 'bg-success',
                                        default => 'bg-primary'
                                    } }}">
                                        {{ $achievement->medal_label }}
                                    </span>
                                </div>
                                <h3>{{ $achievement->competition_name }}</h3>
                                <p class="mb-1">
                                    <strong>Atlet / Kontingen:</strong>
                                    {{ $achievement->student?->full_name ?? 'Kontingen Tapak Suci Sport Academy' }}
                                </p>
                                @if ($achievement->category || $achievement->weight_class)
                                    <p class="mb-1">
                                        <strong>Kategori:</strong>
                                        {{ trim(($achievement->category ?? '').($achievement->weight_class ? " • {$achievement->weight_class}" : '')) }}
                                    </p>
                                @endif
                                <p class="mb-1 text-muted small">
                                    {{ $achievement->event_date?->translatedFormat('d F Y') }}
                                    @if ($achievement->location)
                                        &middot; {{ $achievement->location }}
                                    @endif
                                </p>
                                @if ($achievement->notes)
                                    <p class="mt-2 mb-0 small">{{ $achievement->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-5 text-center border rounded-4">
                            <p class="mb-0">Belum ada data prestasi yang sesuai dengan filter pencarian.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($achievements->hasPages())
                <div class="row mt-4">
                    <div class="col-12">
                        @include('frontend.partials.pagination', ['paginator' => $achievements])
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
