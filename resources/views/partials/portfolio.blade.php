@php
    $categories = config('templates.categories', []);
    $catalog = app(\App\Repositories\CatalogRepository::class);
    $allTemplates = collect($catalog->templates())
        ->filter(fn ($t) => ($t['aktif_katalog'] ?? true))
        ->all();
    $waNumber = '6285777743388';
@endphp

<section id="template" class="relative section-light py-24 md:py-32 overflow-hidden">
    <img src="{{ asset('images/floral-corner.svg') }}" alt="" class="ornament-corner bottom-left hidden md:block" aria-hidden="true">

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-end mb-12">
            <div class="lg:col-span-6 reveal">
                <p class="section-label">Katalog Template</p>
                <h2 class="section-heading text-left mb-0">Pilih sesuai momenmu</h2>
            </div>
            <div class="lg:col-span-5 lg:col-start-8 reveal reveal-delay-1">
                <p class="text-muted text-sm leading-relaxed">
                    Tiap template punya harga sendiri (bisa ada diskon). Lihat demo live, lalu pesan langsung via WhatsApp.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-10 reveal" id="templateFilters">
            <button type="button" class="market-chip is-active" data-filter="all">Semua</button>
            @foreach ($categories as $kat)
                <button type="button" class="market-chip" data-filter="{{ $kat['id'] }}">{{ $kat['nama'] }}</button>
            @endforeach
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7" id="templateGrid">
            @foreach ($allTemplates as $key => $t)
                @php
                    $kat = $categories[$t['kategori']] ?? null;
                    $harga = (int) ($t['harga'] ?? 0);
                    $final = (int) ($t['harga_final'] ?? $harga);
                    $diskon = (float) ($t['diskon_persen'] ?? 0);
                    $punyaDiskon = ! empty($t['punya_diskon']);
                    $hargaFinalLabel = $final > 0 ? $catalog->formatRupiah($final) : null;
                    $hargaAwalLabel = $harga > 0 ? $catalog->formatRupiah($harga) : null;
                    $waText = rawurlencode('Halo Rayakan Momen, saya mau pesan template '.$t['nama'].($hargaFinalLabel ? ' ('.$hargaFinalLabel.')' : ''));
                    $previewFallback = match ($t['kategori'] ?? '') {
                        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_720,c_fill,g_auto'),
                        default => '',
                    };
                    $previewSrc = ! empty($t['preview']) ? $t['preview'] : $previewFallback;
                @endphp
                <article class="portfolio-card market-card reveal"
                         data-category="{{ $t['kategori'] }}">
                    <div class="relative aspect-[4/5] overflow-hidden bg-navy-custom">
                        @if ($previewSrc)
                            <img
                                src="{{ $previewSrc }}"
                                alt="Preview {{ $t['nama'] }}"
                                class="portfolio-img absolute inset-0 w-full h-full object-cover object-top"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="market-placeholder absolute inset-0">
                                <i class="fa-solid {{ $kat['icon'] ?? 'fa-image' }}"></i>
                                <span>Gambar segera</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-charcoal/80 via-transparent to-transparent"></div>
                        <span class="portfolio-tag" style="background: {{ $t['warna'] }};">{{ $t['nama'] }}</span>
                        @if ($kat)
                            <span class="market-cat-badge">{{ $kat['nama'] }}</span>
                        @endif
                        @if ($punyaDiskon)
                            <span class="market-discount-badge">-{{ (int) $diskon }}%</span>
                        @endif
                        @if ($hargaFinalLabel)
                            <span class="market-price-badge">{{ $hargaFinalLabel }}</span>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="font-display text-xl text-charcoal mb-1.5">{{ $t['nama'] }}</h3>
                        <p class="text-sm text-muted mb-4 leading-relaxed flex-1">{{ $t['deskripsi'] }}</p>

                        @if ($hargaFinalLabel)
                            <div class="market-price-row mb-4">
                                @if ($punyaDiskon && $hargaAwalLabel)
                                    <span class="market-price-old">{{ $hargaAwalLabel }}</span>
                                @endif
                                <span class="market-price-amount">{{ $hargaFinalLabel }}</span>
                                <span class="market-price-note">per undangan</span>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-gold px-4 py-2.5 rounded-full text-xs">
                                <i class="fa-brands fa-whatsapp"></i>
                                Pesan
                            </a>
                            @if (! empty($t['demo_url']))
                                <a href="{{ $t['demo_url'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 text-sm font-semibold text-gold-dark-accent hover:text-gold-accent transition-colors">
                                    Demo
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            @else
                                <span class="text-xs text-muted">Preview segera</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-center text-xs text-muted mt-10 reveal">
            Harga &amp; diskon diatur dari admin. Request khusus (custom domain, animasi ekstra) chat kami.
        </p>
    </div>
</section>
