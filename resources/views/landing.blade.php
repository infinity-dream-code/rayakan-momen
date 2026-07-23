<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rayakan Momen — Undangan Digital Pernikahan, Ulang Tahun &amp; Pasangan</title>
    <meta name="description"
        content="Rayakan Momen — undangan digital siap 1 hari. Dashboard RSVP, link personal per tamu, untuk pernikahan, ulang tahun anak, dan kejutan pasangan.">
    <meta name="keywords"
        content="undangan digital, undangan pernikahan, undangan ulang tahun anak, undangan pasangan, Rayakan Momen">
    <meta name="author" content="Rayakan Momen">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Rayakan Momen — Undangan Digital Pernikahan, Ulang Tahun &amp; Pasangan">
    <meta property="og:description"
        content="Pengerjaan biasanya 1 hari. Pantau RSVP di dashboard, buat &amp; bagikan link personal per tamu.">
    <meta property="og:image"
        content="https://res.cloudinary.com/zujq4fvj/image/upload/v1784771473/RAYAKAN_MOMEN_veeyix.png">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Rayakan Momen">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Rayakan Momen — Jasa Undangan Digital">
    <meta name="twitter:description"
        content="Undangan digital untuk pernikahan, ulang tahun anak, dan kejutan pasangan. Mulai dari Rp 150.000.">
    <meta name="twitter:image"
        content="https://res.cloudinary.com/zujq4fvj/image/upload/v1784771473/RAYAKAN_MOMEN_veeyix.png">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://res.cloudinary.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- LCP: preload hero mobile-first --}}
    <link rel="preload" as="image" href="{{ cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_960,c_limit') }}"
        imagesrcset="{{ cdn_srcset('hero_wedding', [640, 960, 1280, 1920]) }}" imagesizes="100vw" fetchpriority="high">

    {{-- Utilities inlined di Blade agar layout tidak tergantung upload CSS baru --}}
    @php
        $cssPath = public_path('css/app.css');
        $jsPath = public_path('js/app.js');
        $appCssVer = is_file($cssPath) ? filemtime($cssPath) : time();
        $appJsVer = is_file($jsPath) ? filemtime($jsPath) : time();
    @endphp
    @include('partials.landing-tw-inline')

    {{-- Fonts non-blocking --}}
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap">
    </noscript>

    {{-- Icons non-blocking (full FA is heavy) --}}
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </noscript>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}?v={{ $appCssVer }}">
    @include('partials.landing-dark-text')
</head>

<body class="font-sans antialiased">
    @include('partials.navbar')

    <main>
        @include('partials.hero')
        @include('partials.value')
        @include('partials.portfolio')
        @include('partials.features')
        @include('partials.process')
        @include('partials.testimonials')
        @include('partials.faq')
        @include('partials.cta')
    </main>

    @include('partials.footer')

    @include('partials.campaign-popup')

    <script src="{{ asset('js/app.js') }}?v={{ $appJsVer }}"></script>
</body>

</html>
