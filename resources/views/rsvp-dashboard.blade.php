<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard RSVP — {{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        .font-display { font-family: 'Playfair Display', serif; }
        .wrap { max-width: 920px; margin: 0 auto; padding: 1.25rem 1rem 3rem; }
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
            right: -40px; top: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(201,168,76,.15);
        }
        .hero-label { font-size: .75rem; letter-spacing: .12em; text-transform: uppercase; color: var(--gold-soft); opacity: .9; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(1.5rem, 4vw, 2rem); margin: .4rem 0 .35rem; font-weight: 600; }
        .hero p { margin: 0; color: rgba(255,255,255,.55); font-size: .875rem; }
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
            border: 1px solid rgba(201,168,76,.25);
            border-radius: 1rem;
            padding: 1.1rem 1rem;
            text-align: center;
        }
        .stat .num { font-family: 'Playfair Display', serif; font-size: 1.75rem; line-height: 1.2; }
        .stat .lbl { font-size: .7rem; color: #777; margin-top: .25rem; text-transform: uppercase; letter-spacing: .06em; }
        .stat.hadir .num { color: #059669; }
        .stat.tidak .num { color: #e11d48; }
        .panel {
            background: #fff;
            border: 1px solid rgba(201,168,76,.22);
            border-radius: 1.25rem;
            overflow: hidden;
        }
        .panel-head {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .panel-head h2 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.15rem; }
        .share-row {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .share-input {
            flex: 1;
            min-width: 180px;
            border: 1px solid #e5e0d8;
            border-radius: .65rem;
            padding: .55rem .75rem;
            font-size: .75rem;
            background: var(--ivory);
            color: #555;
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
        }
        .btn-gold { background: linear-gradient(135deg, #dfc06a, #c9a84c); color: #12161f; }
        .btn-ghost { background: var(--ivory); color: var(--navy); border: 1px solid #e5e0d8; }
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
            padding: 3rem 1.5rem;
            text-align: center;
            color: #999;
            font-size: .9rem;
        }
        .foot {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .75rem;
            color: #999;
        }
        .foot a { color: var(--gold); text-decoration: none; }
        .toast {
            position: fixed;
            bottom: 1.25rem;
            left: 50%;
            transform: translateX(-50%) translateY(120%);
            background: var(--navy);
            color: #fff;
            padding: .65rem 1.1rem;
            border-radius: 999px;
            font-size: .8rem;
            transition: transform .25s ease;
            z-index: 50;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="hero">
            <div class="hero-label">Dashboard RSVP</div>
            <h1>{{ $title }}</h1>
            <p>Daftar ucapan &amp; konfirmasi kehadiran tamu</p>
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
                <div class="share-row" style="flex:1; justify-content:flex-end; max-width:100%;">
                    <input class="share-input" id="shareUrl" type="text" readonly value="{{ $shareUrl }}">
                    <button type="button" class="btn btn-gold" id="btnCopy"><i class="fa-solid fa-link"></i> Salin Link</button>
                    <a class="btn btn-ghost" href="{{ url('/'.$undangan['slug']) }}" target="_blank" rel="noopener">Lihat Undangan</a>
                </div>
            </div>

            <div class="list">
                @forelse ($ucapan as $item)
                    @php
                        $isHadir = ($item['kehadiran'] ?? '') === 'hadir';
                        $waktu = !empty($item['created_at'])
                            ? \Illuminate\Support\Carbon::parse($item['created_at'])->timezone('Asia/Jakarta')->format('d M Y · H:i')
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
                        <i class="fa-regular fa-comment-dots" style="font-size:1.75rem;display:block;margin-bottom:.75rem;opacity:.5;"></i>
                        Belum ada ucapan / RSVP.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="foot">
            Powered by <a href="{{ url('/') }}">Rayakan Momen</a>
        </div>
    </div>

    <div class="toast" id="toast">Link disalin</div>
    <script>
        (function () {
            var btn = document.getElementById('btnCopy');
            var input = document.getElementById('shareUrl');
            var toast = document.getElementById('toast');
            if (!btn || !input) return;
            btn.addEventListener('click', function () {
                input.select();
                input.setSelectionRange(0, 99999);
                var ok = false;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(input.value).then(function () { show(); }).catch(fallback);
                } else {
                    fallback();
                }
                function fallback() {
                    try { ok = document.execCommand('copy'); } catch (e) {}
                    if (ok) show();
                }
                function show() {
                    toast.classList.add('show');
                    setTimeout(function () { toast.classList.remove('show'); }, 1800);
                }
            });
        })();
    </script>
</body>
</html>
