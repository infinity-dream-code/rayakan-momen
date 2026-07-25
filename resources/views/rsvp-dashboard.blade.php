<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard RSVP — {{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon">
    <style>
        :root {
            --navy: #1a2234;
            --navy-deep: #0e1320;
            --gold: #c9a84c;
            --gold-soft: #e8d5a3;
            --ivory: #faf7f2;
            --line: #eee8df;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(165deg, #f4f1eb 0%, #ebe4d8 45%, #f7f4ef 100%);
            color: var(--navy);
            min-height: 100vh;
        }

        .wrap {
            max-width: 920px;
            margin: 0 auto;
            padding: 1.25rem 1rem 3rem;
        }

        .hero {
            background: linear-gradient(135deg, var(--navy-deep), var(--navy));
            color: #fff;
            border-radius: 1.25rem;
            padding: 1.75rem 1.5rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(201, 168, 76, .15);
        }

        .hero-label {
            font-size: .75rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--gold-soft);
            opacity: .9;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 4vw, 2rem);
            margin: .4rem 0 .35rem;
            font-weight: 600;
        }

        .hero p {
            margin: 0;
            color: rgba(255, 255, 255, .55);
            font-size: .875rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .75rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 560px) {
            .stats { grid-template-columns: 1fr; }
        }

        .stat {
            background: #fff;
            border: 1px solid rgba(201, 168, 76, .25);
            border-radius: 1rem;
            padding: 1.1rem 1rem;
            text-align: center;
        }

        .stat .num {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            line-height: 1.2;
        }

        .stat .lbl {
            font-size: .7rem;
            color: #777;
            margin-top: .25rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .stat.hadir .num { color: #059669; }
        .stat.tidak .num { color: #e11d48; }

        .panel {
            background: #fff;
            border: 1px solid rgba(201, 168, 76, .22);
            border-radius: 1.25rem;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .panel-head {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--line);
        }

        .panel-head h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
        }

        .btn {
            border: 0;
            border-radius: 999px;
            padding: .55rem 1rem;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-ghost {
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .22);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy-deep);
        }

        .list { padding: .5rem 0; }

        .item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--line);
            display: grid;
            gap: .35rem;
        }

        .item:last-child { border-bottom: 0; }

        .item-top {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .name { font-weight: 600; font-size: .95rem; }
        .time { font-size: .7rem; color: #999; }
        .msg { color: #555; font-size: .875rem; line-height: 1.55; word-break: break-word; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .2rem .65rem;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 600;
        }

        .badge-hadir { background: #ecfdf5; color: #047857; }
        .badge-tidak { background: #fff1f2; color: #be123c; }

        .empty {
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #999;
            font-size: .9rem;
        }

        .foot {
            text-align: center;
            margin-top: .5rem;
            font-size: .75rem;
            color: #999;
        }

        .foot a { color: var(--gold); text-decoration: none; }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="hero">
            <div class="hero-label">Dashboard RSVP</div>
            <h1>{{ $title }}</h1>
            <p>Daftar ucapan &amp; konfirmasi kehadiran tamu</p>
            <div class="hero-actions">
                <a class="btn btn-ghost" href="{{ $baseInviteUrl }}" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Undangan
                </a>
                <a class="btn btn-gold" href="{{ route('rsvp.bagikan', $token) }}">
                    <i class="fa-solid fa-link"></i> Bagikan Link
                    @if ($tamuTotal > 0)
                        ({{ $tamuTotal }}/{{ $tamuMax }})
                    @endif
                </a>
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <div class="num">{{ $total }}</div>
                <div class="lbl">Total Ucapan</div>
            </div>
            <div class="stat hadir">
                <div class="num">{{ $hadir }}</div>
                <div class="lbl">Hadir</div>
            </div>
            <div class="stat tidak">
                <div class="num">{{ $tidakHadir }}</div>
                <div class="lbl">Tidak Hadir</div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Ucapan Tamu</h2>
            </div>

            <div class="list">
                @forelse ($ucapan as $item)
                    @php
                        $isHadir = ($item['kehadiran'] ?? '') === 'hadir';
                        $waktu = !empty($item['created_at'])
                            ? \Illuminate\Support\Carbon::parse($item['created_at'])
                                ->timezone('Asia/Jakarta')
                                ->format('d M Y')
                            : '';
                    @endphp
                    <div class="item">
                        <div class="item-top">
                            <div>
                                <div class="name">{{ $item['nama'] ?? 'Tamu' }}</div>
                                @if ($waktu)
                                    <div class="time">{{ $waktu }}</div>
                                @endif
                            </div>
                            <span class="badge {{ $isHadir ? 'badge-hadir' : 'badge-tidak' }}">
                                {{ $isHadir ? 'Hadir' : 'Tidak Hadir' }}
                            </span>
                        </div>
                        <div class="msg">{{ $item['ucapan'] ?? '' }}</div>
                    </div>
                @empty
                    <div class="empty">
                        <i class="fa-regular fa-comment-dots"
                            style="font-size:1.75rem;display:block;margin-bottom:.75rem;opacity:.5;"></i>
                        Belum ada ucapan / RSVP.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="foot">
            Powered by <a href="{{ url('/') }}">Rayakan Momen</a>
        </div>
    </div>
</body>

</html>
