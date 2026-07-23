@php
    $categories = $categories ?? config('templates.categories', []);
    $catalog = $catalog ?? app(\App\Repositories\CatalogRepository::class);
    $allTemplates =
        $allTemplates ?? collect($catalog->templates())->filter(fn($t) => $t['aktif_katalog'] ?? true)->all();
    $activeKat = $activeKat ?? 'all';
    $waNumber = '6285199641845';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Undangan — Rayakan Momen</title>
    <meta name="description"
        content="Lihat katalog undangan digital Rayakan Momen: pernikahan, ulang tahun anak, dan untuk pasangan. Pilih desain, lihat demo, pesan via WhatsApp.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ route('katalog') }}">
    <link rel="preconnect" href="https://res.cloudinary.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        $cssPath = public_path('css/app.css');
        $jsPath = public_path('js/app.js');
        $appCssVer = is_file($cssPath) ? filemtime($cssPath) : time();
        $appJsVer = is_file($jsPath) ? filemtime($jsPath) : time();
    @endphp
    @include('partials.landing-tw-inline')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $appCssVer }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon">
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600&display=swap">
    </noscript>
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </noscript>
    <style>
        body.katalog-page {
            background: #f7f4ef;
        }

        .katalog-banner {
            background: linear-gradient(135deg, #0e1320 0%, #1a2234 100%);
            color: #fff;
            padding: 5.5rem 0 2rem;
        }

        .katalog-banner h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.75rem, 5vw, 2.5rem);
            margin: 0;
            font-weight: 600;
        }

        .katalog-banner h1 em {
            font-style: italic;
            color: #e8d5a3;
            font-weight: 400;
        }

        .katalog-banner p {
            margin: .6rem 0 0;
            color: rgba(255, 255, 255, .55);
            font-size: .9rem;
        }

        .katalog-toolbar {
            position: sticky;
            top: 4rem;
            z-index: 40;
            background: rgba(247, 244, 239, .92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(201, 168, 76, .18);
            padding: .85rem 0;
        }

        @media (min-width: 768px) {
            .katalog-toolbar {
                top: 4.5rem;
            }
        }

        .katalog-chips {
            display: flex;
            gap: .5rem;
            overflow-x: auto;
            padding-bottom: .15rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .katalog-chips::-webkit-scrollbar {
            display: none;
        }

        .katalog-chip {
            flex: 0 0 auto;
            border: 0;
            border-radius: 999px;
            padding: .55rem 1rem;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: #fff;
            color: #1a2234;
            border: 1px solid #e5e0d8;
            cursor: pointer;
            font-family: inherit;
        }

        .katalog-chip.is-active {
            background: #1a2234;
            color: #e8d5a3;
            border-color: #1a2234;
        }

        .katalog-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
            padding: 1.25rem 0 3rem;
        }

        @media (min-width: 768px) {
            .katalog-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1.25rem;
            }
        }

        @media (min-width: 1100px) {
            .katalog-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .produk-card {
            background: #fff;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(201, 168, 76, .18);
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 24px rgba(26, 34, 52, .04);
        }

        .produk-card.is-hidden {
            display: none;
        }

        .produk-card__media {
            position: relative;
            aspect-ratio: 3/4;
            background: #1a2234;
            overflow: hidden;
        }

        .produk-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .produk-card__badge {
            position: absolute;
            top: .65rem;
            left: .65rem;
            background: rgba(255, 255, 255, .95);
            color: #1a2234;
            font-size: .65rem;
            font-weight: 600;
            padding: .25rem .55rem;
            border-radius: .4rem;
        }

        .produk-card__body {
            padding: .85rem .8rem 1rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .produk-card__kat {
            font-size: .65rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #a8843a;
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .produk-card__title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            margin: 0 0 .55rem;
            color: #1a2234;
            line-height: 1.25;
        }

        @media (min-width: 768px) {
            .produk-card__title {
                font-size: 1.15rem;
            }
        }

        .produk-card__mulai {
            font-size: .62rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: .15rem;
        }

        .produk-card__price-row {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: .35rem .5rem;
            margin-bottom: .85rem;
        }

        .produk-card__price {
            font-size: 1rem;
            font-weight: 700;
            color: #1a2234;
        }

        .produk-card__old {
            font-size: .78rem;
            color: #aaa;
            text-decoration: line-through;
        }

        .produk-card__actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .4rem;
            margin-top: auto;
        }

        .produk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .55rem .4rem;
            border-radius: .55rem;
            font-size: .75rem;
            font-weight: 600;
            text-decoration: none;
            font-family: inherit;
            border: 1px solid transparent;
        }

        .produk-btn--demo {
            background: #fff;
            color: #1a2234;
            border-color: #d8d2c8;
        }

        .produk-btn--pakai {
            background: #1a2234;
            color: #e8d5a3;
        }

        .produk-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem 1rem;
            color: #999;
            display: none;
        }

        .produk-empty.is-show {
            display: block;
        }

        .katalog-back {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: rgba(255, 255, 255, .65);
            text-decoration: none;
            font-size: .8rem;
            margin-bottom: 1rem;
        }

        .katalog-back:hover {
            color: #e8d5a3;
        }
    </style>
</head>

<body class="font-sans antialiased katalog-page">
    @include('partials.navbar')

    <div class="katalog-banner">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <a href="{{ route('landing') }}" class="katalog-back"><i class="fa-solid fa-arrow-left"></i> Beranda</a>
            <h1>Curated <em>Collection</em></h1>
            <p>Pilih desain undangan digital — lihat demo, lalu pesan via WhatsApp.</p>
        </div>
    </div>

    <div class="katalog-toolbar">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="katalog-chips" id="katalogFilters" role="tablist">
                <button type="button" class="katalog-chip {{ $activeKat === 'all' ? 'is-active' : '' }}"
                    data-filter="all">Semua</button>
                @foreach ($categories as $kat)
                    <button type="button" class="katalog-chip {{ $activeKat === $kat['id'] ? 'is-active' : '' }}"
                        data-filter="{{ $kat['id'] }}">{{ $kat['nama'] }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="katalog-grid" id="katalogGrid">
            @foreach ($allTemplates as $key => $t)
                @php
                    $kat = $categories[$t['kategori']] ?? null;
                    $harga = (int) ($t['harga'] ?? 0);
                    $final = (int) ($t['harga_final'] ?? $harga);
                    $diskon = (float) ($t['diskon_persen'] ?? 0);
                    $punyaDiskon = !empty($t['punya_diskon']);
                    $hargaFinalLabel = $final > 0 ? $catalog->formatRupiah($final) : null;
                    $hargaAwalLabel = $harga > 0 ? $catalog->formatRupiah($harga) : null;
                    $waText = rawurlencode(
                        'Halo Rayakan Momen, saya mau pesan undangan ' .
                            $t['nama'] .
                            ($hargaFinalLabel ? ' (' . $hargaFinalLabel . ')' : ''),
                    );
                    $previewFallback = match ($t['kategori'] ?? '') {
                        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        default => '',
                    };
                    $previewSrc = !empty($t['preview']) ? $t['preview'] : $previewFallback;
                    $hidden = $activeKat !== 'all' && ($t['kategori'] ?? '') !== $activeKat;
                @endphp
                <article class="produk-card {{ $hidden ? 'is-hidden' : '' }}" data-category="{{ $t['kategori'] }}">
                    <div class="produk-card__media">
                        @if ($previewSrc)
                            <img src="{{ $previewSrc }}" alt="{{ $t['nama'] }}" loading="lazy" decoding="async">
                        @endif
                        @if ($punyaDiskon)
                            <span class="produk-card__badge">-{{ (int) $diskon }}%</span>
                        @endif
                    </div>
                    <div class="produk-card__body">
                        <div class="produk-card__kat">{{ $kat['nama'] ?? 'Undangan' }}</div>
                        <h2 class="produk-card__title">{{ $t['nama'] }}</h2>
                        @if ($hargaFinalLabel)
                            <div class="produk-card__mulai">Mulai dari</div>
                            <div class="produk-card__price-row">
                                <span class="produk-card__price">{{ $hargaFinalLabel }}</span>
                                @if ($punyaDiskon && $hargaAwalLabel)
                                    <span class="produk-card__old">{{ $hargaAwalLabel }}</span>
                                @endif
                            </div>
                        @endif
                        <div class="produk-card__actions">
                            @if (!empty($t['demo_url']))
                                <a href="{{ $t['demo_url'] }}" target="_blank" rel="noopener noreferrer"
                                    class="produk-btn produk-btn--demo">Demo</a>
                            @else
                                <span class="produk-btn produk-btn--demo" style="opacity:.45;cursor:default">Demo</span>
                            @endif
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank"
                                rel="noopener noreferrer" class="produk-btn produk-btn--pakai">Pakai</a>
                        </div>
                    </div>
                </article>
            @endforeach
            <div class="produk-empty" id="katalogEmpty">Tidak ada desain di kategori ini.</div>
        </div>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/app.js') }}?v={{ $appJsVer }}" defer></script>
    <script>
        (function() {
            var chips = document.querySelectorAll('#katalogFilters .katalog-chip');
            var cards = document.querySelectorAll('#katalogGrid .produk-card');
            var empty = document.getElementById('katalogEmpty');

            function setFilter(cat) {
                chips.forEach(function(c) {
                    c.classList.toggle('is-active', c.getAttribute('data-filter') === cat);
                });
                var shown = 0;
                cards.forEach(function(card) {
                    var match = cat === 'all' || card.getAttribute('data-category') === cat;
                    card.classList.toggle('is-hidden', !match);
                    if (match) shown++;
                });
                if (empty) empty.classList.toggle('is-show', shown === 0);

                try {
                    var url = new URL(window.location.href);
                    if (cat === 'all') url.searchParams.delete('kategori');
                    else url.searchParams.set('kategori', cat);
                    window.history.replaceState({}, '', url);
                } catch (e) {}
            }

            chips.forEach(function(chip) {
                chip.addEventListener('click', function() {
                    setFilter(this.getAttribute('data-filter') || 'all');
                });
            });
        })();
    </script>
</body>

</html>
