@php
    $tema = $undangan['tema'] ?? 'elegan';
    $isDark = $tema === 'langit_malam';
    $isClassic = $tema === 'classic';
    $cover = !empty($undangan['cover_image'])
        ? asset($undangan['cover_image'])
        : ($isDark
            ? 'https://images.unsplash.com/photo-1419242902214-272b3f66ee7a?auto=format&fit=crop&w=900&q=80'
            : ($isClassic
                ? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=900&q=80'
                : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80'));

    $ytId = null;
    if (
        !empty($undangan['youtube_url']) &&
        preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $undangan['youtube_url'], $m)
    ) {
        $ytId = $m[1];
    }

    $tanggalUtama = $undangan['tanggal_resepsi'] ?? ($undangan['tanggal_akad'] ?? null);
    $tanggalFormatted = $tanggalUtama
        ? \Illuminate\Support\Carbon::parse($tanggalUtama)->locale('id')->translatedFormat('l · d F Y')
        : null;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $undangan['nama_wanita'] }} & {{ $undangan['nama_pria'] }} — Undangan Pernikahan</title>
    <meta name="description"
        content="Undangan pernikahan digital {{ $undangan['nama_wanita'] }} & {{ $undangan['nama_pria'] }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg: {{ $isDark ? '#0b1320' : ($isClassic ? '#234338' : '#faf7f2') }};
            --text: {{ $isDark || $isClassic ? '#f5f0e8' : '#2c1810' }};
            --muted: {{ $isDark || $isClassic ? 'rgba(245,240,232,0.65)' : 'rgba(44,24,16,0.6)' }};
            --accent: {{ $isDark ? '#d4b56a' : ($isClassic ? '#e8c9b8' : '#8b3a3a') }};
            --card: {{ $isDark ? 'rgba(255,255,255,0.06)' : ($isClassic ? 'rgba(255,255,255,0.08)' : '#ffffff') }};
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .font-display {
            font-family: 'Playfair Display', serif;
        }

        .font-script {
            font-family: 'Great Vibes', cursive;
        }

        .invite-wrap {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
        }

        .cover-hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 1.5rem 6rem;
            position: relative;
            overflow: hidden;
        }

        .cover-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: {{ $isDark || $isClassic
                ? 'linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.55) 100%)'
                : 'linear-gradient(180deg, rgba(250,247,242,0.3) 0%, rgba(250,247,242,0.92) 100%)' }};
            z-index: 1;
        }

        .cover-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: {{ $isDark ? 'brightness(0.55)' : 'none' }};
        }

        .cover-content {
            position: relative;
            z-index: 2;
        }

        .section-block {
            padding: 3rem 1.5rem;
        }

        .divider {
            width: 64px;
            height: 1px;
            margin: 1rem auto;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }

        .card-soft {
            background: var(--card);
            border: 1px solid {{ $isDark || $isClassic ? 'rgba(255,255,255,0.1)' : 'rgba(139,58,58,0.12)' }};
            border-radius: 1.25rem;
            padding: 1.5rem;
        }

        .nav-bottom {
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            display: flex;
            gap: 0.25rem;
            padding: 0.5rem 0.75rem;
            border-radius: 999px;
            background: {{ $isDark ? 'rgba(15,20,30,0.9)' : 'rgba(255,255,255,0.92)' }};
            border: 1px solid {{ $isDark ? 'rgba(212,181,106,0.25)' : 'rgba(0,0,0,0.06)' }};
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        .nav-bottom a {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 0.875rem;
            transition: .2s;
        }

        .nav-bottom a:hover,
        .nav-bottom a.active {
            background: var(--accent);
            color: {{ $isDark || $isClassic ? '#1a1510' : '#fff' }};
        }

        .form-field {
            width: 100%;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid {{ $isDark || $isClassic ? 'rgba(255,255,255,0.15)' : '#e8dfd4' }};
            background: {{ $isDark || $isClassic ? 'rgba(255,255,255,0.06)' : '#fff' }};
            color: var(--text);
            font-size: 0.875rem;
            outline: none;
        }

        .btn-accent {
            background: var(--accent);
            color: {{ $isDark || $isClassic ? '#1a1510' : '#fff' }};
            font-weight: 600;
            border-radius: 999px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .music-btn {
            position: fixed;
            bottom: 5rem;
            right: 1rem;
            z-index: 40;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            background: var(--accent);
            color: {{ $isDark || $isClassic ? '#1a1510' : '#fff' }};
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .hidden-audio {
            display: none;
        }

        .timeline-item {
            position: relative;
            padding-left: 1.5rem;
            border-left: 1px solid var(--accent);
            margin-left: 0.5rem;
            padding-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 0.35rem;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--accent);
        }
    </style>
</head>

<body>
    <div class="invite-wrap">
        <section id="cover" class="cover-hero">
            <div class="cover-bg" style="background-image:url('{{ $cover }}')"></div>
            <div class="cover-content">
                <p class="text-xs tracking-[0.25em] uppercase mb-4" style="color:var(--accent)">The Wedding Of</p>
                <h1 class="font-display text-4xl sm:text-5xl leading-tight mb-2">
                    {{ $undangan['nama_wanita'] }}
                    <span class="font-script text-3xl block my-1" style="color:var(--accent)">&</span>
                    {{ $undangan['nama_pria'] }}
                </h1>
                @if ($tanggalFormatted)
                    <p class="text-sm tracking-wide mt-4" style="color:var(--muted)">
                        {{ strtoupper($tanggalFormatted) }}</p>
                @endif
                <button type="button" id="btn-buka" class="btn-accent mt-8 text-sm">
                    <i class="fa-solid fa-envelope-open"></i> Buka Undangan
                </button>
            </div>
        </section>

        <div id="isi-undangan" class="hidden">
            <section id="mempelai" class="section-block text-center">
                <p class="text-xs tracking-[0.2em] uppercase mb-2" style="color:var(--accent)">Kedua Mempelai</p>
                <div class="divider"></div>
                @if (!empty($undangan['kutipan']))
                    <p class="font-display italic text-sm leading-relaxed mb-2 max-w-sm mx-auto"
                        style="color:var(--muted)">
                        "{{ $undangan['kutipan'] }}"
                    </p>
                    @if (!empty($undangan['kutipan_sumber']))
                        <p class="text-xs mb-8" style="color:var(--accent)">{{ $undangan['kutipan_sumber'] }}</p>
                    @endif
                @endif

                <div class="space-y-8 mt-6">
                    <div>
                        <h2 class="font-display text-3xl mb-1" style="color:var(--accent)">
                            {{ $undangan['nama_wanita'] }}</h2>
                        @if (!empty($undangan['nama_lengkap_wanita']))
                            <p class="text-sm font-medium">{{ $undangan['nama_lengkap_wanita'] }}</p>
                        @endif
                        @if (!empty($undangan['ortu_wanita']))
                            <p class="text-xs mt-2" style="color:var(--muted)">Putri
                                dari<br>{{ $undangan['ortu_wanita'] }}</p>
                        @endif
                    </div>
                    <p class="font-script text-3xl" style="color:var(--accent)">&</p>
                    <div>
                        <h2 class="font-display text-3xl mb-1" style="color:var(--accent)">{{ $undangan['nama_pria'] }}
                        </h2>
                        @if (!empty($undangan['nama_lengkap_pria']))
                            <p class="text-sm font-medium">{{ $undangan['nama_lengkap_pria'] }}</p>
                        @endif
                        @if (!empty($undangan['ortu_pria']))
                            <p class="text-xs mt-2" style="color:var(--muted)">Putra
                                dari<br>{{ $undangan['ortu_pria'] }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section id="acara" class="section-block">
                <p class="text-xs tracking-[0.2em] uppercase text-center mb-2" style="color:var(--accent)">Waktu &
                    Tempat</p>
                <h2 class="font-display text-2xl text-center mb-6">Detail Acara</h2>
                <div class="space-y-4">
                    @if ($undangan['tempat_akad'] || $undangan['tanggal_akad'])
                        <div class="card-soft text-center">
                            <p class="text-xs tracking-widest uppercase mb-2" style="color:var(--accent)">Akad Nikah</p>
                            @if ($undangan['tanggal_akad'])
                                <p class="font-display text-lg">
                                    {{ \Illuminate\Support\Carbon::parse($undangan['tanggal_akad'])->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                            @endif
                            @if ($undangan['waktu_akad'])
                                <p class="text-sm mt-1" style="color:var(--muted)">{{ $undangan['waktu_akad'] }}</p>
                            @endif
                            @if ($undangan['tempat_akad'])
                                <p class="font-medium text-sm mt-3">{{ $undangan['tempat_akad'] }}</p>
                            @endif
                            @if ($undangan['alamat_akad'])
                                <p class="text-xs mt-1" style="color:var(--muted)">{{ $undangan['alamat_akad'] }}</p>
                            @endif
                        </div>
                    @endif
                    @if ($undangan['tempat_resepsi'] || $undangan['tanggal_resepsi'])
                        <div class="card-soft text-center">
                            <p class="text-xs tracking-widest uppercase mb-2" style="color:var(--accent)">Resepsi</p>
                            @if ($undangan['tanggal_resepsi'])
                                <p class="font-display text-lg">
                                    {{ \Illuminate\Support\Carbon::parse($undangan['tanggal_resepsi'])->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                            @endif
                            @if ($undangan['waktu_resepsi'])
                                <p class="text-sm mt-1" style="color:var(--muted)">{{ $undangan['waktu_resepsi'] }}</p>
                            @endif
                            @if ($undangan['tempat_resepsi'])
                                <p class="font-medium text-sm mt-3">{{ $undangan['tempat_resepsi'] }}</p>
                            @endif
                            @if ($undangan['alamat_resepsi'])
                                <p class="text-xs mt-1" style="color:var(--muted)">{{ $undangan['alamat_resepsi'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                    @if (!empty($undangan['maps_url']))
                        <div class="text-center pt-2">
                            <a href="{{ $undangan['maps_url'] }}" target="_blank" rel="noopener"
                                class="btn-accent text-sm">
                                <i class="fa-solid fa-location-dot"></i> Lihat Lokasi
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            @if (!empty($undangan['cerita']))
                <section id="cerita" class="section-block">
                    <p class="text-xs tracking-[0.2em] uppercase text-center mb-2" style="color:var(--accent)">Our Story
                    </p>
                    <h2 class="font-display text-2xl text-center mb-8">Perjalanan Kami</h2>
                    <div class="max-w-sm mx-auto">
                        @foreach ($undangan['cerita'] as $c)
                            <div class="timeline-item">
                                <p class="text-xs font-semibold" style="color:var(--accent)">{{ $c['tahun'] ?? '' }}
                                </p>
                                <h3 class="font-display text-lg mt-0.5">{{ $c['judul'] ?? '' }}</h3>
                                <p class="text-sm mt-1 leading-relaxed" style="color:var(--muted)">
                                    {{ $c['deskripsi'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($undangan['galeri']))
                <section id="galeri" class="section-block">
                    <p class="text-xs tracking-[0.2em] uppercase text-center mb-2" style="color:var(--accent)">Galeri
                    </p>
                    <h2 class="font-display text-2xl text-center mb-6">Momen Kami</h2>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($undangan['galeri'] as $g)
                            <img src="{{ asset($g) }}" alt=""
                                class="w-full aspect-square object-cover rounded-xl" loading="lazy">
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($undangan['rekening']))
                <section id="gift" class="section-block">
                    <p class="text-xs tracking-[0.2em] uppercase text-center mb-2" style="color:var(--accent)">Wedding
                        Gift</p>
                    <h2 class="font-display text-2xl text-center mb-3">Tanda Kasih</h2>
                    <p class="text-sm text-center mb-6 max-w-xs mx-auto" style="color:var(--muted)">Doa restu Anda
                        sangat berarti. Jika berkenan, dapat melalui rekening berikut.</p>
                    <div class="space-y-3">
                        @foreach ($undangan['rekening'] as $r)
                            <div class="card-soft text-center">
                                <p class="font-semibold text-sm">{{ $r['bank'] ?? '' }}</p>
                                <p class="font-display text-xl mt-1 tracking-wide">{{ $r['nomor'] ?? '' }}</p>
                                <p class="text-xs mt-1" style="color:var(--muted)">a.n. {{ $r['atas_nama'] ?? '' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="ucapan" class="section-block pb-28">
                <p class="text-xs tracking-[0.2em] uppercase text-center mb-2" style="color:var(--accent)">RSVP</p>
                <h2 class="font-display text-2xl text-center mb-6">Ucapan & Doa</h2>

                @if (session('success'))
                    <div class="mb-4 text-sm text-center px-4 py-3 rounded-xl"
                        style="background:rgba(16,185,129,0.15);color:#10b981;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('undangan.ucapan', $undangan['slug']) }}"
                    class="card-soft space-y-3 mb-8">
                    @csrf
                    <div>
                        <input type="text" name="nama" placeholder="Nama kamu" class="form-field" required
                            value="{{ old('nama') }}">
                    </div>
                    <div>
                        <select name="kehadiran" class="form-field" required>
                            <option value="">Konfirmasi kehadiran</option>
                            <option value="hadir">Hadir</option>
                            <option value="tidak_hadir">Tidak Hadir</option>
                        </select>
                    </div>
                    <div>
                        <textarea name="ucapan" rows="3" placeholder="Tulis ucapan & doa..." class="form-field" required>{{ old('ucapan') }}</textarea>
                    </div>
                    <button type="submit" class="btn-accent w-full text-sm">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Ucapan
                    </button>
                </form>

                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse ($undangan['ucapan_tersimpan'] ?? [] as $u)
                        <div class="card-soft">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="font-medium text-sm">{{ $u['nama'] }}</p>
                                <span class="text-[10px] uppercase tracking-wider" style="color:var(--accent)">
                                    {{ ($u['kehadiran'] ?? '') === 'hadir' ? 'Hadir' : 'Tidak Hadir' }}
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed" style="color:var(--muted)">{{ $u['ucapan'] }}</p>
                        </div>
                    @empty
                        <p class="text-center text-sm" style="color:var(--muted)">Jadilah yang pertama memberi ucapan.
                        </p>
                    @endforelse
                </div>
            </section>

            <footer class="text-center py-8 text-xs" style="color:var(--muted)">
                Made with ♥ by <a href="{{ url('/') }}" class="underline" style="color:var(--accent)">Rayakan
                    Momen</a>
            </footer>
        </div>
    </div>

    <nav class="nav-bottom" id="nav-bottom" style="display:none;">
        <a href="#cover" title="Beranda"><i class="fa-solid fa-house"></i></a>
        <a href="#mempelai" title="Mempelai"><i class="fa-solid fa-user-group"></i></a>
        <a href="#acara" title="Acara"><i class="fa-solid fa-calendar"></i></a>
        <a href="#gift" title="Gift"><i class="fa-solid fa-gift"></i></a>
        <a href="#ucapan" title="Ucapan"><i class="fa-solid fa-comment"></i></a>
    </nav>

    @if ($ytId)
        <button type="button" id="music-btn" class="music-btn" style="display:none;" aria-label="Toggle musik">
            <i class="fa-solid fa-music"></i>
        </button>
        <iframe id="yt-player" class="hidden-audio"
            src="https://www.youtube.com/embed/{{ $ytId }}?enablejsapi=1&loop=1&playlist={{ $ytId }}"
            allow="autoplay"></iframe>
    @endif

    <script>
        document.getElementById('btn-buka').addEventListener('click', function() {
            document.getElementById('isi-undangan').classList.remove('hidden');
            document.getElementById('nav-bottom').style.display = 'flex';
            var musicBtn = document.getElementById('music-btn');
            if (musicBtn) musicBtn.style.display = 'flex';
            document.getElementById('mempelai').scrollIntoView({
                behavior: 'smooth'
            });
        });

        @if ($ytId)
            var playing = false;
            var iframe = document.getElementById('yt-player');
            document.getElementById('music-btn').addEventListener('click', function() {
                if (!playing) {
                    iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                    playing = true;
                    this.innerHTML = '<i class="fa-solid fa-pause"></i>';
                } else {
                    iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                    playing = false;
                    this.innerHTML = '<i class="fa-solid fa-music"></i>';
                }
            });
        @endif
    </script>
</body>

</html>
