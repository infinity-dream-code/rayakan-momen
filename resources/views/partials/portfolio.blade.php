@php
    $categories = config('templates.categories', []);
@endphp

<section id="template" class="relative section-light py-16 md:py-24 pb-20 md:pb-28 overflow-hidden">
    <img src="{{ asset('images/floral-corner.svg') }}" alt="" class="ornament-corner bottom-left hidden md:block" aria-hidden="true">

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-8 md:mb-10 reveal">
            <p class="section-label">Katalog Undangan</p>
            <h2 class="section-heading text-left mb-2">Pilih jenis undanganmu</h2>
            <p class="text-muted text-sm max-w-xl leading-relaxed">
                Buka katalog untuk lihat desain, harga, demo, dan pesan langsung.
            </p>
        </div>

        <div class="home-cat-grid mb-8">
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
                            src="{{ cdn_image($imgKey, 'f_auto,q_auto:eco,w_480,c_fill,g_auto') }}"
                            alt="{{ $kat['nama'] }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                    <div class="home-cat-card__body">
                        <span class="home-cat-card__label">{{ $kat['nama'] }}</span>
                        <h3 class="font-display">{{ $kat['tagline'] }}</h3>
                        <span class="home-cat-card__cta">Lihat desain <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="home-katalog-cta reveal">
            <a href="{{ route('katalog') }}" class="home-katalog-all">
                Lihat Semua Katalog
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<style>
#template.section-light{
    padding-bottom:5rem !important;
}
@media(min-width:768px){
    #template.section-light{padding-bottom:6.5rem !important}
}
/* Grid pakai CSS sendiri (jangan andalkan Tailwind purge) */
.home-cat-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:1rem;
}
@media(min-width:640px){
    .home-cat-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:1.15rem}
}
.home-cat-card{
    display:flex;
    flex-direction:column;
    border-radius:.9rem;
    overflow:hidden;
    background:#fff;
    border:1px solid rgba(201,168,76,.22);
    text-decoration:none;
    color:inherit;
    transition:transform .2s ease, box-shadow .2s ease;
    max-width:100%;
}
.home-cat-card:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 28px rgba(26,34,52,.08);
}
.home-cat-card__media{
    aspect-ratio:16/10;
    overflow:hidden;
    background:#1a2234;
}
@media(min-width:640px){
    .home-cat-card__media{aspect-ratio:4/3}
}
.home-cat-card__media img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
    transition:transform .4s ease;
}
.home-cat-card:hover .home-cat-card__media img{transform:scale(1.03)}
.home-cat-card__body{
    padding:.85rem 1rem 1rem;
    display:flex;
    flex-direction:column;
    gap:.2rem;
}
.home-cat-card__label{
    font-size:.65rem;
    letter-spacing:.1em;
    text-transform:uppercase;
    color:#a8843a;
    font-weight:600;
}
.home-cat-card__body h3{
    margin:0;
    font-size:.95rem;
    line-height:1.35;
    color:#1a2234;
    font-weight:600;
}
@media(min-width:768px){
    .home-cat-card__body h3{font-size:1.05rem}
}
.home-cat-card__cta{
    margin-top:.45rem;
    font-size:.78rem;
    font-weight:600;
    color:#1a2234;
}
.home-cat-card__cta i{
    font-size:.65rem;
    margin-left:.2rem;
    color:#c9a84c;
}
.home-katalog-cta{
    text-align:center;
    padding-bottom:2.5rem;
}
@media(min-width:768px){
    .home-katalog-cta{padding-bottom:3.5rem}
}
.home-katalog-all{
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    padding:.7rem 1.25rem;
    border-radius:.75rem;
    border:1.5px solid #1a2234;
    color:#1a2234;
    font-weight:600;
    font-size:.85rem;
    text-decoration:none;
    background:#fff;
    transition:all .2s ease;
}
.home-katalog-all:hover{background:#1a2234;color:#e8d5a3}
.home-katalog-all i{font-size:.7rem}
</style>
