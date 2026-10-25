@php
    $site = $site ?? [];
    $name = data_get($site, 'identity.name', 'Tapak Suci Sport Academy');
    $url = data_get($site, 'identity.website_url', url('/'));
    $logo = data_get($site, 'assets.logo', asset('images/logo.svg'));
    $description = data_get($site, 'identity.description', 'Pencak Silat Tapak Suci untuk anak, remaja, dan dewasa.');
    $social = data_get($site, 'social_links', []);

    $sameAs = collect($social)->pluck('url')->filter()->values()->all();

    $orgJson = [
        '@context' => 'https://schema.org',
        '@type' => 'SportsOrganization',
        'name' => $name,
        'url' => $url,
        'logo' => $logo,
        'description' => $description,
        'sameAs' => $sameAs,
    ];
@endphp

<script type="application/ld+json">
    @json($orgJson)
</script>

@if (isset($program) && $program)
    @php
        $courseJson = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $program->name,
            'description' => Str::limit(strip_tags($program->description), 150),
            'provider' => [
                '@type' => 'SportsOrganization',
                'name' => $name,
                'url' => $url,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $program->monthly_fee ?? 0,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
                'url' => route('program.show', $program->slug),
            ],
        ];
    @endphp
    <script type="application/ld+json">
        @json($courseJson)
    </script>
@endif

@if (isset($post) && $post)
    @php
        $postJson = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => Str::limit(strip_tags($post->excerpt ?? $post->content), 150),
            'image' => $post->cover_url ?? '',
            'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author_name,
            ],
            'publisher' => [
                '@type' => 'SportsOrganization',
                'name' => $name,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
        @json($postJson)
    </script>
@endif