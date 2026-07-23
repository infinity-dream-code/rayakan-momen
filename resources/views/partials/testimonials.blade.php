<section id="testimoni" class="section-dark py-24 md:py-32 overflow-hidden">
    <img src="{{ asset('images/floral-corner.svg') }}" alt="" class="ornament-corner top-left hidden md:block"
        aria-hidden="true">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="text-center mb-14 reveal">
            <p class="section-label">Testimoni Klien</p>
            <h2 class="section-heading">
                Dari yang sudah memesan
            </h2>
            <p class="section-desc mt-4">
                Dari undangan nikah sampai pesta si kecil — mereka bilang tautannya langsung bikin tamu antusias.
            </p>
        </div>

        <div class="relative reveal">
            <div id="testimonial-track" class="testimonial-track">
                {{-- Teks testimoni di bawah masih dummy — ganti dengan data asli pasangan klien --}}
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Desainnya elegan banget, cocok sama konsep adat kita. Tamu-tamu pada bilang undangannya cantik
                        dan gampang dibuka.
                    </p>
                    <p class="testimonial-name">Rina &amp; Dimas</p>
                    <p class="testimonial-city">Pernikahan · Yogyakarta</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Ulang tahun anak kami jadi lebih seru — temannya pada antusias buka undangan, konfirmasi
                        hadirnya juga rapi.
                    </p>
                    <p class="testimonial-name">Ibu Sinta · Kirana</p>
                    <p class="testimonial-city">Ulang Tahun Anak · Semarang</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Buat kejutan pasangan, surat digitalnya bikin dia mewek. Animasi + fotonya terasa personal
                        banget.
                    </p>
                    <p class="testimonial-name">Andi</p>
                    <p class="testimonial-city">Untuk Pasangan · Jakarta</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Prosesnya cepat, revisinya ditanggapi baik. Amplop digitalnya juga praktis buat tamu kirim
                        hadiah.
                    </p>
                    <p class="testimonial-name">Alya &amp; Fajar</p>
                    <p class="testimonial-city">Pernikahan · Bandung</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Harganya sepadan. Dari konsultasi sampai jadi, komunikasinya enak. Cocok buat yang ingin
                        undangan tanpa ribet.
                    </p>
                    <p class="testimonial-name">Nadia &amp; Kevin</p>
                    <p class="testimonial-city">Pernikahan · Surabaya</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Konfirmasi hadir online-nya membantu banget menghitung jumlah tamu. Buku ucapannya juga ramai
                        diisi keluarga dari luar kota.
                    </p>
                    <p class="testimonial-name">Dewi &amp; Andra</p>
                    <p class="testimonial-city">Semarang</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        Tema dark elegant-nya sesuai banget sama suasana kami. Tautannya gampang dibagikan, orang tua
                        juga bisa buka di HP.
                    </p>
                    <p class="testimonial-name">Maya &amp; Tristan</p>
                    <p class="testimonial-city">Bali</p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-5 mt-10">
                <button id="testimonial-prev" type="button" class="carousel-btn" aria-label="Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <div class="flex gap-2">
                    <button type="button" class="carousel-dot active" aria-label="Slide 1"></button>
                    <button type="button" class="carousel-dot" aria-label="Slide 2"></button>
                    <button type="button" class="carousel-dot" aria-label="Slide 3"></button>
                    <button type="button" class="carousel-dot" aria-label="Slide 4"></button>
                    <button type="button" class="carousel-dot" aria-label="Slide 5"></button>
                    <button type="button" class="carousel-dot" aria-label="Slide 6"></button>
                </div>
                <button id="testimonial-next" type="button" class="carousel-btn" aria-label="Berikutnya">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</section>
