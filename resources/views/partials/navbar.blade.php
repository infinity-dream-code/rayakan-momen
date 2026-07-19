<nav id="navbar" class="nav-blur fixed top-0 inset-x-0 z-50 transition-all duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 md:h-[4.5rem]">
            <a href="#beranda" class="font-display text-xl md:text-2xl text-gold-light-accent tracking-wide">
                Web Untal
            </a>

            <ul class="hidden lg:flex items-center gap-8">
                <li><a href="#beranda" class="nav-link">Beranda</a></li>
                <li><a href="#layanan" class="nav-link">Layanan</a></li>
                <li><a href="#template" class="nav-link">Template</a></li>
                <li><a href="#testimoni" class="nav-link">Testimoni</a></li>
                <li><a href="#faq" class="nav-link">FAQ</a></li>
                <li><a href="#kontak" class="nav-link">Kontak</a></li>
            </ul>

            <div class="flex items-center gap-3">
                <a href="https://wa.me/6285777743388?text=Halo%20Web%20Untal%2C%20saya%20ingin%20pesan%20undangan%20digital"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-gold hidden sm:inline-flex px-5 py-2.5 rounded-full text-sm">
                    <i class="fa-brands fa-whatsapp"></i>
                    Pesan Sekarang
                </a>
                <button id="hamburger" type="button" class="lg:hidden text-gold-light-accent text-xl w-10 h-10 flex items-center justify-center" aria-label="Buka menu">
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
        <button id="mobile-close" type="button" class="text-gold-light-accent text-xl w-10 h-10 flex items-center justify-center" aria-label="Tutup menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <ul class="flex flex-col px-5 py-6 gap-1">
        <li><a href="#beranda" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Beranda</a></li>
        <li><a href="#layanan" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Layanan</a></li>
        <li><a href="#template" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Template</a></li>
        <li><a href="#testimoni" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Testimoni</a></li>
        <li><a href="#faq" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">FAQ</a></li>
        <li><a href="#kontak" class="mobile-nav-link block py-3 border-b border-white/5 text-on-dark hover:text-gold-light-accent transition-colors">Kontak</a></li>
    </ul>
    <div class="px-5 mt-4">
        <a href="https://wa.me/6285777743388?text=Halo%20Web%20Untal%2C%20saya%20ingin%20pesan%20undangan%20digital"
           target="_blank"
           rel="noopener noreferrer"
           class="btn-gold mobile-nav-link w-full py-3 rounded-full text-sm">
            <i class="fa-brands fa-whatsapp"></i>
            Pesan Sekarang
        </a>
    </div>
</div>
