	<header class="main-header">
		<div class="header-sticky bg-section">
			<nav class="navbar navbar-expand-lg">
				<div class="container">
					<!-- Logo Start -->
					<a class="navbar-brand" href="./">
						<img src="{{ asset('images/logo.svg') }}" alt="Logo">
					</a>
					<!-- Logo End -->

                    <!-- Main Menu Start -->
                    <div class="collapse navbar-collapse main-menu">
                        <div class="nav-menu-wrapper">
                            <ul class="navbar-nav mr-auto" id="menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">Tentang Kami</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('program') }}">Programs</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('blog') }}">Blog</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('gallery') }}">Gallery</a></li>
                                <li class="nav-item submenu"><a class="nav-link" href="#">Pages</a>
                                    <ul>
                                        <li class="nav-item"><a class="nav-link" href="program-single.html">Program Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="blog-single.html">Blog Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="team.html">Our Team</a></li>
                                        <li class="nav-item"><a class="nav-link" href="team-single.html">Team Details</a></li>
                                        <li class="nav-item"><a class="nav-link" href="pricing.html">Pricing Plan</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('testimonials') }}">Testimoni</a></li>
                                        <li class="nav-item"><a class="nav-link" href="image-gallery.html">Image Gallery</a></li>
                                        <li class="nav-item"><a class="nav-link" href="video-gallery.html">Video Gallery</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('calendar') }}">Kalender Kegiatan</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('schedules') }}">Jadwal Latihan</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('faqs') }}">FAQ</a></li>
                                        <li class="nav-item"><a class="nav-link" href="404.html">404</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('contact-us') }}">Contact Us</a></li>
                                <li class="nav-item highlighted-menu"><a class="nav-link" href="{{ route('registration') }}">Daftar Sekarang</a></li>
                            </ul>
                        </div>
                        
                        <!-- Header Btn Start -->
                        <div class="header-btn">
                            <a href="{{ route('registration') }}" class="btn-default btn-highlighted">Daftar Sekarang</a>
                        </div>
                        <!-- Header Btn End -->
                    </div>
					<!-- Main Menu End -->
					<div class="navbar-toggle"></div>
				</div>
			</nav>
			<div class="responsive-menu"></div>
		</div>
	</header>
	</header>