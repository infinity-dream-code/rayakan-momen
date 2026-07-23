<section id="fitur" class="section-dark py-24 md:py-32 overflow-hidden">
    <div class="features-glow" aria-hidden="true"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="w-full mb-14 md:mb-20 reveal border border-red-500">
            <p class="section-label-feature text-center border border-red-500">Yang Kamu Dapatkan</p>
            <h2 class="section-heading text-center">
                Hidup, nyaman dibuka,<br>
                <span class="gold-gradient-text">enak di HP</span>
            </h2>
            <p class="section-desc-feature text-center mx-0 mt-4">
                Tiap jenis undangan punya suasana sendiri — dari buka amplop pernikahan sampai pecah balon ulang tahun.
            </p>
        </div>

        <div class="feature-rows">
            @php
                $features = [
                    [
                        'icon' => 'fa-envelope-open-text',
                        'title' => 'Pembuka yang beda',
                        'desc' =>
                            'Buka amplop, pecah balon, atau buka surat — suasana awal menyesuaikan jenis undangannya.',
                    ],
                    [
                        'icon' => 'fa-hourglass-half',
                        'title' => 'Hitung mundur',
                        'desc' => 'Menuju hari H: nikahan, pesta ulang tahun, atau tanggal spesial kalian.',
                    ],
                    [
                        'icon' => 'fa-location-dot',
                        'title' => 'Detail & lokasi',
                        'desc' => 'Waktu, tempat, dan tombol langsung ke Google Maps.',
                    ],
                    [
                        'icon' => 'fa-images',
                        'title' => 'Galeri kenangan',
                        'desc' => 'Foto & video siap dilihat nyaman di HP tamu.',
                    ],
                    [
                        'icon' => 'fa-clipboard-check',
                        'title' => 'Konfirmasi hadir',
                        'desc' => 'Tamu isi kehadiran online — hasilnya terkumpul rapi, tanpa chat bolak-balik.',
                    ],
                    [
                        'icon' => 'fa-gift',
                        'title' => 'Amplop digital',
                        'desc' => 'Opsional: transfer bank, QRIS, atau e-wallet.',
                    ],
                    [
                        'icon' => 'fa-comments',
                        'title' => 'Ucapan & doa',
                        'desc' => 'Kolom ucapan untuk doa, harapan, atau pesan manis dari tamu.',
                    ],
                    [
                        'icon' => 'fa-mobile-screen',
                        'title' => 'Siap dikirim WA',
                        'desc' => 'Satu link, dibuka nyaman di HP keluarga dan teman.',
                    ],
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
