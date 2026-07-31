<footer id="kontak" class="footer-dark pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div class="lg:col-span-1">
                @php $logoVer = is_file(public_path('logo-gold.png')) ? filemtime(public_path('logo-gold.png')) : time(); @endphp
                <a href="{{ route('landing') }}#beranda" class="inline-flex items-center gap-3 mb-4 no-underline">
                    <img src="{{ asset('logo-gold.png') }}?v={{ $logoVer }}"
                        alt="" width="40" height="60" class="object-contain" decoding="async"
                        style="background:transparent;height:3rem;width:auto">
                    <span class="font-display text-xl md:text-2xl text-gold-light-accent tracking-wide">Rayakan Momen</span>
                </a>
                <p class="text-sm leading-relaxed text-on-dark-soft">
                    Jasa undangan digital untuk pernikahan, ulang tahun anak, dan kejutan pasangan. Pilih desain, lihat
                    harga, siap dibagikan lewat WhatsApp.
                </p>
            </div>

            <div>
                <h4 class="text-on-dark font-medium text-sm mb-4 tracking-wide">Menu Cepat</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('landing') }}#beranda" class="footer-link">Beranda</a></li>
                    <li><a href="{{ route('landing') }}#layanan" class="footer-link">Layanan</a></li>
                    <li><a href="{{ route('katalog') }}" class="footer-link">Katalog &amp; Harga</a></li>
                    <li><a href="{{ route('landing') }}#faq" class="footer-link">Pertanyaan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-on-dark font-medium text-sm mb-4 tracking-wide">Kontak</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="https://wa.me/6285199641845" target="_blank" rel="noopener noreferrer"
                            class="footer-link inline-flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-gold-accent"></i>
                            0851-9964-1845
                        </a>
                    </li>
                    <li>
                        <a href="mailto:admin@rayakanmomen.com" class="footer-link inline-flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-gold-accent"></i>
                            admin@rayakanmomen.com
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-on-dark font-medium text-sm mb-4 tracking-wide">Sosial Media</h4>
                <div class="flex gap-3">
                    <a href="https://instagram.com/rayakanmomen_" target="_blank" rel="noopener noreferrer"
                        class="social-btn" aria-label="Instagram Rayakan Momen">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@rayakanmomen.com" target="_blank" rel="noopener noreferrer"
                        class="social-btn" aria-label="TikTok Rayakan Momen">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="https://wa.me/6285199641845" target="_blank" rel="noopener noreferrer" class="social-btn"
                        aria-label="WhatsApp Rayakan Momen">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 pt-6 text-center text-xs text-on-dark-soft">
            &copy; 2026 Rayakan Momen. Semua hak dilindungi.
        </div>
    </div>
</footer>
