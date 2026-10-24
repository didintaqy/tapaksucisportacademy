@extends('layout.master')

@section('title', 'Jadwal Latihan')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'Jadwal Latihan', 'breadcrumb' => 'Jadwal Latihan'])
@endsection

@section('content')
    <!-- Page Schedules Start -->
    <div class="page-faqs">
        <div class="container">
            @forelse ($groups as $day => $schedules)
                <div class="row section-row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Hari Latihan</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">{{ $day }}</h2>
                        </div>
                    </div>

                    @foreach ($schedules as $schedule)
                        <div class="col-xl-4 col-md-6">
                            <div class="program-item wow fadeInUp">
                                <div class="program-item-content">
                                    <h3>{{ $schedule->class_name }}</h3>
                                    <p><strong>{{ $schedule->time_range }}</strong></p>
                                    <p>{{ $schedule->branch }}</p>
                                    <p>{{ $schedule->location }}</p>
                                    @if ($schedule->coach_name)
                                        <p>Pelatih: {{ $schedule->coach_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="row section-row">
                    <div class="col-lg-12">
                        <p class="text-center">Belum ada jadwal latihan yang ditampilkan.</p>
                    </div>
                </div>
            @endforelse

            <div class="row section-row">
                <div class="col-lg-12 text-center">
                    @if (filled($site['whatsapp_url'] ?? null))
                        <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-default btn-highlighted me-2">
                            Tanya Jadwal via WhatsApp
                        </a>
                    @endif
                    <a href="{{ route('contact-us') }}" class="btn-default">Tanya Jadwal</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Schedules End -->
@endsection
