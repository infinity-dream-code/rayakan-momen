<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($seo['title'] ?? null) ?: ('Untukmu, '.($undangan['nama_wanita'] ?? 'Sayang').' — '.($template['nama'] ?? 'Couple')) }}</title>
    @if (!empty($seo))
        <meta name="description" content="{{ $seo['desc'] }}">
        <link rel="canonical" href="{{ $seo['url'] }}">
        <meta property="og:title" content="{{ $seo['title'] }}">
        <meta property="og:description" content="{{ $seo['desc'] }}">
        <meta property="og:url" content="{{ $seo['url'] }}">
        @if (!empty($seo['image']))<meta property="og:image" content="{{ $seo['image'] }}">@endif
        <meta name="robots" content="index,follow">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --rose:#c45c7a; --ink:#2a1f24; --cream:#fff5f7; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Outfit, sans-serif; background: radial-gradient(circle at 30% 20%, #ffe0ea, var(--cream) 55%); color: var(--ink); min-height: 100vh; }
        .wrap { max-width: 420px; margin: 0 auto; padding: 48px 24px 80px; text-align: center; }
        .badge { display: inline-block; border: 1px solid rgba(196,92,122,.35); color: var(--rose); font-size: .7rem; letter-spacing: .14em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px; margin-bottom: 22px; }
        .script { font-family: 'Great Vibes', cursive; font-size: 2.8rem; color: var(--rose); line-height: 1.2; margin-bottom: 8px; }
        .sub { opacity: .7; margin-bottom: 24px; }
        .letter { background: #fff; border-radius: 16px; padding: 28px 22px; box-shadow: 0 16px 40px rgba(196,92,122,.12); text-align: left; line-height: 1.7; }
        .letter h2 { font-family: 'Great Vibes', cursive; font-size: 2rem; color: var(--rose); text-align: center; margin-bottom: 14px; }
        .photos { display: flex; gap: 12px; justify-content: center; margin: 22px 0; }
        .photos img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .note { margin-top: 28px; font-size: .75rem; opacity: .55; }
        a.demo { color: var(--rose); font-weight: 500; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrap">
    <span class="badge">{{ $template['nama'] ?? 'Couple' }}</span>
    <p class="script">Hai, {{ $undangan['nama_wanita'] ?? 'Sayang' }}</p>
    <p class="sub">Dari {{ $undangan['nama_pria'] ?? 'Aku' }}</p>

    <div class="photos">
        @if (!empty($undangan['foto_pria']))
            <img src="{{ asset($undangan['foto_pria']) }}" alt="">
        @endif
        @if (!empty($undangan['foto_wanita']))
            <img src="{{ asset($undangan['foto_wanita']) }}" alt="">
        @endif
    </div>

    <div class="letter">
        <h2>Surat untukmu</h2>
        <p>{{ $undangan['kutipan'] ?? ($undangan['pesan_janji'] ?? 'Ada kejutan spesial untukmu.') }}</p>
        @if (!empty($undangan['pesan_janji']) && ($undangan['pesan_janji'] !== ($undangan['kutipan'] ?? '')))
            <p style="margin-top:14px">{{ $undangan['pesan_janji'] }}</p>
        @endif
        @if (!empty($undangan['tanggal_spesial'] ?? $undangan['tanggal_akad'] ?? null))
            <p style="margin-top:16px;font-size:.85rem;opacity:.7">
                Tanggal spesial:
                {{ \Illuminate\Support\Carbon::parse($undangan['tanggal_spesial'] ?? $undangan['tanggal_akad'])->locale('id')->translatedFormat('d F Y') }}
            </p>
        @endif
    </div>

    @if (!empty($undangan['alasan_sayang']))
        <div class="letter" style="margin-top:16px">
            <h2>Alasan aku sayang kamu</h2>
            <ol style="padding-left:18px;margin:0">
                @foreach ($undangan['alasan_sayang'] as $a)
                    <li style="margin:6px 0">{{ $a }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @if (!empty($template['demo_url']))
        <p style="margin-top:20px"><a class="demo" href="{{ $template['demo_url'] }}" target="_blank" rel="noopener">Buka demo pengalaman penuh →</a></p>
    @endif

    <p class="note">Preview data dari admin. Fitur interaktif (amplop, lilin, gelembung) menyusul diintegrasikan dari demo.</p>
</div>
</body>
</html>
