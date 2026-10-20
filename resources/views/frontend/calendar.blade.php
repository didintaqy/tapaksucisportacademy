@extends('layout.master')

@section('title', 'Kalender Kegiatan | Tapak Suci Sport Academy')

@section('hero')
    <div class="page-header bg-section dark-section parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Kalender Kegiatan</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">Kalender</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Calendar Page Start -->
    <div class="page-section calendar-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center mb-5">
                        <h2>Kalender Kegiatan Akademi</h2>
                        <p>Jadwal latihan, kejuaraan, UKT, dan pengumuman resmi Tapak Suci Sport Academy</p>
                    </div>
                </div>
            </div>

            {{-- Filter Bulan/Tahun --}}
            <div class="row mb-5">
                <div class="col-md-8 mx-auto">
                    <form method="GET" class="d-flex flex-wrap gap-2 justify-content-center">
                        <select name="month" class="form-select" style="min-width: 180px;">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-select" style="width: 120px;">
                            @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-primary align-self-end">
                            <i class="fas fa-filter me-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('calendar') }}" class="btn btn-outline-secondary align-self-end">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </form>
                </div>
            </div>

            {{-- Info Bulan Aktif --}}
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <div class="d-inline-flex align-items-center gap-3 px-4 py-2 bg-light rounded-pill">
                        <a href="{{ route('calendar', ['month' => $month === 1 ? 12 : $month - 1, 'year' => $month === 1 ? $year - 1 : $year]) }}" 
                           class="btn btn-sm btn-outline-primary rounded-circle p-1" title="Bulan Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <h4 class="mb-0 fw-bold">{{ ucfirst($monthName) }} {{ $year }}</h4>
                        <a href="{{ route('calendar', ['month' => $month === 12 ? 1 : $month + 1, 'year' => $month === 12 ? $year + 1 : $year]) }}" 
                           class="btn btn-sm btn-outline-primary rounded-circle p-1" title="Bulan Berikutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Grid Kalender --}}
            <div class="row">
                <div class="col-12">
                    <div class="calendar-grid bg-white rounded-3 p-3 shadow-sm"
                         style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">

                        {{-- Header Hari --}}
                        @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                            <div class="calendar-day-header text-center fw-bold py-2 bg-light rounded d-flex align-items-center justify-content-center"
                                 style="height: 40px;">
                                {{ $day }}
                            </div>
                        @endforeach

                        {{-- Kosong awal bulan --}}
                        @for($i = 0; $i < $firstDayOfWeek; $i++)
                            <div class="calendar-day empty bg-transparent"></div>
                        @endfor

                        {{-- Hari-hari --}}
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                                $isToday = $today->format('Y-m-d') === $dateStr;
                                $dayEvents = collect($eventsByDay[$d] ?? []);
                            @endphp
                            <div class="calendar-day p-2 border rounded h-100 d-flex flex-column position-relative"
                                 style="min-height: 130px; background: {{ $isToday ? 'rgba(var(--bs-primary-rgb), 0.08)' : 'transparent' }};">
                                
                                {{-- Badge "Hari Ini" --}}
                                @if($isToday)
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-1 px-2 py-1"
                                          style="font-size: 0.65rem; z-index: 2;">
                                        Hari Ini
                                    </span>
                                @endif

                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-bold {{ $isToday ? 'text-primary' : '' }}">{{ $d }}</span>
                                    @if($dayEvents->count() > 3)
                                        <span class="badge bg-light text-dark px-2 py-1"
                                              style="font-size: 0.6rem;">
                                            +{{ $dayEvents->count() - 3 }} lagi
                                        </span>
                                    @endif
                                </div>

                                <div class="flex-grow-1 overflow-auto mt-1" style="max-height: 90px;">
                                    @foreach($dayEvents->take(3) as $event)
                                        <div class="mb-1">
                                            <span class="badge bg-{{ $event->type_color }} text-decoration-none d-inline-block text-wrap px-2 py-1"
                                                  style="font-size: 0.65rem; cursor: pointer;"
                                                  title="{{ $event->title }}{{ $event->description ? ': ' . $event->description : '' }}">
                                                {{ $event->title }}
                                            </span>
                                            @if($event->ends_at && $event->ends_at->format('Y-m-d') !== $event->starts_at->format('Y-m-d'))
                                                <small class="text-muted d-block ms-2" style="font-size: 0.55rem;">
                                                    (sd {{ $event->ends_at->format('d M') }})
                                                </small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Keterangan Jenis Kegiatan:</h6>
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                @foreach(['latihan' => 'primary', 'kejuaraan' => 'danger', 'ukt' => 'warning', 'pengumuman' => 'info', 'libur' => 'secondary'] as $type => $color)
                                    <span class="badge bg-{{ $color }} px-3 py-2 d-inline-flex align-items-center gap-1"
                                          style="font-size: 0.8rem;">
                                        <i class="fas {{ $type === 'latihan' ? 'fa-dumbbell' : ($type === 'kejuaraan' ? 'fa-trophy' : ($type === 'ukt' ? 'fa-medal' : ($type === 'pengumuman' ? 'fa-bullhorn' : 'fa-umbrella-beach'))) }}"></i>
                                        {{ ucfirst($type) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <div class="card border-0 bg-primary text-white py-4">
                        <div class="card-body">
                            <h4 class="mb-2">Ingin Bergabung?</h4>
                            <p class="mb-3 opacity-75">Lihat jadwal latihan rutin dan daftar menjadi siswa Tapak Suci Sport Academy</p>
                            <a href="{{ route('registration') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                            </a>
                            <a href="{{ route('schedules') }}" class="btn btn-outline-light btn-lg px-4 ms-2">
                                <i class="fas fa-calendar-alt me-2"></i> Lihat Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Calendar Page End -->
@endsection

@push('scripts')
<style>
    .calendar-day {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .calendar-day:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        z-index: 10;
    }
    .calendar-day.empty {
        background: transparent !important;
        pointer-events: none;
    }
    .calendar-day-header {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid var(--bs-primary);
    }
    @media (max-width: 767px) {
        .calendar-grid {
            grid-template-columns: repeat(7, 1fr) !important;
            gap: 4px !important;
        }
        .calendar-day {
            min-height: 100px !important;
            padding: 0.5rem 0.25rem !important;
        }
        .calendar-day-header {
            height: 32px !important;
            font-size: 0.75rem !important;
        }
    }
</style>
@endpush