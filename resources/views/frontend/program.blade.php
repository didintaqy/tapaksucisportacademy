@extends('layout.master')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'Program Latihan', 'breadcrumb' => 'Program'])
@endsection

@section('content')

    <!-- Page Programs Start -->
    <div class="page-programs">
        <div class="container">
            @if ($branches->isNotEmpty())
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 align-items-center wow fadeInUp">
                            <span class="text-muted me-1">Cabang:</span>
                            <a href="{{ route('program') }}"
                               class="btn btn-sm {{ ! $activeBranch ? 'btn-primary' : 'btn-outline-primary' }}">
                                Semua
                            </a>
                            @foreach ($branches as $branch)
                                <a href="{{ route('program', ['branch' => $branch]) }}"
                                   class="btn btn-sm {{ $activeBranch === $branch ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $branch }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($programs->isEmpty())
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="wow fadeInUp">Belum ada program latihan yang tersedia.</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($programs as $program)
                        <div class="col-xl-3 col-md-6">
                            <!-- Program Item Start -->
                            <div class="program-item wow fadeInUp" data-wow-delay="{{ min($loop->index * 0.2, 1.4) }}s">
                                <div class="program-item-image">
                                    <figure>
                                        <a href="{{ route('program.show', $program->slug) }}" data-cursor-text="Lihat">
                                            <img src="{{ $program->cover_url ?: asset('images/programs-item-image-1.png') }}"
                                                 alt="{{ $program->name }}">
                                        </a>
                                    </figure>
                                </div>
                                <div class="program-item-content">
                                    <span class="badge bg-primary mb-2">{{ $program->level->getLabel() }}</span>
                                    <h2><a href="{{ route('program.show', $program->slug) }}">{{ $program->name }}</a></h2>
                                    <p>{{ $program->short_description }}</p>
                                    <p class="small text-muted mb-0">
                                        {{ $program->age_range_label }} · {{ $program->duration_label }}
                                    </p>
                                </div>
                                <div class="program-item-btn">
                                    <a href="{{ route('program.show', $program->slug) }}" class="readmore-btn">Selengkapnya</a>
                                </div>
                            </div>
                            <!-- Program Item End -->
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <!-- Page Programs End -->

    <!-- How It Work Section Start -->
    <div class="how-it-work bg-section dark-section parallaxie">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6">
                    <!-- How It Work Image Box Start -->
                    <div class="how-it-work-image-box wow fadeInUp">
                        <!-- How Works Image Item 1 Start -->
                        <div class="how-works-image-item-1">
                            <!-- How Works Client Box Start -->
                            <div class="how-works-client-box">
                                <!-- Satisfy Client Images Start -->
                                <div class="satisfy-client-images">
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="images/author-1.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="images/author-2.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="images/author-3.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="images/author-4.jpg" alt="">
                                        </figure>
                                    </div>
                                </div>
                                <!-- Satisfy Client Images End -->

                                <!-- How Works Client Content Start -->
                                <div class="how-works-client-content">
                                    <ul>
                                        <li><span class="counter">4.9</span></li>
                                        <li><i class="fa fa-star"></i></li>
                                    </ul>
                                    <p>Students Review</p>
                                </div>
                                <!-- How Works Client Content End -->
                            </div>
                            <!-- How Works Client Box End -->

                            <!-- How Works Image 1 Start -->
                            <div class="how-works-image-1">
                                <figure class="image-anime">
                                    <img src="images/how-it-work-image-1.jpg" alt="">
                                </figure>
                            </div>
                            <!-- How Works Image 1 End -->
                        </div>
                        <!-- How Works Image Item 1 End -->

                        <!-- How Works Image Item 2 Start -->
                        <div class="how-works-image-item-2">
                            <!-- How Works Image 2 Start -->
                            <div class="how-works-image-2">
                                <figure class="image-anime">
                                    <img src="images/how-it-work-image-2.jpg" alt="">
                                </figure>
                            </div>
                            <!-- How Works Image 2 End -->
                        </div>
                        <!-- How Works Image Item 2 End -->
                    </div>
                    <!-- How It Work Image Box End -->
                </div>

                <div class="col-xl-6">
                    <!-- How It Work Content Start -->
                    <div class="how-it-work-content">
                        <!-- Section Title Start -->
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">How It Work</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Step-by-step training for lasting martial strength</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- How It Work Steps Start -->
                        <div class="how-it-work-steps">
                            <!-- How It Work Step Start -->
                            <div class="how-work-step-item wow fadeInUp">
                                <div class="icon-box">
                                    <img src="images/icon-how-it-work-step-1.svg" alt="">
                                </div>
                                <div class="how-work-step-item-content">
                                    <h3>Step 1: Free Trial Class</h3>
                                    <p>Experience our teaching style, environment, and instructors firsthand this session.</p>
                                </div>
                            </div>
                            <!-- How It Work Step End -->

                            <!-- How It Work Step Start -->
                            <div class="how-work-step-item wow fadeInUp" data-wow-delay="0.2s">
                                <div class="icon-box">
                                    <img src="images/icon-how-it-work-step-2.svg" alt="">
                                </div>
                                <div class="how-work-step-item-content">
                                    <h3>Step 2: Choose Training Program</h3>
                                    <p>Experience our teaching style, environment, and instructors firsthand this session.</p>
                                </div>
                            </div>
                            <!-- How It Work Step End -->

                            <!-- How It Work Step Start -->
                            <div class="how-work-step-item wow fadeInUp" data-wow-delay="0.4s">
                                <div class="icon-box">
                                    <img src="images/icon-how-it-work-step-3.svg" alt="">
                                </div>
                                <div class="how-work-step-item-content">
                                    <h3>Step 3: Begin Regular Training</h3>
                                    <p>Experience our teaching style, environment, and instructors firsthand this session.</p>
                                </div>
                            </div>
                            <!-- How It Work Step End -->

                            <!-- How It Work Step Start -->
                            <div class="how-work-step-item wow fadeInUp" data-wow-delay="0.6s">
                                <div class="icon-box">
                                    <img src="images/icon-how-it-work-step-4.svg" alt="">
                                </div>
                                <div class="how-work-step-item-content">
                                    <h3>Step 4: Grow Strong — Mind & Body</h3>
                                    <p>Experience our teaching style, environment, and instructors firsthand this session.</p>
                                </div>
                            </div>
                            <!-- How It Work Step End -->
                        </div>
                        <!-- How It Work Steps End -->
                    </div>
                    <!-- How It Work Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- How It Work Section End -->

    <!-- Why Choose Us Section Start -->
    <div class="why-choose-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6">
                    <!-- Why Choose Us Content Start -->
                    <div class="why-choose-us-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Why Choose Us</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Why students grow stronger with our guidance</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">With consistent training, focused instruction, and real encouragement, students unlock their true potential and become stronger every day.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Why Choose Item List Start -->
                        <div class="why-choose-item-list wow fadeInUp" data-wow-delay="0.4s">
                            <!-- Why Choose Item Start -->
                            <div class="why-choose-item">
                                <!-- Why Choose Item Image Start -->
                                <div class="why-choose-item-image">
                                    <figure class="image-anime">
                                        <img src="images/why-choose-item-img-1.jpg" alt="">
                                    </figure>
                                </div>
                                <!-- Why Choose Item Image End -->

                                <!-- Why Choose Item Content Start -->
                                <div class="why-choose-item-content">
                                    <h3>Focus on Discipline and Confidence</h3>
                                    <p>We help students develop patience, respect, and a positive mindset. Through structured training, they learn to stay focused.</p>
                                </div>
                                <!-- Why Choose Item Content End -->
                            </div>
                            <!-- Why Choose Item End -->

                            <!-- Why Choose Item Start -->
                            <div class="why-choose-item">
                                <!-- Why Choose Item Image Start -->
                                <div class="why-choose-item-image">
                                    <figure class="image-anime">
                                        <img src="images/why-choose-item-img-2.jpg" alt="">
                                    </figure>
                                </div>
                                <!-- Why Choose Item Image End -->

                                <!-- Why Choose Item Content Start -->
                                <div class="why-choose-item-content">
                                    <h3>Structured Training Programs</h3>
                                    <p>We help students develop patience, respect, and a positive mindset. Through structured training, they learn to stay focused.</p>
                                </div>
                                <!-- Why Choose Item Content End -->
                            </div>
                            <!-- Why Choose Item End -->
                        </div>
                        <!-- Why Choose Item List End -->

                        <!-- Why Choose Us Footer Start -->
                        <div class="why-choose-us-footer wow fadeInUp" data-wow-delay="0.6s">
                            <!-- Why Choose Us Button Start -->
                            <div class="why-choose-btn">
                                <a href="book-appointment.html" class="btn-default">Book Free Trial</a>
                            </div>
                            <!-- Why Choose Us Button End -->

                            <!-- Why Choose Intro Video Start -->
                            <div class="why-choose-intro-video">
                                <!-- Video Play Button Start -->
                                <div class="video-play-button">
                                    <a href="https://www.youtube.com/watch?v=Y-x0efG1seA" class="popup-video" data-cursor-text="Play">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                </div>
                                <!-- Video Play Button End -->

                                <!-- Why Choose Intro Video Content Start -->
                                <div class="why-choose-intro-video-content">
                                    <h3>Intro Video</h3>
                                </div>
                                <!-- Why Choose Intro Video Content End -->
                            </div>
                            <!-- Why Choose Intro Video End -->
                        </div>
                        <!-- Why Choose Us Footer End -->
                    </div>
                    <!-- Why Choose Us Content End -->
                </div>

                <div class="col-xl-6">
                    <!-- Why Choose Images Start -->
                    <div class="why-choose-images wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Why Choose Image 1 Start -->
                        <div class="why-choose-img-1">
                            <figure class="image-anime">
                                <img src="images/why-choose-image-1.jpg" alt="">
                            </figure>
                        </div>
                        <!-- Why Choose Image 1 End -->

                        <!-- Why Choose Image 2 Start -->
                        <div class="why-choose-img-2">
                            <figure class="image-anime">
                                <img src="images/why-choose-image-2.jpg" alt="">
                            </figure>

                            <!-- Contact Us Circle Start -->
                            <div class="years-experience-circle">
                                <a href="contact.html">
                                    <img src="images/years-experience-circle.svg" alt="">
                                </a>
                            </div>
                            <!-- Contact Us Circle End -->
                        </div>
                        <!-- Why Choose Image 2 End -->
                    </div>
                    <!-- Why Choose Images End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Why Choose Us Section End -->

    <!-- Our Testimonials Section Start -->
    <div class="our-testimonials bg-section dark-section parallaxie">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Testimonials</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Real experiences from our dojo family community</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Testimonial Slider Start -->
                    <div class="testimonial-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper" data-cursor-text="Drag">
                                <!-- Swiper Slide Start -->
                                <div class="swiper-slide">
                                    <!-- Testimonial Item Start -->
                                    <div class="testimonial-item">
                                        <!-- Testimonial Item Header Start -->
                                        <div class="testimonial-item-header">
                                            <!-- Testimonial Author Start -->
                                            <div class="testimonial-author">
                                                <div class="testimonial-author-image">
                                                    <figure class="image-anime">
                                                        <img src="images/author-1.jpg" alt="">
                                                    </figure>
                                                </div>                                                
                                                <div class="testimonial-author-content">
                                                    <h3>Leslie Alexander</h3>
                                                    <p>Adult Learner</p>
                                                </div>
                                            </div>
                                            <!-- Testimonial Author End -->

                                            <!-- Testimonial Item Quote Start -->
                                            <div class="testimonial-item-quote">
                                                <img src="images/testimonial-item-quote.svg" alt="">
                                            </div>
                                            <!-- Testimonial Item Quote End -->
                                        </div>
                                        <!-- Testimonial Item Header End -->

                                        <!-- Testimonial Item Body Start -->
                                        <div class="testimonial-item-body">
                                            <div class="testimonial-rating">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="testimonial-item-body-content">
                                                <p>“My daughter joined the dojo a few ago, and the change has been amazing. She has become more focused, confident, and respectful at home and in school. The instructors attentive, supportive, and truly care about each student's progress.”</p>
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Body End -->
                                    </div>
                                    <!-- Testimonial Item End -->
                                </div>
                                <!-- Swiper Slide End -->

                                <!-- Swiper Slide Start -->
                                <div class="swiper-slide">
                                    <!-- Testimonial Item Start -->
                                    <div class="testimonial-item">
                                        <!-- Testimonial Item Header Start -->
                                        <div class="testimonial-item-header">
                                            <!-- Testimonial Author Start -->
                                            <div class="testimonial-author">
                                                <div class="testimonial-author-image">
                                                    <figure class="image-anime">
                                                        <img src="images/author-2.jpg" alt="">
                                                    </figure>
                                                </div>                                                
                                                <div class="testimonial-author-content">
                                                    <h3>Brooklyn Simmons</h3>
                                                    <p>Adult Learner</p>
                                                </div>
                                            </div>
                                            <!-- Testimonial Author End -->

                                            <!-- Testimonial Item Quote Start -->
                                            <div class="testimonial-item-quote">
                                                <img src="images/testimonial-item-quote.svg" alt="">
                                            </div>
                                            <!-- Testimonial Item Quote End -->
                                        </div>
                                        <!-- Testimonial Item Header End -->

                                        <!-- Testimonial Item Body Start -->
                                        <div class="testimonial-item-body">
                                            <div class="testimonial-rating">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="testimonial-item-body-content">
                                                <p>“My daughter joined the dojo a few ago, and the change has been amazing. She has become more focused, confident, and respectful at home and in school. The instructors attentive, supportive, and truly care about each student's progress.”</p>
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Body End -->
                                    </div>
                                    <!-- Testimonial Item End -->
                                </div>
                                <!-- Swiper Slide End -->

                                <!-- Swiper Slide Start -->
                                <div class="swiper-slide">
                                    <!-- Testimonial Item Start -->
                                    <div class="testimonial-item">
                                        <!-- Testimonial Item Header Start -->
                                        <div class="testimonial-item-header">
                                            <!-- Testimonial Author Start -->
                                            <div class="testimonial-author">
                                                <div class="testimonial-author-image">
                                                    <figure class="image-anime">
                                                        <img src="images/author-3.jpg" alt="">
                                                    </figure>
                                                </div>                                                
                                                <div class="testimonial-author-content">
                                                    <h3>Savannah Nguyen</h3>
                                                    <p>Adult Learner</p>
                                                </div>
                                            </div>
                                            <!-- Testimonial Author End -->

                                            <!-- Testimonial Item Quote Start -->
                                            <div class="testimonial-item-quote">
                                                <img src="images/testimonial-item-quote.svg" alt="">
                                            </div>
                                            <!-- Testimonial Item Quote End -->
                                        </div>
                                        <!-- Testimonial Item Header End -->

                                        <!-- Testimonial Item Body Start -->
                                        <div class="testimonial-item-body">
                                            <div class="testimonial-rating">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="testimonial-item-body-content">
                                                <p>“My daughter joined the dojo a few ago, and the change has been amazing. She has become more focused, confident, and respectful at home and in school. The instructors attentive, supportive, and truly care about each student's progress.”</p>
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Body End -->
                                    </div>
                                    <!-- Testimonial Item End -->
                                </div>
                                <!-- Swiper Slide End -->

                                <!-- Swiper Slide Start -->
                                <div class="swiper-slide">
                                    <!-- Testimonial Item Start -->
                                    <div class="testimonial-item">
                                        <!-- Testimonial Item Header Start -->
                                        <div class="testimonial-item-header">
                                            <!-- Testimonial Author Start -->
                                            <div class="testimonial-author">
                                                <div class="testimonial-author-image">
                                                    <figure class="image-anime">
                                                        <img src="images/author-4.jpg" alt="">
                                                    </figure>
                                                </div>
                                                
                                                <div class="testimonial-author-content">
                                                    <h3>Alexander Simmons</h3>
                                                    <p>Adult Learner</p>
                                                </div>
                                            </div>
                                            <!-- Testimonial Author End -->

                                            <!-- Testimonial Item Quote Start -->
                                            <div class="testimonial-item-quote">
                                                <img src="images/testimonial-item-quote.svg" alt="">
                                            </div>
                                            <!-- Testimonial Item Quote End -->
                                        </div>
                                        <!-- Testimonial Item Header End -->

                                        <!-- Testimonial Item Body Start -->
                                        <div class="testimonial-item-body">
                                            <div class="testimonial-rating">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>

                                            <div class="testimonial-item-body-content">
                                                <p>“My daughter joined the dojo a few ago, and the change has been amazing. She has become more focused, confident, and respectful at home and in school. The instructors attentive, supportive, and truly care about each student's progress.”</p>
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Body End -->
                                    </div>
                                    <!-- Testimonial Item End -->
                                </div>
                                <!-- Swiper Slide End -->
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial Slider End -->
                </div>

                <div class="col-lg-12">
                    <!-- Section Footer Text Start -->
                    <div class="section-footer-text wow fadeInUp" data-wow-delay="0.2s">
                        <p>Explore All Training Programs Designed for Every Student - <a href="programs.html">View All Program</a></p>
                        <ul>
                            <li><span class="counter">4.9</span>/5</li>
                            <li>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </li>
                            <li>Our 4200 Review</li>
                        </ul>
                    </div>
                    <!-- Section Footer Text End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Testimonials Section End -->

    <!-- Our Faqs Section Start -->
    <div class="our-faqs">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <!-- Out Faqs Content Start -->
                    <div class="our-faqs-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">FAQ's</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Answers to yourmost common student questions</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">We understand that starting martial arts training can bring many questions gathered clear and helpful answers to the things students and parents ask most</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Our Faqs List Start -->
                        <div class="our-faqs-list wow fadeInUp" data-wow-delay="0.4s">
                            <ul>
                                <li>Enrollment and Membership Steps Made Easy</li>
                                <li>Instructor Qualifications and Experience Clarified</li>
                                <li>Clear and Simple Answers for Beginners</li>
                            </ul>
                        </div>
                        <!-- Our Faqs List End -->

                        <!-- Out Faqs Button Start -->
                        <div class="our-faqs-btn wow fadeInUp" data-wow-delay="0.6s">
                            <a href="faqs.html" class="btn-default">View all FAQ's</a>
                        </div>
                        <!-- Out Faqs Button End -->
                    </div>
                    <!-- Out Faqs Content End -->
                </div>

                <div class="col-xl-6">
                    <!-- FAQ Accordion Start -->
                    <div class="faq-accordion" id="accordion">
                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    Q1. Who can join your karate training programs?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" role="region" aria-labelledby="heading1" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.2s">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    Q2. Do beginners need prior martial arts experience?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" role="region" aria-labelledby="heading2" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.4s">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Q3. What should students wear during first training session?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse show" role="region" aria-labelledby="heading3" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.6s">
                            <h2 class="accordion-header" id="heading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    Q4. How long does belt promotion usually take?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" role="region" aria-labelledby="heading4" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.8s">
                            <h2 class="accordion-header" id="heading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                    Q5. Do you offer private one-on-one sessions?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" role="region" aria-labelledby="heading5" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="1s">
                            <h2 class="accordion-header" id="heading6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                    Q6. Is karate training safe for young children?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" role="region" aria-labelledby="heading6" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>For your first class, just wear comfortable workout clothing like a t-shirt and track pants or shorts. You don't need the karate uniform yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->
                    </div>
                    <!-- FAQ Accordion End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Faqs Section End -->

@endsection