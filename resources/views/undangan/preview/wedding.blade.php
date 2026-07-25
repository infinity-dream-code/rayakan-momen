<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($seo['title'] ?? null) ?: (($undangan['nama_wanita'] ?? '').' & '.($undangan['nama_pria'] ?? '').' — '.($template['nama'] ?? 'Wedding')) }}</title>
    @if (!empty($seo))
        <meta name="description" content="{{ $seo['desc'] }}">
        <link rel="canonical" href="{{ $seo['url'] }}">
        <meta property="og:title" content="{{ $seo['title'] }}">
        <meta property="og:description" content="{{ $seo['desc'] }}">
        <meta property="og:url" content="{{ $seo['url'] }}">
        @if (!empty($seo['image']))
            <meta property="og:image" content="{{ $seo['image'] }}">
        @endif
        <meta name="robots" content="index,follow">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#3b2418; --cream:#f7f0e6; --gold:#a8843a; --wine:#6b3a2a; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Jost, sans-serif; background: var(--cream); color: var(--ink); min-height: 100vh; }
        .wrap { max-width: 420px; margin: 0 auto; padding: 48px 24px 80px; text-align: center; }
        .eyebrow { letter-spacing: .25em; text-transform: uppercase; font-size: .65rem; color: var(--gold); margin-bottom: 12px; }
        h1 { font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; font-weight: 500; line-height: 1.2; margin-bottom: 8px; }
        .amp { color: var(--gold); font-style: italic; }
        .parents { font-size: .85rem; opacity: .75; margin: 18px 0; line-height: 1.6; }
        .quote { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1.05rem; line-height: 1.7; margin: 28px 0; opacity: .9; }
        .card { background: #fff; border: 1px solid rgba(168,132,58,.25); border-radius: 12px; padding: 20px; margin: 14px 0; text-align: left; }
        .card h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; margin-bottom: 8px; color: var(--wine); }
        .card p { font-size: .9rem; line-height: 1.55; opacity: .85; }
        .note { margin-top: 32px; font-size: .75rem; opacity: .55; }
        .badge { display: inline-block; font-size: .65rem; letter-spacing: .12em; text-transform: uppercase; padding: 6px 12px; border-radius: 999px; background: var(--wine); color: #fff; margin-bottom: 20px; }
        a.map { color: var(--gold); font-weight: 500; text-decoration: none; font-size: .85rem; }
    </style>
</head>
<body>
<div class="wrap">
    <span class="badge">{{ $template['nama'] ?? 'Adat Jawa' }} · Preview</span>
    <p class="eyebrow">The Wedding Of</p>
    <h1>{{ $undangan['nama_wanita'] ?? 'Mempelai' }} <span class="amp">&amp;</span> {{ $undangan['nama_pria'] ?? 'Mempelai' }}</h1>

    @if (!empty($undangan['ortu_wanita']) || !empty($undangan['ortu_pria']))
        <div class="parents">
            @if (!empty($undangan['ortu_wanita']))
                <p>Putri dari<br>{{ $undangan['ortu_wanita'] }}</p>
            @endif
            @if (!empty($undangan['ortu_pria']))
                <p style="margin-top:10px">Putra dari<br>{{ $undangan['ortu_pria'] }}</p>
            @endif
        </div>
    @endif

    @if (!empty($undangan['kutipan']))
        <p class="quote">“{{ $undangan['kutipan'] }}”
            @if (!empty($undangan['kutipan_sumber']))<br><small>{{ $undangan['kutipan_sumber'] }}</small>@endif
        </p>
    @endif

    @if (!empty($undangan['tanggal_akad']) || !empty($undangan['tempat_akad']))
        <div class="card">
            <h3>Akad Nikah</h3>
            <p>
                @if (!empty($undangan['tanggal_akad'])){{ \Illuminate\Support\Carbon::parse($undangan['tanggal_akad'])->locale('id')->translatedFormat('l, d F Y') }}<br>@endif
                @if (!empty($undangan['waktu_akad'])){{ $undangan['waktu_akad'] }}<br>@endif
                @if (!empty($undangan['tempat_akad'])){{ $undangan['tempat_akad'] }}<br>@endif
                @if (!empty($undangan['alamat_akad'])){{ $undangan['alamat_akad'] }}@endif
            </p>
        </div>
    @endif

    @if (!empty($undangan['tanggal_resepsi']) || !empty($undangan['tempat_resepsi']))
        <div class="card">
            <h3>Resepsi</h3>
            <p>
                @if (!empty($undangan['tanggal_resepsi'])){{ \Illuminate\Support\Carbon::parse($undangan['tanggal_resepsi'])->locale('id')->translatedFormat('l, d F Y') }}<br>@endif
                @if (!empty($undangan['waktu_resepsi'])){{ $undangan['waktu_resepsi'] }}<br>@endif
                @if (!empty($undangan['tempat_resepsi'])){{ $undangan['tempat_resepsi'] }}<br>@endif
                @if (!empty($undangan['alamat_resepsi'])){{ $undangan['alamat_resepsi'] }}@endif
            </p>
        </div>
    @endif

    @if (!empty($undangan['maps_url']))
        <p style="margin-top:16px"><a class="map" href="{{ $undangan['maps_url'] }}" target="_blank" rel="noopener">Lihat Lokasi →</a></p>
    @endif

    <p class="note">Halaman preview sementara. Data dari admin sudah aktif — pastikan file template &amp; folder assets terpasang di server.</p>
</div>
</body>
</html>
