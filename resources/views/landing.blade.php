<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web Untal — Undangan Digital Wedding, Ultah &amp; Couple</title>
    <meta name="description" content="Web Untal — marketplace undangan digital untuk pernikahan, ulang tahun anak, dan kejutan couple. Lihat demo live, pilih template, lalu pesan lewat WhatsApp.">
    <meta name="keywords" content="undangan digital, undangan pernikahan, undangan ulang tahun anak, undangan couple, wedding invitation, Web Untal">
    <meta name="author" content="Web Untal">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Web Untal — Undangan Digital Wedding, Ultah &amp; Couple">
    <meta property="og:description" content="Pilih template undangan digital: wedding, ultah anak, atau couple. Demo live tersedia — siap share lewat WA.">
    <meta property="og:image" content="https://images.unsplash.com/photo-1519741497674-611481863552?w=1200&q=80">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Web Untal">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Web Untal — Jasa Undangan Digital">
    <meta name="twitter:description" content="Undangan digital elegan untuk wedding, ultah anak, dan couple. Mulai dari Rp 150.000.">
    <meta name="twitter:image" content="https://images.unsplash.com/photo-1519741497674-611481863552?w=1200&q=80">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script type="tailwind-config">
    {
        "theme": {
            "extend": {
                "colors": {
                    "charcoal": "#12161f",
                    "navy": {
                        "DEFAULT": "#1a2234",
                        "soft": "#243049",
                        "deep": "#0e1320"
                    },
                    "gold": {
                        "DEFAULT": "#c9a84c",
                        "light": "#e8d5a3",
                        "dark": "#a8843a"
                    },
                    "champagne": "#d4b896",
                    "blush": "#f3e8e4",
                    "ivory": "#faf7f2"
                },
                "fontFamily": {
                    "display": ["Playfair Display", "serif"],
                    "sans": ["Poppins", "sans-serif"]
                }
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
