<section id="beranda" class="hero-stage">
    <div class="hero-media" aria-hidden="true">
        <div class="hero-slides" id="heroSlides">
            <div class="hero-slide is-active" data-label="Pernikahan">
                <img src="{{ cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_960,c_limit') }}"
                    srcset="{{ cdn_srcset('hero_wedding', [640, 960, 1280, 1920]) }}" sizes="100vw" width="1920"
                    height="1080" alt="" class="hero-media-img" fetchpriority="high" decoding="async">
            </div>
            <div class="hero-slide" data-label="Untuk Pasangan">
                <img src="{{ cdn_image('hero_couple', 'f_auto,q_auto:eco,w_960,c_limit') }}"
                    srcset="{{ cdn_srcset('hero_couple', [640, 960, 1280, 1920]) }}" sizes="100vw" width="1920"
                    height="1080" alt="" class="hero-media-img" loading="lazy" decoding="async">
            </div>
            <div class="hero-slide" data-label="Ulang Tahun Anak">
                <img src="{{ cdn_image('hero_ultah', 'f_auto,q_auto:eco,w_960,c_limit') }}"
                    srcset="{{ cdn_srcset('hero_ultah', [640, 960, 1280, 1920]) }}" sizes="100vw" width="1920"
                    height="1080" alt="" class="hero-media-img" loading="lazy" decoding="async">
            </div>
        </div>
        <div class="hero-media-veil"></div>
        <div class="hero-media-grain"></div>
    </div>

    <div class="hero-content">
        <p class="hero-brand hero-in">Rayakan Momen</p>

        <h1 class="hero-title hero-in hero-in-2">
            Undangan digital<br>
            <em>yang hidup</em>
        </h1>

        <p class="hero-lead hero-in hero-in-3">
            Nikahan, ultah anak, atau kejutan pasangan &mdash; siap 1 hari, dengan dashboard RSVP &amp; link personal.
        </p>

        <div class="hero-actions hero-in hero-in-4">
            <a href="{{ route('katalog') }}" class="btn-gold px-8 py-3.5 rounded-full text-sm">
                Lihat Katalog Undangan
            </a>
            <a href="https://wa.me/6285199641845?text=Halo%20Rayakan%20Momen%2C%20saya%20mau%20tanya%20soal%20undangan%20digital"
                target="_blank" rel="noopener noreferrer" class="btn-outline-gold px-8 py-3.5 rounded-full text-sm">
                <i class="fa-brands fa-whatsapp"></i>
                Tanya via WhatsApp
            </a>
        </div>

        <div class="hero-slide-meta hero-in hero-in-4" aria-live="polite">
            <span class="hero-slide-label" id="heroSlideLabel">Pernikahan</span>
            <div class="hero-slide-dots" id="heroSlideDots" role="tablist" aria-label="Slide hero">
                <button type="button" class="is-active" data-slide="0" aria-label="Pernikahan"></button>
                <button type="button" data-slide="1" aria-label="Untuk Pasangan"></button>
                <button type="button" data-slide="2" aria-label="Ulang Tahun Anak"></button>
            </div>
        </div>
    </div>

    <a href="#layanan" class="hero-scroll" aria-label="Scroll ke bawah">
        <span></span>
    </a>
</section>
