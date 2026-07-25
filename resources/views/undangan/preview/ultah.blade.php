<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($seo['title'] ?? null) ?: ('Ultah '.($undangan['nama_anak'] ?? ($undangan['nama_wanita'] ?? 'Si Kecil')).' — '.($template['nama'] ?? 'Ultah')) }}</title>
    @if (!empty($seo))
        <meta name="description" content="{{ $seo['desc'] }}">
        <link rel="canonical" href="{{ $seo['url'] }}">
        <meta property="og:title" content="{{ $seo['title'] }}">
        <meta property="og:description" content="{{ $seo['desc'] }}">
        <meta property="og:url" content="{{ $seo['url'] }}">
        @if (!empty($seo['image']))<meta property="og:image" content="{{ $seo['image'] }}">@endif
        <meta name="robots" content="index,follow">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --pink:#e85d75; --cream:#fff7f9; --ink:#3a2a32; --lilac:#6b5b95; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Nunito, sans-serif; background: linear-gradient(180deg, #ffe8ef, var(--cream)); color: var(--ink); min-height: 100vh; }
        .wrap { max-width: 420px; margin: 0 auto; padding: 40px 22px 80px; text-align: center; }
        .badge { display: inline-block; background: var(--pink); color: #fff; font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px; margin-bottom: 18px; }
        h1 { font-family: Fredoka, sans-serif; font-size: 2.1rem; line-height: 1.2; color: var(--pink); margin-bottom: 8px; }
        .usia { font-weight: 700; color: var(--lilac); margin-bottom: 18px; }
        .photo { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(232,93,117,.25); margin: 10px auto 22px; display: block; }
        .card { background: #fff; border-radius: 18px; padding: 18px 20px; margin: 12px 0; text-align: left; box-shadow: 0 8px 24px rgba(58,42,50,.06); }
        .card h3 { font-family: Fredoka, sans-serif; color: var(--pink); margin-bottom: 6px; }
        .card p { font-size: .92rem; line-height: 1.55; }
        .quote { font-style: italic; margin: 22px 0; opacity: .85; }
        .note { margin-top: 28px; font-size: .75rem; opacity: .55; }
        a.map { color: var(--lilac); font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
@php
    $nama = $undangan['nama_anak'] ?? ($undangan['nama_wanita'] ?? 'Si Kecil');
    $foto = $undangan['foto_anak'] ?? ($undangan['foto_wanita'] ?? null);
    $tgl = $undangan['tanggal_acara'] ?? ($undangan['tanggal_akad'] ?? null);
@endphp
<div class="wrap">
    <span class="badge">{{ $template['nama'] ?? 'Ultah Anak' }}</span>
    <p style="letter-spacing:.2em;text-transform:uppercase;font-size:.7rem;opacity:.6;margin-bottom:8px;">Undangan Spesial</p>
    <h1>Yeay! {{ $nama }}</h1>
    @if (!empty($undangan['usia']))
        <p class="usia">Ultah {{ $undangan['usia'] }}</p>
    @endif

    @if ($foto)
        <img class="photo" src="{{ asset($foto) }}" alt="{{ $nama }}">
    @endif

    @if (!empty($undangan['kutipan']))
        <p class="quote">“{{ $undangan['kutipan'] }}”</p>
    @endif

    <div class="card">
        <h3>Detail Acara</h3>
        <p>
            @if ($tgl){{ \Illuminate\Support\Carbon::parse($tgl)->locale('id')->translatedFormat('l, d F Y') }}<br>@endif
            @if (!empty($undangan['waktu_acara'] ?? $undangan['waktu_akad'] ?? null)){{ $undangan['waktu_acara'] ?? $undangan['waktu_akad'] }}<br>@endif
            @if (!empty($undangan['tempat_acara'] ?? $undangan['tempat_akad'] ?? null)){{ $undangan['tempat_acara'] ?? $undangan['tempat_akad'] }}<br>@endif
            @if (!empty($undangan['alamat_acara'] ?? $undangan['alamat_akad'] ?? null)){{ $undangan['alamat_acara'] ?? $undangan['alamat_akad'] }}@endif
        </p>
    </div>

    @if (!empty($undangan['ayah_host']) || !empty($undangan['ibu_host']) || !empty($undangan['ortu_wanita']))
        <div class="card">
            <h3>Dari</h3>
            <p>
                @if (!empty($undangan['ayah_host']) || !empty($undangan['ibu_host']))
                    {{ trim(($undangan['ayah_host'] ?? '').' & '.($undangan['ibu_host'] ?? ''), ' &') }}
                @else
                    {{ $undangan['ortu_wanita'] }}
                @endif
            </p>
        </div>
    @endif

    @if (!empty($undangan['dress_code']))
        <div class="card">
            <h3>Dress Code</h3>
            <p>{{ $undangan['dress_code'] }}</p>
        </div>
    @endif

    @if (!empty($undangan['jadwal']))
        <div class="card">
            <h3>Susunan Acara</h3>
            @foreach ($undangan['jadwal'] as $j)
                <p style="margin-top:8px">
                    <strong>{{ $j['jam'] ?? '' }}</strong>
                    @if (!empty($j['judul'])) — {{ $j['judul'] }}@endif
                    @if (!empty($j['deskripsi']))<br><span style="opacity:.75">{{ $j['deskripsi'] }}</span>@endif
                </p>
            @endforeach
        </div>
    @endif

    @if (!empty($undangan['cerita']))
        <div class="card">
            <h3>Perjalanan</h3>
            @foreach ($undangan['cerita'] as $c)
                <p style="margin-top:8px">
                    <strong>{{ $c['tahun'] ?? '' }} — {{ $c['judul'] ?? '' }}</strong>
                    @if (!empty($c['deskripsi']))<br><span style="opacity:.75">{{ $c['deskripsi'] }}</span>@endif
                </p>
            @endforeach
        </div>
    @endif

    @if (!empty($undangan['maps_url']))
        <p style="margin-top:14px"><a class="map" href="{{ $undangan['maps_url'] }}" target="_blank" rel="noopener">Buka Peta →</a></p>
    @endif

    @if (!empty($template['demo_url']))
        <p style="margin-top:18px"><a class="map" href="{{ $template['demo_url'] }}" target="_blank" rel="noopener">Lihat demo interaktif penuh →</a></p>
    @endif

    <p class="note">Preview data dari admin. Template interaktif penuh (balon, lilin, game) menyusul diintegrasikan.</p>
</div>
</body>
</html>
