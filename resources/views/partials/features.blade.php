<section id="fitur" class="section-dark py-24 md:py-32 overflow-hidden">
    <div class="features-glow" aria-hidden="true"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="max-w-2xl mb-14 md:mb-20 reveal">
            <p class="section-label">Yang Kamu Dapat</p>
            <h2 class="section-heading text-left">
                Hidup, nyaman dibuka,<br>
                <span class="gold-gradient-text">enak di HP</span>
            </h2>
            <p class="section-desc text-left mx-0 mt-4">
                Tiap jenis undangan punya suasana sendiri — dari buka amplop pernikahan sampai pecah balon ulang tahun.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-5 md:gap-6 mb-16 md:mb-24">
            <a href="{{ route('katalog', ['kategori' => 'wedding']) }}" class="cat-spotlight reveal" style="--spot:#8b3a3a">
                <div class="cat-spotlight-media">
                    <img
                        src="{{ cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_480,c_fill,g_auto') }}"
                        srcset="{{ cdn_srcset('cat_wedding', [360, 480, 720], 'f_auto,q_auto:eco,c_fill,g_auto') }}"
                        sizes="(min-width: 768px) 33vw, 90vw"
                        width="720"
                        height="900"
                        alt=""
                        loading="lazy"
                        decoding="async"
                    >
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-ring"></i>
                    <h3>Pernikahan</h3>
                    <p>Akad, resepsi, cerita, amplop digital</p>
                </div>
            </a>
            <a href="{{ route('katalog', ['kategori' => 'ultah_anak']) }}" class="cat-spotlight reveal reveal-delay-1" style="--spot:#e85d75">
                <div class="cat-spotlight-media">
                    <img
                        src="{{ cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_480,c_fill,g_auto') }}"
                        srcset="{{ cdn_srcset('cat_ultah', [360, 480, 720], 'f_auto,q_auto:eco,c_fill,g_auto') }}"
                        sizes="(min-width: 768px) 33vw, 90vw"
                        width="720"
                        height="900"
                        alt=""
                        loading="lazy"
                        decoding="async"
                    >
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-cake-candles"></i>
                    <h3>Ulang Tahun Anak</h3>
                    <p>Balon, lilin, permainan, konfirmasi hadir</p>
                </div>
            </a>
            <a href="{{ route('katalog', ['kategori' => 'couple']) }}" class="cat-spotlight reveal reveal-delay-2" style="--spot:#c45c7a">
                <div class="cat-spotlight-media">
                    <img
                        src="{{ cdn_image('cat_couple', 'f_auto,q_auto:eco,w_480,c_fill,g_auto') }}"
                        srcset="{{ cdn_srcset('cat_couple', [360, 480, 720], 'f_auto,q_auto:eco,c_fill,g_auto') }}"
                        sizes="(min-width: 768px) 33vw, 90vw"
                        width="720"
                        height="900"
                        alt=""
                        loading="lazy"
                        decoding="async"
                    >
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-heart"></i>
                    <h3>Untuk Pasangan</h3>
                    <p>Surat, hitung mundur, kejutan manis</p>
                </div>
            </a>
        </div>

        <div class="feature-rows">
            @php
                $features = [
                    ['icon' => 'fa-envelope-open-text', 'title' => 'Pembuka yang beda', 'desc' => 'Buka amplop, pecah balon, atau buka surat — suasana awal menyesuaikan jenis undangannya.'],
                    ['icon' => 'fa-hourglass-half', 'title' => 'Hitung mundur', 'desc' => 'Menuju hari H: nikahan, pesta ulang tahun, atau tanggal spesial kalian.'],
                    ['icon' => 'fa-location-dot', 'title' => 'Detail & lokasi', 'desc' => 'Waktu, tempat, dan tombol langsung ke Google Maps.'],
                    ['icon' => 'fa-images', 'title' => 'Galeri kenangan', 'desc' => 'Foto & video siap dilihat nyaman di HP tamu.'],
                    ['icon' => 'fa-clipboard-check', 'title' => 'Konfirmasi hadir', 'desc' => 'Tamu isi kehadiran online — hasilnya terkumpul rapi, tanpa chat bolak-balik.'],
                    ['icon' => 'fa-gift', 'title' => 'Amplop digital', 'desc' => 'Opsional: transfer bank, QRIS, atau e-wallet.'],
                    ['icon' => 'fa-comments', 'title' => 'Ucapan & doa', 'desc' => 'Kolom ucapan untuk doa, harapan, atau pesan manis dari tamu.'],
                    ['icon' => 'fa-mobile-screen', 'title' => 'Siap dikirim WA', 'desc' => 'Satu link, dibuka nyaman di HP keluarga dan teman.'],
                ];
            @endphp

            @foreach ($features as $i => $feature)
                <div class="feature-row reveal reveal-delay-{{ ($i % 4) + 1 }}">
                    <span class="feature-row-icon"><i class="fa-solid {{ $feature['icon'] }}"></i></span>
                    <div>
                        <h3 class="feature-row-title">{{ $feature['title'] }}</h3>
                        <p class="feature-row-desc">{{ $feature['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
