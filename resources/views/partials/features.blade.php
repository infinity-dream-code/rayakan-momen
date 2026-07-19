<section id="fitur" class="section-dark py-24 md:py-32 overflow-hidden">
    <div class="features-glow" aria-hidden="true"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="max-w-2xl mb-14 md:mb-20 reveal">
            <p class="section-label">Yang Kamu Dapat</p>
            <h2 class="section-heading text-left">
                Interaktif, siap share,<br>
                <span class="gold-gradient-text">cocok di HP</span>
            </h2>
            <p class="section-desc text-left mx-0 mt-4">
                Tiap kategori punya ritme sendiri — dari buka amplop wedding sampai pecah balon ultah.
            </p>
        </div>

        {{-- 3 kategori sebagai visual utama --}}
        <div class="grid md:grid-cols-3 gap-5 md:gap-6 mb-16 md:mb-24">
            <a href="#template" class="cat-spotlight reveal" data-jump-cat="wedding" style="--spot:#8b3a3a">
                <div class="cat-spotlight-media">
                    <img src="{{ asset('images/landing/cat-wedding.jpg') }}" alt="" loading="lazy">
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-ring"></i>
                    <h3>Wedding</h3>
                    <p>Akad, resepsi, story, amplop digital</p>
                </div>
            </a>
            <a href="#template" class="cat-spotlight reveal reveal-delay-1" data-jump-cat="ultah_anak" style="--spot:#e85d75">
                <div class="cat-spotlight-media">
                    <img src="{{ asset('images/landing/cat-ultah.jpg') }}" alt="" loading="lazy">
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-cake-candles"></i>
                    <h3>Ultah Anak</h3>
                    <p>Balon, lilin, game, RSVP ceria</p>
                </div>
            </a>
            <a href="#template" class="cat-spotlight reveal reveal-delay-2" data-jump-cat="couple" style="--spot:#c45c7a">
                <div class="cat-spotlight-media">
                    <img src="{{ asset('images/landing/cat-couple.jpg') }}" alt="" loading="lazy">
                </div>
                <div class="cat-spotlight-body">
                    <i class="fa-solid fa-heart"></i>
                    <h3>Couple</h3>
                    <p>Surat, countdown, kejutan manis</p>
                </div>
            </a>
        </div>

        {{-- Fitur tanpa card dashboard --}}
        <div class="feature-rows">
            @php
                $features = [
                    ['icon' => 'fa-envelope-open-text', 'title' => 'Cover interaktif', 'desc' => 'Buka amplop, pecah balon, atau buka surat — pembuka beda tiap kategori.'],
                    ['icon' => 'fa-hourglass-half', 'title' => 'Countdown real-time', 'desc' => 'Hitung mundur ke hari H: nikahan, pesta, atau tanggal spesial.'],
                    ['icon' => 'fa-location-dot', 'title' => 'Detail & peta', 'desc' => 'Waktu, tempat, dan tombol lokasi langsung ke Google Maps.'],
                    ['icon' => 'fa-images', 'title' => 'Galeri momen', 'desc' => 'Foto & video siap dipamerkan di HP tamu.'],
                    ['icon' => 'fa-clipboard-check', 'title' => 'RSVP online', 'desc' => 'Konfirmasi hadir terkumpul rapi tanpa chat bolak-balik.'],
                    ['icon' => 'fa-gift', 'title' => 'Amplop digital', 'desc' => 'Opsional: transfer bank, QRIS, atau e-wallet.'],
                    ['icon' => 'fa-comments', 'title' => 'Ucapan & doa', 'desc' => 'Guestbook digital untuk doa, harapan, atau pesan manis.'],
                    ['icon' => 'fa-mobile-screen', 'title' => 'Siap share WA', 'desc' => 'Satu link, dibuka nyaman di HP keluarga & teman.'],
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
