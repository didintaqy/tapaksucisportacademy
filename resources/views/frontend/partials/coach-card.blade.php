@php($delay = $delay ?? 0)
<div class="team-item wow fadeInUp" data-wow-delay="{{ $delay }}s">
    <div class="team-item-image">
        <figure class="image-anime">
            @if ($coach->photo_url)
                <img src="{{ $coach->photo_url }}" alt="{{ $coach->name }}">
            @else
                <div class="team-initials" aria-hidden="true">{{ $coach->initials }}</div>
            @endif
        </figure>
    </div>

    <div class="team-item-content">
        <h3>{{ $coach->name }}</h3>
        @if (filled($coach->title))
            <p>{{ $coach->title }}</p>
        @endif
        @if (filled($coach->specialization))
            <p class="coach-card-meta">{{ $coach->specialization }}</p>
        @endif
        @if ($coach->experience_label)
            <p class="small text-muted">{{ $coach->experience_label }}</p>
        @endif
        @if (filled($coach->bio))
            <p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($coach->bio, 120) }}</p>
        @endif
    </div>
</div>
