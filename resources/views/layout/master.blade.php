<!DOCTYPE html>
<html lang="zxx">
<head>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<meta name="description" content="{{ $seoDescription ?? 'Tapak Suci Sport Academy - Website resmi akademi pencak silat Tapak Suci. Profil, galeri, blog, pendaftaran siswa, dan portal pembayaran.' }}">
	<meta name="keywords" content="{{ $seoKeywords ?? 'pencak silat, tapak suci, latihan silat, pendaftaran silat, akademi bela diri' }}">
	<meta name="author" content="Tapak Suci Sport Academy">
	<!-- Page Title -->
    <title>{{ $seoTitle ?? 'Tapak Suci Sport Academy' }}</title>
	<!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $seoTitle ?? 'Tapak Suci Sport Academy' }}">
    <meta property="og:description" content="{{ $seoDescription ?? 'Tapak Suci Sport Academy - Website resmi akademi pencak silat Tapak Suci.' }}">
    <meta property="og:image" content="{{ $seoImage ?? asset('images/og-default.jpg') }}">
	<!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle ?? 'Tapak Suci Sport Academy' }}">
    <meta name="twitter:description" content="{{ $seoDescription ?? 'Tapak Suci Sport Academy - Website resmi akademi pencak silat Tapak Suci.' }}">
    <meta name="twitter:image" content="{{ $seoImage ?? asset('images/og-default.jpg') }}">
	<!-- Favicon Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
	<!-- Google Fonts Css-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Headline:wght@200..700&display=swap" rel="stylesheet">
	<!-- Bootstrap Css -->
	<link href="{{  asset('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<!-- SlickNav Css -->
	<link href="{{ asset('css/slicknav.min.css') }}" rel="stylesheet">
	<!-- Swiper Css -->
	<link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
	<!-- Font Awesome Icon Css-->
	<link href="{{ asset('css/all.min.css') }}" rel="stylesheet" media="screen">
	<!-- Animated Css -->
	<link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <!-- Magnific Popup Core Css File -->
	<link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
	<!-- Mouse Cursor Css File -->
	<link rel="stylesheet" href="{{ asset('css/mousecursor.css') }}">
	<!-- Main Custom Css -->
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="screen">
</head>
<body>
    <!-- Preloader Start -->
	<div class="preloader">
		<div class="loading-container">
			<div class="loading"></div>
			<div id="loading-icon"><img src="{{ asset('images/loader.svg') }}" alt=""></div>
		</div>
	</div>
	<!-- Preloader End -->

    <!-- Header Start -->
	@include('layout.header')
	<!-- Header End -->

    <!-- Hero Section Start -->
    @hasSection('hero')
      @yield('hero')
    @elseif (request()->is('/'))
      @include('layout.herosection.video')
    @else
      @include('layout.herosection.page-header')
    @endif
    <!-- Hero Section End -->
    @yield('content')
    <!-- Footer Section Start -->
    @include('layout.footer')
    <!-- Footer Section End -->

    <!-- Jquery Library File -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js file -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <!-- Validator js file -->
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <!-- SlickNav js file -->
    <script src="{{ asset('js/jquery.slicknav.js') }}"></script>
    <!-- Swiper js file -->
    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
    <!-- Counter js file -->
    <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
    <!-- Magnific js file -->
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <!-- SmoothScroll -->
    <script src="{{ asset('js/SmoothScroll.js') }}"></script>
    <!-- Parallax js -->
    <script src="{{ asset('js/parallaxie.js') }}"></script>
    <!-- MagicCursor js file -->
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <script src="{{ asset('js/magiccursor.js') }}"></script>
    <!-- Text Effect js file -->
    <script src="{{ asset('js/SplitText.min.js') }}"></script>
    <script src="{{ asset('js/ScrollTrigger.min.js') }}"></script>
    <!-- YTPlayer js File -->
    <script src="{{ asset('js/jquery.mb.YTPlayer.min.js') }}"></script>
    <!-- Wow js file -->
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <!-- Main Custom js file -->
    <script src="{{ asset('js/function.js') }}"></script>

    {{-- Analytics Hook (GA4 / Umami) --}}
    @if (app()->environment('production') && env('GA_MEASUREMENT_ID'))
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_MEASUREMENT_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GA_MEASUREMENT_ID') }}');
        </script>
    @endif

    @if (app()->environment('production') && env('UMAMI_WEBSITE_ID') && env('UMAMI_SCRIPT_URL'))
        <!-- Umami Analytics -->
        <script defer src="{{ env('UMAMI_SCRIPT_URL') }}" data-website-id="{{ env('UMAMI_WEBSITE_ID') }}"></script>
    @endif
</body>
</html>