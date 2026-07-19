<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rayakan Momen — Undangan Digital Wedding, Ultah &amp; Couple</title>
    <meta name="description" content="Rayakan Momen — marketplace undangan digital untuk pernikahan, ulang tahun anak, dan kejutan couple. Lihat demo live, pilih template, lalu pesan lewat WhatsApp.">
    <meta name="keywords" content="undangan digital, undangan pernikahan, undangan ulang tahun anak, undangan couple, wedding invitation, Rayakan Momen">
    <meta name="author" content="Rayakan Momen">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Rayakan Momen — Undangan Digital Wedding, Ultah &amp; Couple">
    <meta property="og:description" content="Pilih template undangan digital: wedding, ultah anak, atau couple. Demo live tersedia — siap share lewat WA.">
    <meta property="og:image" content="{{ cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_1200,c_fill,g_auto') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Rayakan Momen">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Rayakan Momen — Jasa Undangan Digital">
    <meta name="twitter:description" content="Undangan digital elegan untuk wedding, ultah anak, dan couple. Mulai dari Rp 150.000.">
    <meta name="twitter:image" content="{{ cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_1200,c_fill,g_auto') }}">
    <link rel="canonical" href="{{ url('/') }}">

    <link rel="preconnect" href="https://res.cloudinary.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- LCP: preload hero mobile-first --}}
    <link rel="preload" as="image"
          href="{{ cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_960,c_limit') }}"
          imagesrcset="{{ cdn_srcset('hero_wedding', [640, 960, 1280, 1920]) }}"
          imagesizes="100vw"
          fetchpriority="high">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Fonts non-blocking --}}
    <link rel="preload" as="style"
          href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap">
    </noscript>

    {{-- Icons non-blocking (full FA is heavy) --}}
    <link rel="preload" as="style"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </noscript>
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

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
