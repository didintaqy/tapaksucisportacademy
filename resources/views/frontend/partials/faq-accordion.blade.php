@php($accordionId = $accordionId ?? 'faq-accordion')
<div class="faq-accordion" id="{{ $accordionId }}">
    @foreach ($faqs as $faq)
        <div class="accordion-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.1, 0.8) }}s">
            <h2 class="accordion-header" id="{{ $accordionId }}-heading-{{ $faq->id }}">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}-collapse-{{ $faq->id }}"
                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                    aria-controls="{{ $accordionId }}-collapse-{{ $faq->id }}">
                    {{ $faq->question }}
                </button>
            </h2>
            <div id="{{ $accordionId }}-collapse-{{ $faq->id }}"
                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                 role="region" aria-labelledby="{{ $accordionId }}-heading-{{ $faq->id }}"
                 data-bs-parent="#{{ $accordionId }}">
                <div class="accordion-body">
                    <p>{!! nl2br(e($faq->answer)) !!}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
