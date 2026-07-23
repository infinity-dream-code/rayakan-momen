<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Bagikan Link — {{ $title }}</title>
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

        .back {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: var(--gold-soft);
            text-decoration: none;
            font-size: .8rem;
            margin-bottom: .75rem;
            position: relative;
            z-index: 1;
        }

        .back:hover { color: #fff; }

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
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .panel-head h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
        }

        .panel-sub {
            margin: .2rem 0 0;
            font-size: .75rem;
            color: #888;
        }

        .panel-body { padding: 1rem 1.25rem 1.25rem; }

        .alert {
            border-radius: .85rem;
            padding: .75rem 1rem;
            font-size: .85rem;
            margin-bottom: 1rem;
        }

        .alert-ok {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .alert-err {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .alert-link {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
            margin-top: .65rem;
        }

        .alert-link code {
            flex: 1;
            min-width: 0;
            font-size: .72rem;
            background: rgba(255, 255, 255, .7);
            border-radius: .5rem;
            padding: .45rem .6rem;
            word-break: break-all;
            color: var(--navy);
        }

        .tamu-form {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .tamu-form input {
            flex: 1;
            min-width: 180px;
            border: 1px solid #e5e0d8;
            border-radius: .85rem;
            padding: .7rem .9rem;
            font-family: inherit;
            font-size: .9rem;
            background: var(--ivory);
            color: var(--navy);
        }

        .tamu-form input:focus {
            outline: 2px solid rgba(201, 168, 76, .45);
            border-color: var(--gold);
        }

        .tamu-form input:disabled,
        .tamu-form button:disabled {
            opacity: .55;
            cursor: not-allowed;
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
            background: var(--ivory);
            color: var(--navy);
            border: 1px solid #e5e0d8;
        }

        .btn-primary { background: var(--navy); color: #fff; }
        .btn-wa { background: #25d366; color: #fff; }
        .btn-icon { padding: .5rem .7rem; }
        .btn-danger {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .list-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            padding: .65rem 1.25rem;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            background: #fcfbf8;
            font-size: .75rem;
            color: #777;
        }

        .list-meta strong { color: var(--navy); }
        .list-meta.full { background: #fff1f2; color: #be123c; }

        .list { padding: .5rem 0; }

        .item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--line);
            display: grid;
            gap: .35rem;
        }

        .item:last-child { border-bottom: 0; }

        .name { font-weight: 600; font-size: .95rem; }

        .link-url {
            font-size: .72rem;
            color: #777;
            word-break: break-all;
            line-height: 1.4;
        }

        .item-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-top: .35rem;
        }

        .empty {
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #999;
            font-size: .9rem;
        }

        .pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: .4rem;
            flex-wrap: wrap;
            padding: 1rem 1.25rem 1.15rem;
            border-top: 1px solid var(--line);
        }

        .pager a,
        .pager span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 .55rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
        }

        .pager a {
            background: var(--ivory);
            color: var(--navy);
            border: 1px solid #e5e0d8;
        }

        .pager a:hover { border-color: var(--gold); }

        .pager .is-active {
            background: var(--navy);
            color: #fff;
            border: 1px solid var(--navy);
        }

        .pager .is-disabled {
            opacity: .35;
            pointer-events: none;
            background: var(--ivory);
            color: #999;
            border: 1px solid #e5e0d8;
        }

        .toast {
            position: fixed;
            left: 50%;
            bottom: 1.25rem;
            transform: translateX(-50%) translateY(120%);
            background: var(--navy);
            color: #fff;
            padding: .65rem 1.1rem;
            border-radius: 999px;
            font-size: .8rem;
            z-index: 50;
            opacity: 0;
            transition: .25s ease;
            pointer-events: none;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
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
    @php
        $inviteTitle = $title;
        $waTextTpl = "Assalamualaikum Wr. Wb.\n\nDengan hormat, kami mengundang *{nama}* untuk membuka undangan digital *{title}*.\n\n{link}\n\nTerima kasih.";
    @endphp
    <div class="wrap">
        <div class="hero">
            <a class="back" href="{{ route('rsvp.dashboard', $token) }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard RSVP
            </a>
            <div class="hero-label">Bagikan Link</div>
            <h1>{{ $title }}</h1>
            <p>Buat link undangan personal per nama tamu</p>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div>
                    <h2>Tambah Nama Tamu</h2>
                    <p class="panel-sub">Salin link atau kirim lewat WhatsApp</p>
                </div>
                <a class="btn btn-ghost" href="{{ $baseInviteUrl }}" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Undangan
                </a>
            </div>
            <div class="panel-body">
                @if (session('tamu_error'))
                    <div class="alert alert-err">{{ session('tamu_error') }}</div>
                @endif
                @if (session('tamu_success'))
                    <div class="alert alert-ok">
                        {{ session('tamu_success') }}
                        @if (session('tamu_last_link'))
                            <div class="alert-link">
                                <code>{{ session('tamu_last_link') }}</code>
                                <button type="button" class="btn btn-ghost btn-icon" data-copy="{{ session('tamu_last_link') }}">
                                    <i class="fa-regular fa-copy"></i> Salin
                                </button>
                                <a class="btn btn-wa btn-icon"
                                    href="https://wa.me/?text={{ rawurlencode(str_replace(['{nama}', '{title}', '{link}'], [session('tamu_last_nama'), $inviteTitle, session('tamu_last_link')], $waTextTpl)) }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <form class="tamu-form" method="post" action="{{ route('rsvp.tamu.store', $token) }}">
                    @csrf
                    <input type="text" name="nama" value="{{ old('nama') }}" maxlength="80"
                        placeholder="Contoh: Budi & Keluarga" required autocomplete="name"
                        @if ($tamuFull) disabled @endif>
                    <button type="submit" class="btn btn-primary" @if ($tamuFull) disabled @endif>
                        <i class="fa-solid fa-plus"></i> Buat Link
                    </button>
                </form>
                @if ($tamuFull)
                    <p class="panel-sub" style="margin-top:.75rem;color:#be123c;">
                        Kuota 50 nama penuh. Hapus nama di daftar sebelum menambah lagi.
                    </p>
                @endif
            </div>

            <div class="list-meta {{ $tamuFull ? 'full' : '' }}">
                <span>Daftar link: <strong>{{ $tamuTotal }}</strong> / {{ $tamuMax }} nama</span>
                @if ($tamuLinks->total() > 0)
                    <span>Halaman {{ $tamuLinks->currentPage() }} dari {{ $tamuLinks->lastPage() }}</span>
                @endif
            </div>

            <div class="list">
                @forelse ($tamuLinks as $tamu)
                    @php
                        $namaTamu = (string) ($tamu['nama'] ?? '');
                        $linkTamu = $baseInviteUrl . '?to=' . rawurlencode($namaTamu);
                        $waText = str_replace(
                            ['{nama}', '{title}', '{link}'],
                            [$namaTamu, $inviteTitle, $linkTamu],
                            $waTextTpl
                        );
                    @endphp
                    <div class="item">
                        <div>
                            <div class="name">{{ $namaTamu }}</div>
                            <div class="link-url">{{ $linkTamu }}</div>
                        </div>
                        <div class="item-actions">
                            <button type="button" class="btn btn-ghost btn-icon" data-copy="{{ $linkTamu }}">
                                <i class="fa-regular fa-copy"></i> Salin
                            </button>
                            <a class="btn btn-wa btn-icon"
                                href="https://wa.me/?text={{ rawurlencode($waText) }}"
                                target="_blank" rel="noopener">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                            </a>
                            <form method="post"
                                action="{{ route('rsvp.tamu.destroy', [$token, $tamu['id']]) }}?page={{ $tamuLinks->currentPage() }}"
                                onsubmit="return confirm('Hapus {{ addslashes($namaTamu) }} dari daftar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon" title="Hapus">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty">
                        <i class="fa-solid fa-user-plus"
                            style="font-size:1.5rem;display:block;margin-bottom:.75rem;opacity:.45;"></i>
                        Belum ada nama. Tambahkan tamu di atas untuk membuat link personal.
                    </div>
                @endforelse
            </div>

            @if ($tamuLinks->lastPage() > 1)
                <nav class="pager" aria-label="Navigasi daftar link">
                    @if ($tamuLinks->onFirstPage())
                        <span class="is-disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $tamuLinks->previousPageUrl() }}" aria-label="Sebelumnya">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach ($tamuLinks->getUrlRange(1, $tamuLinks->lastPage()) as $page => $url)
                        @if ($page == $tamuLinks->currentPage())
                            <span class="is-active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($tamuLinks->hasMorePages())
                        <a href="{{ $tamuLinks->nextPageUrl() }}" aria-label="Berikutnya">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="is-disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </nav>
            @endif
        </div>

        <div class="foot">
            Powered by <a href="{{ url('/') }}">Rayakan Momen</a>
        </div>
    </div>

    <div class="toast" id="toast" role="status"></div>
    <script>
        (function() {
            var toast = document.getElementById('toast');
            var timer;
            function showToast(msg) {
                toast.textContent = msg;
                toast.classList.add('show');
                clearTimeout(timer);
                timer = setTimeout(function() { toast.classList.remove('show'); }, 1800);
            }
            document.querySelectorAll('[data-copy]').forEach(function(btn) {
                btn.addEventListener('click', async function() {
                    var text = btn.getAttribute('data-copy') || '';
                    try {
                        await navigator.clipboard.writeText(text);
                        showToast('Link disalin');
                    } catch (e) {
                        var ta = document.createElement('textarea');
                        ta.value = text;
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        ta.remove();
                        showToast('Link disalin');
                    }
                });
            });
        })();
    </script>
</body>

</html>
