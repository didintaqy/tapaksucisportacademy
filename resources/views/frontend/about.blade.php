@extends('layout.master')
@section('content')
    <!-- About Us Section Start -->
    <div class="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6">
                    <!-- About Us Image Box Start -->
                    <div class="about-us-image-box wow fadeInUp">
                        <!-- About Us Image Start -->
                        <div class="about-us-image">
                            <figure class="image-anime">
                                <img src="images/about-us-image.jpg" alt="">
                            </figure>
                        </div>
                        <!-- About Us Image End -->
                        
                        <!-- About Us Info Video Box Start -->
                        <div class="about-us-info-video-box">
                            <!-- About Us Info Video Image Start -->
                            <div class="about-us-info-video-image">
                                <figure>
                                    <img src="images/about-us-video-image.jpg" alt="">
                                </figure>

                                <!-- Video Play Button Start -->
                                <div class="video-play-button">
                                    <a href="https://www.youtube.com/watch?v=Y-x0efG1seA" class="popup-video" data-cursor-text="Play">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                </div>
                                <!-- Video Play Button End -->
                            </div>
                            <!-- About Us Info Video Image End -->
                            
                            <!-- About Us Info Video Content Start -->
                            <div class="about-us-info-video-content">
                                <h3>We Have 500+</h3>
                                <p>Student Training </p>
                            </div>
                            <!-- About Us Info Video Content End -->
                        </div>
                        <!-- About Us Info Video Box End -->
                    </div>
                    <!-- About Us Image Box End -->
                </div>

                <div class="col-xl-6">
                    <!-- About Us Content Start -->
                    <div class="about-us-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">About Us</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Creating strong bodies and even stronger minds</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">We focus on building physical strength, mental discipline, and lasting confidence. Through structured training, students learn respect, focus.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- About Us Item List Start -->
                        <div class="about-us-item-list wow fadeInUp" data-wow-delay="0.4s">
                            <!-- About Us Item Start -->
                            <div class="about-us-item">
                                <div class="icon-box">
                                    <img src="images/icon-about-us-item-1.svg" alt="">
                                </div>
                                <div class="about-us-item-content">
                                    <h3>Our Mission</h3>
                                    <p>We train students to move strength and live with confidence.</p>
                                </div>
                            </div>
                            <!-- About Us Item End -->

                            <!-- About Us Item Start -->
                            <div class="about-us-item">
                                <div class="icon-box">
                                    <img src="images/icon-about-us-item-2.svg" alt="">
                                </div>
                                <div class="about-us-item-content">
                                    <h3>Our Vision</h3>
                                    <p>We train students to move strength and live with confidence.</p>
                                </div>
                            </div>
                            <!-- About Us Item End -->
                        </div>
                        <!-- About Us Item Lis End -->

                        <!-- About Us Footer Start -->
                        <div class="about-us-footer wow fadeInUp" data-wow-delay="0.6s">
                            <!-- About Us Button Start -->
                            <div class="about-us-btn">
                                <a href="contact.html" class="btn-default">Contact us</a>
                            </div>
                            <!-- About Us Button End -->

                            <!-- Contact Now Box Start -->
                            <div class="about-contact-box">
                                <div class="icon-box">
                                    <img src="images/icon-phone-white.svg" alt="">
                                </div>
                                <div class="about-contact-box-content">
                                    <p>Need Help?</p>
                                    <h3><a href="tel:+123456789">+91 (123) 456 789</a></h3>
                                </div>
                            </div>
                            <!-- Contact Now Box End -->
                        </div>
                        <!-- About Us Footer End -->
                    </div>
                    <!-- About Us Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- About Us Section End -->

    <!-- What We Do Section Start -->
    <div class="what-we-do bg-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <!-- What We Do Content Start -->
                    <div class="what-we-do-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">What We Do</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Helping you master karate with expert guidance</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Our training programs are designed to help students of all ages and skill levels master karate with confidence. Under the supervision of certified instructors, you'll learn proper techniques.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- What We Do Accordion Start -->
                        <div class="what-we-do-accordian" id="weaccordion">
                            <!-- What We Do Accordion Item Start -->
                            <div class="accordion-item wow fadeInUp">
                                <h2 class="accordion-header" id="weheading1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#wecollapse1" aria-expanded="true" aria-controls="wecollapse1">
                                        <img src="images/icon-what-we-do-1.svg" alt=""> Conditioning Programs
                                    </button>
                                </h2>
                                <div id="wecollapse1" class="accordion-collapse collapse show" role="region" aria-labelledby="weheading1" data-bs-parent="#weaccordion">
                                    <div class="accordion-body">
                                        <p>Our conditioning programs are designed to build strength, stamina, agility, and  fitness essential for martial arts performance.</p>
                                        <ul>
                                            <li>Advanced Kata & Technique Mastery</li>
                                            <li>Kids' Martial Arts & Character Building</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- What We Do Accordion Item End -->

                            <!-- What We Do Accordion Item Start -->
                            <div class="accordion-item wow fadeInUp" data-wow-delay="0.2s">
                                <h2 class="accordion-header" id="weheading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wecollapse2" aria-expanded="false" aria-controls="wecollapse2">
                                        <img src="images/icon-what-we-do-2.svg" alt=""> Champion Coaching
                                    </button>
                                </h2>
                                <div id="wecollapse2" class="accordion-collapse collapse" role="region" aria-labelledby="weheading2" data-bs-parent="#weaccordion">
                                    <div class="accordion-body">
                                        <p>Our conditioning programs are designed to build strength, stamina, agility, and  fitness essential for martial arts performance.</p>
                                        <ul>
                                            <li>Advanced Kata & Technique Mastery</li>
                                            <li>Kids' Martial Arts & Character Building</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- What We Do Accordion Item End -->
                        </div>
                        <!-- What We Do Accordion End -->
                    </div>
                    <!-- What We Do Content End -->
                </div>

                <div class="col-xl-6">
                    <!-- What We Do Image Box Start -->
                    <div class="what-we-do-image-box wow fadeInUp">
                        <!-- What We Do Image Start -->
                        <div class="what-we-do-img">
                            <figure class="image-anime">
                                <img src="images/what-we-do-image.jpg" alt="">
                            </figure>
                        </div>
                        <!-- What We Do Image End -->

                        <!-- What We Do Review Box Start -->
                        <div class="what-we-do-review-box">
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

                            <!-- What We Do Review Box Content Start -->
                            <div class="what-we-do-review-box-content">
                                <ul>
                                    <li><span class="counter">4.9</span></li>
                                    <li><i class="fa fa-star"></i></li>
                                </ul>
                                <p>Students Review</p>
                            </div>
                            <!-- What We Do Review Box Content End -->
                        </div>
                        <!-- What We Do Review Box End -->
                    </div>
                    <!-- What We Do Image Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- What We Do Section End -->

    <!-- Our Future Goals Section Start -->
    <div class="our-future-goals">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Future Goals</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Our future goals for continued martial excellence</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <!-- Our Future Goals Item Start -->
                    <div class="our-future-goals-item box-1 wow fadeInUp">
                        <!-- Our Future Goals Item Header Start -->
                        <div class="our-future-goals-item-header">
                            <!-- Our Future Goals Item Image Start -->
                            <div class="our-future-goals-item-image">
                                <figure>
                                    <img src="images/our-future-goals-image-1.jpg" alt="">
                                </figure>
                            </div>
                            <!-- Our Future Goals Item Image End -->

                            <!-- Our Future Goals Item Content Start -->
                            <div class="our-future-goals-item-content">
                                <h3>Online & Hybrid Learning</h3>
                                <p>To reach more student & support flexible learning we aim to launch online classes, video tutorials & structured.</p>
                            </div>
                            <!-- Our Future Goals Item Content End -->
                        </div>
                        <!-- Our Future Goals Item Header End -->

                        <div class="icon-box">
                            <img src="images/icon-our-future-goals-1.svg" alt="">
                        </div>
                    </div>
                    <!-- Our Future Goals Item End -->
                </div>

                <div class="col-xl-4 col-md-6">
                    <!-- Our Future Goals Item Start -->
                    <div class="our-future-goals-item box-2 wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Our Future Goals Item Header Start -->
                        <div class="our-future-goals-item-header">
                            <!-- Our Future Goals Item Image Start -->
                            <div class="our-future-goals-item-image">
                                <figure>
                                    <img src="images/our-future-goals-image-2.png" alt="">
                                </figure>
                            </div>
                            <!-- Our Future Goals Item Image End -->

                            <!-- Our Future Goals Item Content Start -->
                            <div class="our-future-goals-item-content">
                                <h3>Building a Stronger Martial Arts Community</h3>
                            </div>
                            <!-- Our Future Goals Item Content End -->
                        </div>
                        <!-- Our Future Goals Item Header End -->

                        <!-- Our Future Goals Item Header Start -->
                        <div class="our-future-goals-item-body">
                            <!-- Our Future Goals Item List Start -->
                            <div class="our-future-goals-item-list">
                                <ul>
                                    <li>Upgrading Our Facilities</li>
                                    <li>Wellness Integration</li>
                                    <li>International Certifications</li>
                                </ul>
                            </div>
                            <!-- Our Future Goals Item List End -->

                            <!-- Our Future Experience Circle Start -->
                            <div class="our-future-experience-circle">
                                <a href="contact.html">
                                    <img src="images/future-years-experience-circle.svg" alt="">
                                </a>
                            </div>
                            <!-- Our Future Experience Circle End -->
                        </div>
                    </div>
                    <!-- Our Future Goals Item End -->
                </div>

                <div class="col-xl-4">
                    <!-- Our Future Goals Item Box Start -->
                    <div class="our-future-goals-item-box wow fadeInUp" data-wow-delay="0.4s">
                        <!-- Our Future Goals Item Start -->
                        <div class="our-future-goals-item box-3">
                            <!-- Our Future Goals Item Content Start -->
                            <div class="our-future-goals-item-content">
                                <h3>Expanding Training Programs</h3>
                                <p>To reach more students & support flexible learning we to launch online classes, video tutorials & structured.</p>
                            </div>
                            <!-- Our Future Goals Item Content End -->

                            <!-- Our Future Goals Item List Start -->
                            <div class="our-future-goals-item-list">
                                <ul>
                                    <li>Confidence</li>
                                    <li>Coaching</li>
                                    <li>Skill</li>
                                </ul>
                            </div>
                            <!-- Our Future Goals Item List End -->
                        </div>
                        <!-- Our Future Goals Item End -->

                        <!-- Our Future Goals Item Start -->
                        <div class="our-future-goals-item box-4">
                            <div class="icon-box">
                                <img src="images/icon-our-future-goals-2.svg" alt="">
                            </div>

                            <!-- Our Future Goals Item Content Start -->
                            <div class="our-future-goals-item-content">
                                <h3>Expanding Training Programs</h3>
                                <p>To reach more students & support flexible learning we to launch online classes, video tutorials & structured.</p>
                            </div>
                            <!-- Our Future Goals Item Content End -->
                        </div>
                        <!-- Our Future Goals Item End -->
                    </div>
                    <!-- Our Future Goals Item Box End -->
                </div>

                <div class="col-lg-12">
                    <!-- Section Footer Text Start -->
                    <div class="section-footer-text wow fadeInUp" data-wow-delay="0.4s">
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
    <!-- Our Future Goals Section End -->

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

    <!-- Our Team Section Start -->
    <div class="our-team">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Expert Trainers</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Professional martial arts trainers guiding your journey</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <!-- Team Member Item Start -->
                    <div class="team-item wow fadeInUp">
                        <!-- Team Item Image Start -->
                        <div class="team-item-image">
                            <a href="team-single.html" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="images/team-1.jpg" alt="">
                                </figure>
                            </a>
                
                            <!-- Team Social Icon Start -->
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- Team Social Icon End -->
                        </div>
                        <!-- Team Item Image End -->
                
                        <!-- Team Item Content Start -->
                        <div class="team-item-content">
                            <h3><a href="team-single.html">Ethan Walker</a></h3>
                            <p>Martial Arts Trainer</p>
                        </div>
                        <!-- Team Item Content End -->
                    </div>
                    <!-- Team Member Item End -->
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <!-- Team Member Item Start -->
                    <div class="team-item wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Team Item Image Start -->
                        <div class="team-item-image">
                            <a href="team-single.html" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="images/team-2.jpg" alt="">
                                </figure>
                            </a>
                
                            <!-- Team Social Icon Start -->
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- Team Social Icon End -->
                        </div>
                        <!-- Team Item Image End -->
                
                        <!-- Team Item Content Start -->
                        <div class="team-item-content">
                            <h3><a href="team-single.html">Sophia Martinez</a></h3>
                            <p>Championship Coach</p>
                        </div>
                        <!-- Team Item Content End -->
                    </div>
                    <!-- Team Member Item End -->
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <!-- Team Member Item Start -->
                    <div class="team-item wow fadeInUp" data-wow-delay="0.4s">
                        <!-- Team Item Image Start -->
                        <div class="team-item-image">
                            <a href="team-single.html" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="images/team-3.jpg" alt="">
                                </figure>
                            </a>
                
                            <!-- Team Social Icon Start -->
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- Team Social Icon End -->
                        </div>
                        <!-- Team Item Image End -->
                
                        <!-- Team Item Content Start -->
                        <div class="team-item-content">
                            <h3><a href="team-single.html">Lucas Bennett</a></h3>
                            <p>Self-Defense Specialist</p>
                        </div>
                        <!-- Team Item Content End -->
                    </div>
                    <!-- Team Member Item End -->
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <!-- Team Member Item Start -->
                    <div class="team-item wow fadeInUp" data-wow-delay="0.6s">
                        <!-- Team Item Image Start -->
                        <div class="team-item-image">
                            <a href="team-single.html" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="images/team-4.jpg" alt="">
                                </figure>
                            </a>
                
                            <!-- Team Social Icon Start -->
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- Team Social Icon End -->
                        </div>
                        <!-- Team Item Image End -->
                
                        <!-- Team Item Content Start -->
                        <div class="team-item-content">
                            <h3><a href="team-single.html">Olivia Harris</a></h3>
                            <p>Junior Program Mentor</p>
                        </div>
                        <!-- Team Item Content End -->
                    </div>
                    <!-- Team Member Item End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Team Section End -->

    <!-- Our Fact Section Start -->
    <div class="our-fact bg-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6">
                    <!-- Our Fact Content Start -->
                    <div class="our-fact-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Our Fact</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Important facts that define our martial journey</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Our journey is built on dedication, discipline, and years of continuous growth. These key  highlight our achievements, values, and commitment.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Out Fact List Start -->
                        <div class="our-fact-list wow fadeInUp" data-wow-delay="0.4s">
                            <ul>
                                <li>Focus on Discipline & Respect</li>
                                <li>Structured Belt Progression System</li>
                                <li>Personalized Skill-Based Coaching</li>
                                <li>Competitive Training & Preparation</li>
                            </ul>
                        </div>
                        <!-- Out Fact List End -->

                        <!-- Out Fact Content Footer Start -->
                        <div class="our-fact-content-footer wow fadeInUp" data-wow-delay="0.6s">
                            <!-- Out Fact Content Button Start -->
                            <div class="our-fact-content-btn">
                                <a href="book-appointment.html" class="btn-default">Book Free Trial</a>
                            </div>
                            <!-- Out Fact Content Button End -->

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
                        <!-- Out Fact Content Footer End -->
                    </div>
                    <!-- Our Fact Content End -->
                </div>

                <div class="col-xl-6">
                    <!-- Our Fact Counter Item List Start -->
                    <div class="fact-counter-item-list wow fadeInUp">
                        <!-- Our Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <div class="fact-counter-item-image">
                                <img src="images/fact-item-bg-image.jpg" alt="">
                            </div>
                            <div class="our-fact-counter-body">
                                <div class="icon-box">
                                    <img src="images/icon-our-fact-1.svg" alt="">
                                </div>
                                <div class="fact-counter-item-content">
                                    <h2><span class="counter">800</span>+</h2>
                                    <p>Students Successfully Trained</p>
                                </div>
                            </div>
                        </div>
                        <!-- Our Fact Counter Item End -->

                        <!-- Our Fact Counter Item Start -->
                        <div class="fact-counter-item active">
                            <div class="fact-counter-item-image">
                                <img src="images/fact-item-bg-image.jpg" alt="">
                            </div>
                            <div class="our-fact-counter-body">
                                <div class="icon-box">
                                    <img src="images/icon-our-fact-2.svg" alt="">
                                </div>
                                <div class="fact-counter-item-content">
                                    <h2><span class="counter">150</span>+</h2>
                                    <p>Championship Wins national level</p>
                                </div>
                            </div>
                        </div>
                        <!-- Our Fact Counter Item End -->

                        <!-- Our Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <div class="fact-counter-item-image">
                                <img src="images/fact-item-bg-image.jpg" alt="">
                            </div>
                            <div class="our-fact-counter-body">
                                <div class="icon-box">
                                    <img src="images/icon-our-fact-3.svg" alt="">
                                </div>
                                <div class="fact-counter-item-content">
                                    <h2><span class="counter">50</span>+</h2>
                                    <p>Community Workshop Conducted</p>
                                </div>
                            </div>
                        </div>
                        <!-- Our Fact Counter Item End -->

                        <!-- Our Fact Counter Item Start -->
                        <div class="fact-counter-item">
                            <div class="fact-counter-item-image">
                                <img src="images/fact-item-bg-image.jpg" alt="">
                            </div>
                            <div class="our-fact-counter-body">
                                <div class="icon-box">
                                    <img src="images/icon-our-fact-4.svg" alt="">
                                </div>
                                <div class="fact-counter-item-content">
                                    <h2><span class="counter">12</span>+</h2>
                                    <p>Years of Training Excellence</p>
                                </div>
                            </div>
                        </div>
                        <!-- Our Fact Counter Item End -->
                    </div>
                    <!-- Our Fact Counter Item List End -->
                </div>

                <div class="col-lg-12">
                    <!-- Comapany Support Slider Start -->
                    <div class="company-supports-slider-box">
                        <!-- Comapany Support Content Start -->
                        <div class="company-supports-content wow fadeInUp" data-wow-delay="0.2s">
                            <hr>
                            <p>Our 500+ Karate School Support.</p>
                            <hr>
                        </div>
                        <!-- Comapany Support Content End -->

                        <!-- Comapany Support Slider Start -->
                        <div class="company-supports-slider">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-1.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
    
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-2.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
    
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-3.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
    
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-4.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
    
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-2.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
    
                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-4.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->

                                    <!-- Company Support Logo Start -->
                                    <div class="swiper-slide">
                                        <div class="company-supports-logo">
                                            <img src="images/company-supports-logo-3.svg" alt="">
                                        </div>
                                    </div>
                                    <!-- Comapany Support Logo End -->
                                </div>
                            </div>
                        </div>
                        <!-- Comapany Support Slider End -->
                    </div>
                    <!-- Comapany Support Slider End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Fact Section End -->

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