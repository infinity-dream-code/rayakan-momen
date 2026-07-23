<style>
    /* Nav CTA: sembunyi di HP — tidak bergantung upload app.css */
    .nav-cta {
        display: none !important;
        white-space: nowrap;
        flex-shrink: 0
    }

    @media(min-width:640px) {
        .nav-cta {
            display: inline-flex !important
        }
    }

    .nav-hamburger {
        display: inline-flex;
        flex-shrink: 0
    }

    @media(min-width:1024px) {
        .nav-hamburger {
            display: none !important
        }
    }

    @media(min-width:1024px) {

        .mobile-menu,
        .mobile-overlay {
            display: none !important
        }
    }

    .nav-brand {
        display: inline-flex;
        align-items: center;
        gap: .7rem;
        text-decoration: none;
        flex-shrink: 0;
    }

    .nav-brand img {
        width: auto;
        height: 2.55rem;
        object-fit: contain;
        display: block;
        background: transparent;
    }

    .nav-brand-text {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        color: #e8d5a3;
        letter-spacing: .02em;
        line-height: 1.1;
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .nav-brand img {
            height: 2.85rem;
        }

        .nav-brand-text {
            font-size: 1.35rem;
        }
    }
</style>
<nav id="navbar" class="nav-blur fixed top-0 inset-x-0 z-50 transition-all duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 md:h-[4.5rem]">
            <a href="{{ route('landing') }}#beranda" class="nav-brand" aria-label="Rayakan Momen">
                @php $logoVer = is_file(public_path('logo-gold.png')) ? filemtime(public_path('logo-gold.png')) : time(); @endphp
                <img src="{{ asset('logo-gold.png') }}?v={{ $logoVer }}"
                    alt="" width="36" height="54" decoding="async">
                <span class="nav-brand-text">Rayakan Momen</span>
            </a>

            <ul class="hidden lg:flex items-center gap-8">
                <li><a href="{{ route('landing') }}#beranda" class="nav-link">Beranda</a></li>
                <li><a href="{{ route('landing') }}#layanan" class="nav-link">Layanan</a></li>
                <li><a href="{{ route('katalog') }}" class="nav-link">Katalog</a></li>
                <li><a href="{{ route('landing') }}#testimoni" class="nav-link">Testimoni</a></li>
                <li><a href="{{ route('landing') }}#faq" class="nav-link">FaQ</a></li>
                <li><a href="{{ route('landing') }}#kontak" class="nav-link">Kontak</a></li>
            </ul>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="https://wa.me/6285199641845?text=Halo%20Rayakan%20Momen%2C%20saya%20ingin%20pesan%20undangan%20digital"
                    target="_blank" rel="noopener noreferrer" class="btn-gold nav-cta px-5 py-2.5 rounded-full text-sm">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Pesan Sekarang</span>
                </a>
                <button id="hamburger" type="button"
                    class="nav-hamburger text-gold-light-accent text-xl w-10 h-10 flex items-center justify-center"
                    aria-label="Buka menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<div id="mobile-overlay" class="mobile-overlay fixed inset-0 z-[60] lg:hidden"></div>

<div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-[82%] max-w-xs z-[70] lg:hidden shadow-2xl">
    <div class="flex items-center justify-between px-5 h-16 border-b border-white/10">
        <span class="font-display text-lg text-gold-light-accent">Menu</span>
        <button id="mobile-close" type="button"
            class="text-gold-light-accent text-xl w-10 h-10 flex items-center justify-center" aria-label="Tutup menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <ul class="flex flex-col px-5 py-6 gap-1">
        <li><a href="{{ route('landing') }}#beranda"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Beranda</a>
        </li>
        <li><a href="{{ route('landing') }}#layanan"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Layanan</a>
        </li>
        <li><a href="{{ route('katalog') }}"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Katalog</a>
        </li>
        <li><a href="{{ route('landing') }}#testimoni"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Cerita
                Klien</a></li>
        <li><a href="{{ route('landing') }}#faq"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Pertanyaan</a>
        </li>
        <li><a href="{{ route('landing') }}#kontak"
                class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Kontak</a>
        </li>
    </ul>
    <div class="px-5 mt-4">
        <a href="https://wa.me/6285199641845?text=Halo%20Rayakan%20Momen%2C%20saya%20ingin%20pesan%20undangan%20digital"
            target="_blank" rel="noopener noreferrer" class="btn-gold mobile-nav-link w-full py-3 rounded-full text-sm">
            <i class="fa-brands fa-whatsapp"></i>
            Pesan Sekarang
        </a>
    </div>
</div>
