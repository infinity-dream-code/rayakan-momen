@php
    $categories = config('templates.categories', []);
    $waNumber = '6285777433886';
@endphp

<section id="template" class="relative section-light py-24 md:py-32 overflow-hidden">
    <img src="{{ asset('images/floral-corner.svg') }}" alt="" class="ornament-corner bottom-left hidden md:block" aria-hidden="true">

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-10 md:mb-14 reveal">
            <p class="section-label">Katalog Undangan</p>
            <h2 class="section-heading text-left mb-3">Pilih jenis undanganmu</h2>
            <p class="text-muted text-sm max-w-xl leading-relaxed">
                Setiap jenis punya desain &amp; harga sendiri. Buka katalog untuk lihat semua produk, demo, dan pesan langsung.
            </p>
        </div>

        <div class="grid sm:grid-cols-3 gap-5 md:gap-6 mb-10">
            @foreach ($categories as $kat)
                @php
                    $imgKey = match ($kat['id']) {
                        'wedding' => 'cat_wedding',
                        'ultah_anak' => 'cat_ultah',
                        'couple' => 'cat_couple',
                        default => 'cat_wedding',
                    };
                @endphp
                <a href="{{ route('katalog', ['kategori' => $kat['id']]) }}" class="home-cat-card reveal">
                    <div class="home-cat-card__media">
                        <img
                            src="{{ cdn_image($imgKey, 'f_auto,q_auto:eco,w_640,c_fill,g_auto') }}"
                            alt="{{ $kat['nama'] }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                    <div class="home-cat-card__body">
                        <span class="home-cat-card__label">{{ $kat['tagline'] }}</span>
                        <h3 class="font-display">{{ $kat['nama'] }}</h3>
                        <span class="home-cat-card__cta">Lihat desain <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center reveal">
            <a href="{{ route('katalog') }}" class="home-katalog-all">
                Lihat Semua Katalog
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<style>
.home-cat-card{
    display:block;border-radius:1.1rem;overflow:hidden;background:#fff;
    border:1px solid rgba(201,168,76,.22);text-decoration:none;color:inherit;
    transition:transform .25s ease, box-shadow .25s ease;
}
.home-cat-card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(26,34,52,.1)}
.home-cat-card__media{aspect-ratio:4/5;overflow:hidden;background:#1a2234}
.home-cat-card__media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .45s ease}
.home-cat-card:hover .home-cat-card__media img{transform:scale(1.04)}
.home-cat-card__body{padding:1.1rem 1.15rem 1.25rem}
.home-cat-card__label{display:block;font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:#a8843a;margin-bottom:.35rem}
.home-cat-card__body h3{margin:0 0 .65rem;font-size:1.2rem;color:#1a2234}
.home-cat-card__cta{font-size:.8rem;font-weight:600;color:#1a2234}
.home-cat-card__cta i{font-size:.7rem;margin-left:.25rem;color:#c9a84c}
.home-katalog-all{
    display:inline-flex;align-items:center;gap:.55rem;
    padding:.85rem 1.5rem;border-radius:.85rem;
    border:1.5px solid #1a2234;color:#1a2234;font-weight:600;font-size:.9rem;
    text-decoration:none;background:#fff;transition:all .2s ease;
}
.home-katalog-all:hover{background:#1a2234;color:#e8d5a3}
.home-katalog-all i{font-size:.75rem}
</style>
