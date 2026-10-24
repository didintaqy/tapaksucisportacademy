@php($delay = $delay ?? 0)
<div class="program-item wow fadeInUp" data-wow-delay="{{ $delay }}s">
    <div class="program-item-image">
        <figure>
            <a href="{{ route('program.show', $program->slug) }}" data-cursor-text="Lihat">
                <img src="{{ $program->cover_url ?: asset('images/programs-item-image-1.png') }}"
                     alt="{{ $program->name }}">
            </a>
        </figure>
    </div>
    <div class="program-item-content">
        @if ($program->level)
            <span class="badge bg-primary mb-2">{{ $program->level->getLabel() }}</span>
        @endif
        <h2><a href="{{ route('program.show', $program->slug) }}">{{ $program->name }}</a></h2>
        @if (filled($program->short_description))
            <p>{{ $program->short_description }}</p>
        @endif
        <p class="small text-muted mb-1">
            {{ $program->age_range_label }} &middot; {{ $program->duration_label }}
        </p>
        <p class="small mb-0"><strong>{{ $program->monthly_fee_label }}</strong> / bulan</p>
    </div>
    <div class="program-item-btn">
        <a href="{{ route('program.show', $program->slug) }}" class="readmore-btn">Selengkapnya</a>
    </div>
</div>
