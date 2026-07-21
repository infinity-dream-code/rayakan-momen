@php
    use App\Repositories\CatalogRepository;
    use App\Repositories\CategoryRepository;

    $categories = $categories ?? app(CategoryRepository::class)->allActive();
    $homeTemplates = $homeTemplates ?? [];
    $catalog = app(CatalogRepository::class);
    $waNumber = '6285777433886';
@endphp

<section id="template" class="relative section-light py-16 md:py-24 pb-20 md:pb-28 overflow-hidden">
    <img src="{{ asset('images/floral-corner.svg') }}" alt="" class="ornament-corner bottom-left hidden md:block" aria-hidden="true">

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-8 md:mb-10 reveal">
            <p class="section-label">Katalog Undangan</p>
            <h2 class="section-heading text-left mb-2">Pilih desain favoritmu</h2>
            <p class="text-muted text-sm max-w-xl leading-relaxed">
                Lihat demo, atau pesan langsung via WhatsApp.
            </p>
        </div>

        @if (count($homeTemplates) > 0)
            <div class="home-tpl-grid mb-8">
                @foreach ($homeTemplates as $key => $t)
                    @php
                        $kat = $categories[$t['kategori'] ?? ''] ?? null;
                        $final = (int) ($t['harga_final'] ?? 0);
                        $hargaLabel = $final > 0 ? $catalog->formatRupiah($final) : null;
                        $waText = rawurlencode('Halo Rayakan Momen, saya mau pesan undangan '.$t['nama'].($hargaLabel ? ' ('.$hargaLabel.')' : ''));
                        $previewFallback = match ($t['kategori'] ?? '') {
                            'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
                            'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
                            'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
                            default => '',
                        };
                        $cover = ! empty($t['preview']) ? $t['preview'] : $previewFallback;
                    @endphp
                    <article class="home-cat-card reveal">
                        <a href="{{ $t['demo_url'] ?? route('katalog') }}" target="_blank" rel="noopener noreferrer" class="home-cat-card__media">
                            @if ($cover)<img src="{{ $cover }}" alt="{{ $t['nama'] }}" loading="lazy" decoding="async">@endif
                        </a>
                        <div class="home-cat-card__body">
                            <span class="home-cat-card__label">{{ $kat['nama'] ?? 'Undangan' }}</span>
                            <h3 class="font-display">{{ $t['nama'] }}</h3>
                            @if ($hargaLabel)
                                <p class="text-sm text-[#a8843a] font-semibold mb-1">{{ $hargaLabel }}</p>
                            @endif
                            <div class="home-cat-card__actions">
                                @if (! empty($t['demo_url']))
                                    <a href="{{ $t['demo_url'] }}" target="_blank" rel="noopener noreferrer" class="home-cat-btn home-cat-btn--demo">Demo</a>
                                @else
                                    <span class="home-cat-btn home-cat-btn--demo" style="opacity:.45">Demo</span>
                                @endif
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="home-cat-btn home-cat-btn--wa">
                                    <i class="fa-brands fa-whatsapp"></i> Pakai
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 mb-6">Belum ada template di beranda. Centang <strong>Home</strong> di admin Setting.</p>
        @endif

        <div class="home-katalog-cta reveal">
            <a href="{{ route('katalog') }}" class="home-katalog-all">
                Lihat Semua
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<style>
#template.section-light{padding-bottom:5rem !important}
@media(min-width:768px){#template.section-light{padding-bottom:6.5rem !important}}
.home-tpl-grid,.home-cat-grid{
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem;
}
@media(min-width:768px){.home-tpl-grid,.home-cat-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:1.15rem}}
.home-cat-card{
    display:flex;flex-direction:column;border-radius:.9rem;overflow:hidden;
    background:#fff;border:1px solid rgba(201,168,76,.22);color:inherit;
    transition:transform .2s ease,box-shadow .2s ease;
}
.home-cat-card:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(26,34,52,.08)}
.home-cat-card__media{display:block;aspect-ratio:3/4;overflow:hidden;background:#1a2234}
.home-cat-card__media img{width:100%;height:100%;object-fit:cover;object-position:top;display:block}
.home-cat-card__body{padding:.85rem 1rem 1rem;display:flex;flex-direction:column;gap:.2rem;flex:1}
.home-cat-card__label{font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:#a8843a;font-weight:600}
.home-cat-card__body h3{margin:0 0 .35rem;font-size:.95rem;line-height:1.35;color:#1a2234;font-weight:600}
.home-cat-card__actions{display:grid;grid-template-columns:1fr 1fr;gap:.4rem;margin-top:auto}
.home-cat-btn{display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.55rem .4rem;border-radius:.55rem;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid transparent}
.home-cat-btn--demo{background:#fff;color:#1a2234;border-color:#d8d2c8}
.home-cat-btn--wa{background:#1a2234;color:#e8d5a3}
.home-katalog-cta{text-align:center;padding-bottom:2.5rem}
.home-katalog-all{display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;border-radius:.75rem;border:1.5px solid #1a2234;color:#1a2234;font-weight:600;font-size:.85rem;text-decoration:none;background:#fff}
.home-katalog-all:hover{background:#1a2234;color:#e8d5a3}
</style>
