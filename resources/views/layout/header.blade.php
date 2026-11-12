<header class="main-header">
	<div class="header-sticky bg-section">
		<nav class="navbar navbar-expand-lg">
			<div class="container">
				<!-- Logo Start -->
				<a class="navbar-brand" href="{{ route('home') }}">
					<img src="{{ $site['assets']['logo'] }}" alt="{{ $site['identity']['name'] }}">
				</a>
				<!-- Logo End -->

				<!-- Main Menu Start -->
				<div class="collapse navbar-collapse main-menu">
					<div class="nav-menu-wrapper">
						<ul class="navbar-nav mr-auto" id="menu">
							<li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('about') }}">Tentang Kami</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('program') }}">Program</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('schedules') }}">Jadwal</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('blog') }}">Blog</a></li>
							<li class="nav-item submenu"><a class="nav-link" href="#">Informasi</a>
								<ul>
									<li class="nav-item"><a class="nav-link" href="{{ route('achievements') }}">Prestasi Atlet</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('gallery') }}">Galeri</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('testimonials') }}">Testimoni</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('faqs') }}">FAQ</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('calendar') }}">Kalender Kegiatan</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('student.portal') }}">Portal Siswa (REMAJA)</a></li>
									<li class="nav-item"><a class="nav-link" href="{{ route('payments.portal') }}">Pembayaran Iuran</a></li>
								</ul>
							</li>
							<li class="nav-item"><a class="nav-link" href="{{ route('contact-us') }}">Kontak</a></li>
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
