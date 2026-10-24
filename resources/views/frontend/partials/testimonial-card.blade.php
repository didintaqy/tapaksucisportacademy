@php($delay = $delay ?? 0)
<div class="testimonial-item wow fadeInUp" data-wow-delay="{{ $delay }}s">
    <div class="testimonial-item-header">
        <div class="testimonial-author">
            <div class="testimonial-author-image">
                <figure>
                    <span class="avatar-initials">{{ $testimonial->initials }}</span>
                </figure>
            </div>
            <div class="testimonial-author-content">
                <h3>{{ $testimonial->name }}</h3>
                <p>{{ $testimonial->relation ?: 'Anggota Tapak Suci' }}</p>
            </div>
        </div>
        <div class="testimonial-item-quote">
            <img src="{{ asset('images/testimonial-item-quote.svg') }}" alt="">
        </div>
    </div>
    <div class="testimonial-item-body">
        <div class="testimonial-rating">
            @for ($star = 1; $star <= 5; $star++)
                <i class="fa-solid fa-star{{ $star <= $testimonial->rating ? '' : ' text-muted' }}"></i>
            @endfor
        </div>
        <div class="testimonial-item-body-content">
            <p>&ldquo;{{ $testimonial->content }}&rdquo;</p>
        </div>
    </div>
</div>
