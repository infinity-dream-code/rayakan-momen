<?php

namespace App\Services;

class InvitationTemplateRenderer
{
    public function render(array $undangan): string
    {
        $tema = $undangan['tema'] ?? 'elegan';
        $meta = config('templates.templates.'.$tema, []);

        $path = $this->resolveTemplateFile($tema, $meta['file'] ?? null);

        if ($path !== null) {
            $html = file_get_contents($path);
            if ($html === false) {
                $html = '';
            } else {
                // Ganti foto dulu (path assets/ relatif), baru rewrite URL asset template
                $html = $this->replaceCoupleNames($html, $tema, $undangan);
                $html = $this->replaceParents($html, $undangan);
                $html = $this->replaceQuote($html, $undangan);
                $html = $this->replacePhotos($html, $tema, $undangan);
                $html = $this->injectCoupleConfig($html, $tema, $undangan);
                $html = $this->injectUltahData($html, $tema, $undangan);
                $html = $this->replaceMusic($html, $undangan);
                $html = $this->rewriteAssetUrls($html, $tema);
                $html = $this->replaceEventDetails($html, $undangan);
                $html = $this->replaceMaps($html, $undangan);
                $html = $this->replaceStory($html, $undangan);
                $html = $this->replaceGallery($html, $undangan);
                $html = $this->replaceBanks($html, $undangan);
                $html = $this->attachImageErrorHandlers($html);
                $html = $this->injectCopyright($html);
                $html = $this->injectSeoMeta($html, $undangan, $meta);
                $html = $this->injectBridge($html, $undangan);

                return $html;
            }
        }

        $kategori = $meta['kategori'] ?? ($undangan['kategori'] ?? 'wedding');
        $blade = $meta['blade']
            ?? (view()->exists('templates.'.$tema) ? 'templates.'.$tema : null)
            ?? match ($kategori) {
                'ultah_anak' => 'undangan.preview.ultah',
                'couple' => 'undangan.preview.couple',
                default => 'undangan.preview.wedding',
            };

        return view($blade, [
            'undangan' => $undangan,
            'template' => $meta,
            'kategori' => $kategori,
            'categories' => config('templates.categories', []),
            'seo' => $this->buildSeo($undangan, $meta),
        ])->render();
    }

    /**
     * Cari file HTML template: config path, lalu sumber di template_undangan/.
     */
    protected function resolveTemplateFile(string $tema, ?string $file): ?string
    {
        $candidates = [];

        if (filled($file)) {
            $candidates[] = base_path($file);
        }

        $sourceMap = [
            'elegan' => 'template_undangan/template_wedding/template 1/index.html',
            'classic' => 'template_undangan/template_wedding/template 2/index.html',
            'langit_malam' => 'template_undangan/template_wedding/template 3/index.html',
            'adat_jawa' => 'template_undangan/template_wedding/wedding_adat_jawa/template 4/index.html',
            'wedding_islam' => 'template_undangan/template_wedding/wedding_islam/index.html',
            'couple_surat' => 'template_undangan/template couple/index.html',
            'ultah_candyland' => 'template_undangan/template_ultah/template ultah 1/index.html',
            'ultah_bintang' => 'template_undangan/template_ultah/template ultah 2/index.blade.php',
        ];

        if (isset($sourceMap[$tema])) {
            $candidates[] = base_path($sourceMap[$tema]);
        }

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Ubah assets/ relatif → URL publik di /templates/{tema}/assets/
     * Termasuk string di JavaScript (kupu-kupu, petal, dll).
     */
    protected function rewriteAssetUrls(string $html, string $tema): string
    {
        $base = rtrim(asset('templates/'.$tema.'/assets'), '/').'/';

        // url(assets/...) di CSS
        $html = preg_replace(
            '/url\((["\']?)assets\//i',
            'url($1'.$base,
            $html
        ) ?? $html;

        // Semua literal "assets/..." atau 'assets/...' (img src, JS fetch, array, dll)
        // Jangan pakai replace bertingkat supaya tidak dobel-prefix.
        $html = preg_replace(
            '/([\'"])assets\//',
            '$1'.$base,
            $html
        ) ?? $html;

        return $html;
    }

    protected function buildSeo(array $u, array $meta): array
    {
        $kat = $meta['kategori'] ?? ($u['kategori'] ?? 'wedding');
        $slug = $u['slug'] ?? '';
        $url = url('/'.$slug);

        if ($kat === 'ultah_anak') {
            $name = $u['nama_anak'] ?? $u['nama_wanita'] ?? 'Ultah';
            $title = $name.' — Undangan Ulang Tahun';
            $desc = 'Undangan digital ulang tahun '.$name.'. Dibuat dengan Rayakan Momen.';
        } elseif ($kat === 'couple') {
            $name = trim(($u['nama_pria'] ?? '').' & '.($u['nama_wanita'] ?? ''));
            $title = $name.' — Surat Spesial';
            $desc = 'Surat digital spesial untuk '.$name.'. Dibuat dengan Rayakan Momen.';
        } else {
            $name = trim(($u['nama_wanita'] ?? '').' & '.($u['nama_pria'] ?? ''));
            $title = $name.' — Undangan Pernikahan';
            $desc = 'Undangan pernikahan digital '.$name.'. Dibuat dengan Rayakan Momen.';
        }

        $tema = (string) ($meta['id'] ?? ($u['tema'] ?? 'elegan'));
        $image = $this->resolveOgImage($u, $tema);

        return compact('title', 'desc', 'url', 'image', 'name');
    }

    /**
     * Foto preview WhatsApp/OG = "foto utama" per template.
     * - elegan / langit_malam / classic: foto pertama di galeri
     * - adat_jawa: foto mempelai di awal (bukan galeri)
     * - ultah_*: foto anak
     * - couple: foto couple di awal
     */
    protected function resolveOgImage(array $u, string $tema): ?string
    {
        $fromKeys = function (array $keys) use ($u): ?string {
            foreach ($keys as $key) {
                if (! empty($u[$key])) {
                    return $this->mediaUrl($u[$key]);
                }
            }

            return null;
        };

        $galeriUtama = ! empty($u['galeri'][0]) ? $this->mediaUrl($u['galeri'][0]) : null;
        $fotoMempelai = $fromKeys(['foto_wanita', 'foto_pria', 'cover_image']);
        $fotoAnak = $fromKeys(['foto_anak', 'foto_wanita', 'foto_pria', 'cover_image']);
        $fotoCouple = $fromKeys(['foto_pria', 'foto_wanita', 'cover_image']);
        $fotoFormal = $fromKeys(['foto_formal', 'foto_wanita', 'foto_pria', 'cover_image']);

        return match (true) {
            // Template 1, 2, 3, Islam: foto utama = foto pertama di galeri
            in_array($tema, ['elegan', 'classic', 'langit_malam', 'wedding_islam'], true) => $galeriUtama ?? $fotoMempelai,
            // Template 4: foto formal intro, fallback mempelai
            $tema === 'adat_jawa' => $fotoFormal ?? $galeriUtama,
            str_starts_with($tema, 'ultah_') => $fotoAnak ?? $galeriUtama,
            $tema === 'couple_surat' => $fotoCouple ?? $galeriUtama,
            default => $galeriUtama ?? $fotoMempelai,
        };
    }

    protected function injectSeoMeta(string $html, array $u, array $meta): string
    {
        $seo = $this->buildSeo($u, $meta);
        $title = e($seo['title']);
        $desc = e($seo['desc']);
        $url = e($seo['url']);
        $image = e($seo['image'] ?? cdn_image('hero_wedding', 'f_auto,q_auto:eco,w_1200,c_fill,g_auto'));

        $html = preg_replace('/<title>.*?<\/title>/is', '<title>'.$title.'</title>', $html, 1) ?? $html;

        $tags = implode("\n", [
            '<meta name="description" content="'.$desc.'">',
            '<link rel="canonical" href="'.$url.'">',
            '<meta property="og:type" content="website">',
            '<meta property="og:title" content="'.$title.'">',
            '<meta property="og:description" content="'.$desc.'">',
            '<meta property="og:url" content="'.$url.'">',
            '<meta property="og:image" content="'.$image.'">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:title" content="'.$title.'">',
            '<meta name="twitter:description" content="'.$desc.'">',
            '<meta name="twitter:image" content="'.$image.'">',
            '<meta name="robots" content="index,follow">',
        ]);

        if (stripos($html, '</head>') !== false) {
            $html = preg_replace('/<\/head>/i', $tags."\n</head>", $html, 1) ?? $html;
        }

        return $html;
    }

    protected function replaceCoupleNames(string $html, string $tema, array $u): string
    {
        $wanita = trim((string) ($u['nama_wanita'] ?? ''));
        $pria = trim((string) ($u['nama_pria'] ?? ''));
        $wanitaL = trim((string) (($u['nama_lengkap_wanita'] ?? '') ?: $wanita));
        $priaL = trim((string) (($u['nama_lengkap_pria'] ?? '') ?: $pria));

        $pairs = match ($tema) {
            'classic' => [
                ['Alia Putri Rahmawati, S.Kom.', $wanitaL],
                ['Reza Pratama Wijaya, S.E.', $priaL],
                ['ALIA PUTRI RAHMAWATI, S.KOM.', mb_strtoupper($wanitaL)],
                ['REZA PRATAMA WIJAYA, S.E.', mb_strtoupper($priaL)],
                ['Alia &amp; Reza', $wanita.' & '.$pria],
                ['Alia & Reza', $wanita.' & '.$pria],
                ['Alia', $wanita],
                ['Reza', $pria],
                ['ALIA', mb_strtoupper($wanita)],
                ['REZA', mb_strtoupper($pria)],
            ],
            'langit_malam' => [
                ['Kanaya Ardhita Wibowo, S.Psi.', $wanitaL],
                ['Alvaro Kusuma Bramantyo, S.T.', $priaL],
                ['KANAYA ARDHITA WIBOWO, S.PSI.', mb_strtoupper($wanitaL)],
                ['ALVARO KUSUMA BRAMANTYO, S.T.', mb_strtoupper($priaL)],
                ['Kanaya &amp; Alvaro', $wanita.' & '.$pria],
                ['Kanaya & Alvaro', $wanita.' & '.$pria],
                ['Kanaya', $wanita],
                ['Alvaro', $pria],
                ['KANAYA', mb_strtoupper($wanita)],
                ['ALVARO', mb_strtoupper($pria)],
            ],
            'adat_jawa' => [
                ['Shinta Laras Putri, S.Pd.', $wanitaL],
                ['Rama Khrisna Putra, S.T.', $priaL],
                ['SHINTA LARAS PUTRI, S.PD.', mb_strtoupper($wanitaL)],
                ['RAMA KHRISNA PUTRA, S.T.', mb_strtoupper($priaL)],
                ['Laras &amp; Khrisna', $wanita.' & '.$pria],
                ['Laras & Khrisna', $wanita.' & '.$pria],
                ['#LarasKhrisna', '#'.preg_replace('/\s+/', '', $wanita.$pria)],
                ['Laras', $wanita],
                ['Khrisna', $pria],
                ['LARAS', mb_strtoupper($wanita)],
                ['KHRISNA', mb_strtoupper($pria)],
            ],
            'wedding_islam' => [
                ['Ahmad Zaki Ramadhan', $priaL],
                ['Nur Aisyah Putri', $wanitaL],
                ['AHMAD ZAKI RAMADHAN', mb_strtoupper($priaL)],
                ['NUR AISYAH PUTRI', mb_strtoupper($wanitaL)],
                // HTML amp — jangan di-escape ulang (flag false)
                [
                    'Ahmad Zaki <span class="text-gold-400 italic">&amp;</span> Nur Aisyah',
                    e($pria).' <span class="text-gold-400 italic">&amp;</span> '.e($wanita),
                    false,
                ],
                [
                    'Ahmad Zaki <span class="amp">&amp;</span> Nur Aisyah',
                    e($pria).' <span class="amp">&amp;</span> '.e($wanita),
                    false,
                ],
                ['Ahmad Zaki &amp; Nur Aisyah', $pria.' & '.$wanita],
                ['Ahmad Zaki & Nur Aisyah', $pria.' & '.$wanita],
                ['Zaki &amp; Aisyah', $pria.' & '.$wanita],
                ['Zaki & Aisyah', $pria.' & '.$wanita],
                ['#ZakiAisyahBersanding', '#'.preg_replace('/\s+/', '', $pria.$wanita)],
                ['Zaki', $pria],
                ['Aisyah', $wanita],
            ],
            default => [
                ['NICO WARDHANA', mb_strtoupper($priaL)],
                ['Nico Wardhana', $priaL],
                ['WAGURI', mb_strtoupper($wanitaL)],
                ['Waguri &amp; Nico', $wanita.' & '.$pria],
                ['Waguri & Nico', $wanita.' & '.$pria],
                ['Waguri', $wanita],
                ['Nico', $pria],
                ['NICO', mb_strtoupper($pria)],
            ],
        };

        foreach ($pairs as $pair) {
            $from = $pair[0] ?? '';
            $to = $pair[1] ?? '';
            $escape = $pair[2] ?? true;
            if ($from !== '' && $to !== '') {
                $html = str_replace($from, $escape ? e($to) : $to, $html);
            }
        }

        if ($wanita !== '' && $pria !== '') {
            $title = e($wanita.' & '.$pria.' — Undangan Pernikahan');
            $html = preg_replace('/<title>.*?<\/title>/is', '<title>'.$title.'</title>', $html, 1);
        }

        return $html;
    }

    protected function replaceParents(string $html, array $u): string
    {
        $ortuW = $this->formatParentLine($u, 'wanita');
        $ortuP = $this->formatParentLine($u, 'pria');

        if ($ortuW !== '') {
            $html = preg_replace(
                '/(<p class="parents">\s*Putri dari\s*<br\s*\/?\s*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($ortuW).'$3',
                $html,
                1
            ) ?? $html;
        }

        if ($ortuP !== '') {
            $html = preg_replace(
                '/(<p class="parents">\s*Putra dari\s*<br\s*\/?\s*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($ortuP).'$3',
                $html,
                1
            ) ?? $html;
        }

        // Wedding Islam: baris ortu di bawah label PUTRA/PUTRI … DARI
        if ($ortuP !== '') {
            $html = preg_replace(
                '/(PUTRA[^<]*DARI<\/p>\s*<p class="text-ivory\/70[^"]*"[^>]*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($ortuP).'$3',
                $html,
                1
            ) ?? $html;
        }
        if ($ortuW !== '') {
            $html = preg_replace(
                '/(PUTRI[^<]*DARI<\/p>\s*<p class="text-ivory\/70[^"]*"[^>]*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($ortuW).'$3',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function replaceQuote(string $html, array $u): string
    {
        $kutipan = trim((string) ($u['kutipan'] ?? ''));
        $sumber = trim((string) ($u['kutipan_sumber'] ?? ''));

        if ($kutipan === '' && $sumber === '') {
            return $html;
        }

        $kutipanClean = trim($kutipan, " \t\n\r\0\x0B\"'“”„");

        if ($kutipanClean !== '') {
            $html = preg_replace(
                '/(<p class="quote">)([\s\S]*?)(<\/p>)/i',
                '$1"'.e($kutipanClean).'"$3',
                $html,
                1
            ) ?? $html;

            $html = preg_replace(
                '/(<blockquote class="hero-quote[^"]*"[^>]*>)\s*[\s\S]*?(<cite>)/i',
                '$1'."\n                    &ldquo;".e($kutipanClean)."&rdquo;\n                    ".'$2',
                $html,
                1
            ) ?? $html;
        }

        if ($sumber !== '') {
            $html = preg_replace(
                '/(<p class="quote-src">)([\s\S]*?)(<\/p>)/i',
                '$1'.e($sumber).'$3',
                $html,
                1
            ) ?? $html;

            $html = preg_replace(
                '/(<blockquote class="hero-quote[^"]*"[^>]*>[\s\S]*?<cite>)([\s\S]*?)(<\/cite>)/i',
                '$1'.e($sumber).'$3',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function formatParentLine(array $u, string $side): string
    {
        $ayah = trim((string) ($u['ayah_'.$side] ?? ''));
        $ibu = trim((string) ($u['ibu_'.$side] ?? ''));

        if ($ayah === '' && $ibu === '') {
            return trim((string) ($u['ortu_'.$side] ?? ''));
        }

        $parts = [];
        if ($ayah !== '') {
            $parts[] = 'Bapak '.$ayah;
        }
        if ($ibu !== '') {
            $parts[] = 'Ibu '.$ibu;
        }

        return implode(' & ', $parts);
    }

    /**
     * URL publik untuk file upload (hindari double prefix / index.php).
     */
    protected function mediaUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = trim((string) $path);

        // Sudah absolute
        if (preg_match('#^https?://#i', $path)) {
            // Perbaiki URL rusak lama: .../templates/elegan/https://...
            if (preg_match('#https?://.+(https?://.+)$#i', $path, $m)) {
                return $m[1];
            }

            return $path;
        }

        $relative = ltrim($path, '/');
        $url = asset($relative);
        $full = public_path($relative);
        if (is_file($full)) {
            $url .= (str_contains($url, '?') ? '&' : '?').'v='.filemtime($full);
        }

        return $url;
    }

    protected function mediaFileExists(?string $path): bool
    {
        if (! filled($path)) {
            return false;
        }

        $path = trim((string) $path);
        if (preg_match('#^https?://#i', $path)) {
            return true; // remote — biarkan browser yang cek
        }

        return is_file(public_path(ltrim($path, '/')));
    }

    protected function replacePhotos(string $html, string $tema, array $u): string
    {
        // Marker unik di file default template (jangan str_replace path pendek setelah rewrite)
        $markers = [
            'elegan' => [
                'wanita' => ['de45a5997368ad7f1afa624dc7d2417a'],
                'pria' => ['d279b8a5f1d58d6dbe6c598d0b37b072'],
            ],
            'classic' => [
                'wanita' => ['76209dcd4b79a4849d031c22dc1d7fbc'],
                'pria' => ['0ef13b4f10cc3b4e2594591ef63a9c20'],
            ],
            'langit_malam' => [
                'wanita' => ['photo-1544005313-94ddf0286df2'],
                'pria' => ['photo-1500648767791-00dcc994a43e'],
            ],
            'adat_jawa' => [
                'wanita' => ['Ruliff-Refina-CPW'],
                'pria' => ['Ruliff-Refina-CPP'],
            ],
            'wedding_islam' => [
                'wanita' => ['bride.jpg'],
                'pria' => ['groom.jpg'],
            ],
        ];

        $map = $markers[$tema] ?? $markers['elegan'];

        $wanita = (! empty($u['foto_wanita']) && $this->mediaFileExists($u['foto_wanita']))
            ? $this->mediaUrl($u['foto_wanita'])
            : null;
        $pria = (! empty($u['foto_pria']) && $this->mediaFileExists($u['foto_pria']))
            ? $this->mediaUrl($u['foto_pria'])
            : null;

        if ($wanita) {
            foreach ($map['wanita'] as $marker) {
                $html = preg_replace(
                    '/(<img\b[^>]*\bsrc=")[^"]*'.preg_quote($marker, '/').'[^"]*(")/i',
                    '$1'.e($wanita).'$2',
                    $html
                ) ?? $html;
            }
            // Bersihkan URL rusak double-prefix
            $html = preg_replace(
                '/(<img\b[^>]*\bsrc=")[^"]*templates\/[^"\/]+\/https?:\/\/[^"]*foto-wanita[^"]*(")/i',
                '$1'.e($wanita).'$2',
                $html
            ) ?? $html;
        }

        if ($pria) {
            foreach ($map['pria'] as $marker) {
                $html = preg_replace(
                    '/(<img\b[^>]*\bsrc=")[^"]*'.preg_quote($marker, '/').'[^"]*(")/i',
                    '$1'.e($pria).'$2',
                    $html
                ) ?? $html;
            }
            $html = preg_replace(
                '/(<img\b[^>]*\bsrc=")[^"]*templates\/[^"\/]+\/https?:\/\/[^"]*foto-pria[^"]*(")/i',
                '$1'.e($pria).'$2',
                $html
            ) ?? $html;
        }

        // Adat Jawa: foto formal intro (berdua) — bukan galeri
        if ($tema === 'adat_jawa') {
            $formal = (! empty($u['foto_formal']) && $this->mediaFileExists($u['foto_formal']))
                ? $this->mediaUrl($u['foto_formal'])
                : null;
            if ($formal) {
                $html = preg_replace(
                    '/(<div class="intro-photo[^"]*"[^>]*>\s*<img\b[^>]*\bsrc=")[^"]*(")/i',
                    '$1'.e($formal).'$2',
                    $html,
                    1
                ) ?? $html;
                $html = preg_replace(
                    '/(<img\b[^>]*\bsrc=")[^"]*Ruliff-Refina-14[^"]*(")/i',
                    '$1'.e($formal).'$2',
                    $html
                ) ?? $html;
            }
        }

        return $html;
    }

    protected function replaceMaps(string $html, array $u): string
    {
        $akad = trim((string) ($u['maps_url'] ?? ''));
        $resepsi = trim((string) ($u['maps_url_resepsi'] ?? ''));
        if ($resepsi === '') {
            $resepsi = $akad;
        }

        if ($akad === '' && $resepsi === '') {
            return $html;
        }

        $i = 0;
        $html = preg_replace_callback(
            '/(<a\b[^>]*\bclass="[^"]*(?:map-btn|event-map)[^"]*"[^>]*\bhref=")[^"]*(")/i',
            function (array $m) use (&$i, $akad, $resepsi) {
                $url = $i === 0 ? ($akad ?: $resepsi) : ($resepsi ?: $akad);
                $i++;

                return $m[1].htmlspecialchars($url, ENT_QUOTES, 'UTF-8').$m[2];
            },
            $html
        ) ?? $html;

        // href dulu baru class
        if ($i === 0) {
            $html = preg_replace_callback(
                '/(<a\b[^>]*\bhref=")[^"]*("[^>]*\bclass="[^"]*(?:map-btn|event-map)[^"]*")/i',
                function (array $m) use (&$i, $akad, $resepsi) {
                    $url = $i === 0 ? ($akad ?: $resepsi) : ($resepsi ?: $akad);
                    $i++;

                    return $m[1].htmlspecialchars($url, ENT_QUOTES, 'UTF-8').$m[2];
                },
                $html
            ) ?? $html;
        }

        // Fallback template default
        if ($akad !== '') {
            $safe = htmlspecialchars($akad, ENT_QUOTES, 'UTF-8');
            $html = str_replace('href="https://maps.google.com"', 'href="'.$safe.'"', $html);
            $html = str_replace('href="https://maps.google.com/"', 'href="'.$safe.'"', $html);
        }

        // Wedding Islam / tautan google.com/maps (bukan class map-btn)
        if ($i === 0 && ($akad !== '' || $resepsi !== '')) {
            $html = preg_replace_callback(
                '/(<a\b[^>]*\bhref=")https?:\/\/(?:www\.)?google\.com\/maps[^"]*(")/i',
                function (array $m) use (&$i, $akad, $resepsi) {
                    $url = $i === 0 ? ($akad ?: $resepsi) : ($resepsi ?: $akad);
                    $i++;

                    return $m[1].htmlspecialchars($url, ENT_QUOTES, 'UTF-8').$m[2];
                },
                $html
            ) ?? $html;
        }

        return $html;
    }

    protected function replaceEventDetails(string $html, array $u): string
    {
        $tanggalAkad = $this->formatDateLong($u['tanggal_akad'] ?? null);
        $tanggalResepsi = $this->formatDateLong($u['tanggal_resepsi'] ?? null);
        $waktuAkad = $this->normalizeWaktu(
            $u['waktu_akad'] ?? null,
            $u['waktu_akad_mulai'] ?? null,
            $u['waktu_akad_selesai'] ?? null
        );
        $waktuResepsi = $this->normalizeWaktu(
            $u['waktu_resepsi'] ?? null,
            $u['waktu_resepsi_mulai'] ?? null,
            $u['waktu_resepsi_selesai'] ?? null
        );
        $tempatAkad = trim((string) ($u['tempat_akad'] ?? ''));
        $tempatResepsi = trim((string) ($u['tempat_resepsi'] ?? ''));
        $alamatAkad = trim((string) ($u['alamat_akad'] ?? ''));
        $alamatResepsi = trim((string) ($u['alamat_resepsi'] ?? ''));

        $html = $this->replaceEventCard(
            $html,
            'Akad Nikah',
            $tanggalAkad,
            $waktuAkad,
            $tempatAkad,
            $alamatAkad
        );
        $html = $this->replaceEventCard(
            $html,
            'Resepsi(?:\s+Pernikahan)?',
            $tanggalResepsi,
            $waktuResepsi,
            $tempatResepsi,
            $alamatResepsi
        );

        $mainDate = $u['tanggal_akad'] ?? $u['tanggal_resepsi'] ?? null;
        if (filled($mainDate)) {
            $hero = $this->formatDateHero($mainDate);
            $footer = $this->formatDateFooter($mainDate);

            $html = preg_replace(
                '/(<p class="(?:hero-)?date[^"]*"[^>]*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($hero).'$3',
                $html,
                1
            ) ?? $html;

            // Wedding Islam cover: SABTU, 21 NOVEMBER 2026
            try {
                $coverUpper = mb_strtoupper(
                    \Illuminate\Support\Carbon::parse($mainDate)->locale('id')->translatedFormat('l, d F Y'),
                    'UTF-8'
                );
                $html = preg_replace(
                    '/(<p class="text-ivory\/60 text-sm mt-4 tracking-widest font-kufi">)([\s\S]*?)(<\/p>)/i',
                    '$1'.e($coverUpper).'$3',
                    $html,
                    1
                ) ?? $html;
            } catch (\Throwable $e) {
                // ignore
            }

            // Adat Jawa cover date: 22 · 11 · 2026
            $coverDot = $this->formatDateCoverDots($mainDate);
            if ($coverDot !== '') {
                $html = preg_replace(
                    '/(<p class="cover-date"[^>]*>)([\s\S]*?)(<\/p>)/i',
                    '$1'.e($coverDot).'$3',
                    $html,
                    1
                ) ?? $html;
            }

            $html = preg_replace(
                '/(<p class="footer-date"[^>]*>)([\s\S]*?)(<\/p>)/i',
                '$1'.e($footer).'$3',
                $html,
                1
            ) ?? $html;

            $iso = $this->countdownIso($mainDate, $u['waktu_akad_mulai'] ?? $u['waktu_resepsi_mulai'] ?? null, $waktuAkad ?: $waktuResepsi);
            $html = preg_replace(
                "/new Date\('20\d{2}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+07:00'\)/",
                "new Date('".$iso."')",
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function replaceEventCard(
        string $html,
        string $titlePattern,
        ?string $tanggal,
        ?string $waktu,
        string $tempat,
        string $alamat
    ): string {
        $h3 = '<h3[^>]*>\s*'.$titlePattern.'\s*<\/h3>';

        // Wedding Islam: dua <p class="text-emerald-900/70 …"> (waktu + tempat)
        if (preg_match('/'.$h3.'\s*<p class="text-emerald-900\/70/i', $html)) {
            if ($tanggal || $waktu) {
                $timeHtml = e((string) ($tanggal ?: ''));
                if ($waktu) {
                    $timeHtml .= ($timeHtml !== '' ? '<br>' : '').e($waktu);
                }
                $html = preg_replace(
                    '/('.$h3.'\s*<p class="text-emerald-900\/70[^"]*"[^>]*>)([\s\S]*?)(<\/p>)/i',
                    '$1'.$timeHtml.'$3',
                    $html,
                    1
                ) ?? $html;
            }
            if ($tempat !== '' || $alamat !== '') {
                $venue = e($tempat !== '' ? $tempat : $alamat);
                if ($tempat !== '' && $alamat !== '') {
                    $venue = e($tempat).'<br>'.e($alamat);
                }
                $html = preg_replace(
                    '/('.$h3.'\s*<p class="text-emerald-900\/70[^"]*"[^>]*>[\s\S]*?<\/p>\s*<p class="text-emerald-900\/70[^"]*"[^>]*>)([\s\S]*?)(<\/p>)/i',
                    '$1'.$venue.'$3',
                    $html,
                    1
                ) ?? $html;
            }

            return $html;
        }

        if ($tanggal || $waktu) {
            $timeLine = trim(($tanggal ? $tanggal.' · ' : '').($waktu ?: ''));
            $html = preg_replace(
                '/('.$h3.'\s*<p class="time">)([\s\S]*?)(<\/p>)/i',
                '$1'.e($timeLine).'$3',
                $html,
                1
            ) ?? $html;
        }

        if ($tempat === '' && $alamat === '') {
            return $html;
        }

        $hasAddr = (bool) preg_match('/'.$h3.'[\s\S]*?<p class="addr">/i', $html);

        if ($hasAddr) {
            if ($tempat !== '') {
                $html = preg_replace(
                    '/('.$h3.'[\s\S]*?<p class="venue">)([\s\S]*?)(<\/p>)/i',
                    '$1'.e($tempat).'$3',
                    $html,
                    1
                ) ?? $html;
            }
            if ($alamat !== '') {
                $html = preg_replace(
                    '/('.$h3.'[\s\S]*?<p class="addr">)([\s\S]*?)(<\/p>)/i',
                    '$1'.e($alamat).'$3',
                    $html,
                    1
                ) ?? $html;
            }
        } else {
            $venue = e($tempat !== '' ? $tempat : $alamat);
            if ($tempat !== '' && $alamat !== '') {
                $venue = e($tempat).'<br>'.e($alamat);
            }
            $html = preg_replace(
                '/('.$h3.'[\s\S]*?<p class="venue">)([\s\S]*?)(<\/p>)/i',
                '$1'.$venue.'$3',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function formatDateLong(?string $date): ?string
    {
        if (! filled($date)) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');
        } catch (\Throwable $e) {
            return $date;
        }
    }

    protected function formatDateHero(?string $date): string
    {
        try {
            return mb_strtoupper(
                \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat('l · d F Y'),
                'UTF-8'
            );
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    protected function formatDateFooter(?string $date): string
    {
        try {
            return \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat('l · d F Y');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    protected function formatDateCoverDots(?string $date): string
    {
        try {
            return \Illuminate\Support\Carbon::parse($date)->format('d · m · Y');
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function normalizeWaktu(?string $combined, ?string $mulai = null, ?string $selesai = null): ?string
    {
        if (filled($mulai) || filled($selesai)) {
            $fmt = function (?string $time): ?string {
                if (! filled($time)) {
                    return null;
                }
                [$h, $m] = array_pad(explode(':', $time), 2, '00');

                return sprintf('%02d.%02d', (int) $h, (int) $m);
            };
            $a = $fmt($mulai);
            $b = $fmt($selesai);
            if ($a && $b) {
                return $a.' – '.$b.' WIB';
            }
            if ($a || $b) {
                return ($a ?: $b).' WIB';
            }
        }

        if (! filled($combined)) {
            return null;
        }

        $combined = trim((string) $combined);
        if (stripos($combined, 'WIB') !== false && str_contains($combined, '.')) {
            return $combined;
        }

        if (preg_match('/(\d{1,2})[:.](\d{2})\s*[-–—]\s*(\d{1,2})[:.](\d{2})/', $combined, $m)) {
            return sprintf('%02d.%02d – %02d.%02d WIB', $m[1], $m[2], $m[3], $m[4]);
        }

        if (preg_match('/(\d{1,2})[:.](\d{2})/', $combined, $m)) {
            return sprintf('%02d.%02d WIB', $m[1], $m[2]);
        }

        return $combined;
    }

    protected function countdownIso(string $date, ?string $mulai, ?string $waktuLabel): string
    {
        $hour = 8;
        $minute = 0;

        if (filled($mulai) && preg_match('/^(\d{1,2}):(\d{2})/', $mulai, $m)) {
            $hour = (int) $m[1];
            $minute = (int) $m[2];
        } elseif (filled($waktuLabel) && preg_match('/(\d{1,2})[.:](\d{2})/', $waktuLabel, $m)) {
            $hour = (int) $m[1];
            $minute = (int) $m[2];
        }

        try {
            return \Illuminate\Support\Carbon::parse($date, 'Asia/Jakarta')
                ->setTime($hour, $minute, 0)
                ->format('Y-m-d\TH:i:sP');
        } catch (\Throwable $e) {
            return sprintf('%sT%02d:%02d:00+07:00', $date, $hour, $minute);
        }
    }

    protected function replaceStory(string $html, array $u): string
    {
        $cerita = array_values(array_filter(
            $u['cerita'] ?? [],
            fn ($c) => filled($c['tahun'] ?? null) || filled($c['judul'] ?? null) || filled($c['deskripsi'] ?? null)
        ));

        if (count($cerita) === 0) {
            return $html;
        }

        // Elegant (template1): .timeline > .tl-item
        if (str_contains($html, 'class="timeline')) {
            $items = '';
            foreach ($cerita as $c) {
                $tahun = trim((string) ($c['tahun'] ?? ''));
                $judul = trim((string) ($c['judul'] ?? ''));
                $desc = trim((string) ($c['deskripsi'] ?? ''));
                $title = $tahun !== '' && $judul !== ''
                    ? e($tahun).' — '.e($judul)
                    : e($tahun !== '' ? $tahun : $judul);
                $items .= '<div class="tl-item"><h4>'.$title.'</h4><p>'.e($desc).'</p></div>';
            }

            $html = preg_replace(
                '/<div class="timeline[^"]*"[^>]*>[\s\S]*?<\/div>\s*(?=<div class="gallery-head)/i',
                '<div class="timeline reveal-up">'.$items.'</div>'."\n            ",
                $html,
                1
            ) ?? $html;
        }

        // Classic (template2): .story-journey articles
        if (str_contains($html, 'class="story-journey')) {
            $icons = ['&#10022;', '&#9825;', '&#10047;', '&#10038;', '&#10026;'];
            $photos = [
                'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=400',
                'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400',
                'https://images.unsplash.com/photo-1519741497674-611481863552?w=400',
                'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400',
                'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400',
            ];
            $steps = '';
            foreach ($cerita as $i => $c) {
                $tahun = e(trim((string) ($c['tahun'] ?? '')));
                $judul = e(trim((string) ($c['judul'] ?? '')));
                $desc = e(trim((string) ($c['deskripsi'] ?? '')));
                $side = ($i % 2 === 0) ? 'journey-step--right' : 'journey-step--left';
                $num = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                $icon = $icons[$i % count($icons)];
                $photo = $photos[$i % count($photos)];
                $steps .= <<<HTML
<article class="journey-step {$side}">
    <div class="journey-node" aria-hidden="true"><span>{$num}</span></div>
    <div class="journey-card">
        <span class="journey-icon" aria-hidden="true">{$icon}</span>
        <img class="journey-photo" src="{$photo}" alt="" loading="lazy">
        <div class="journey-body">
            <span class="journey-year-bg">{$tahun}</span>
            <span class="journey-year">{$tahun}</span>
            <h3>{$judul}</h3>
            <p>{$desc}</p>
        </div>
    </div>
</article>
HTML;
            }

            $html = preg_replace(
                '/(<div class="story-journey[^"]*"[^>]*>\s*<div class="journey-rail"[^>]*>[\s\S]*?<\/div>)([\s\S]*?)(<\/div>\s*<\/section>\s*<div class="section-bridge)/i',
                '$1'.$steps.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Langit Malam (template3): .story-chapters
        if (str_contains($html, 'class="story-chapters"')) {
            $chapters = '';
            $last = count($cerita) - 1;
            foreach ($cerita as $i => $c) {
                $tahun = e(trim((string) ($c['tahun'] ?? '')));
                $judul = e(trim((string) ($c['judul'] ?? '')));
                $desc = e(trim((string) ($c['deskripsi'] ?? '')));
                $finale = $i === $last ? ' story-chapter--finale' : '';
                $chapters .= <<<HTML
<article class="story-chapter{$finale} reveal">
    <span class="chapter-year">{$tahun}</span>
    <h3>{$judul}</h3>
    <p>{$desc}</p>
</article>
HTML;
            }

            $html = preg_replace(
                '/(<div class="story-chapters"[^>]*>)([\s\S]*?)(<\/div>\s*<\/section>\s*<section id="gallery")/i',
                '$1'.$chapters.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Adat Jawa (template4): .story-rail
        if (str_contains($html, 'class="story-rail"')) {
            $chapters = '';
            foreach ($cerita as $c) {
                $tahun = e(trim((string) ($c['tahun'] ?? '')));
                $judul = e(trim((string) ($c['judul'] ?? '')));
                $desc = e(trim((string) ($c['deskripsi'] ?? '')));
                $chapters .= '<article class="story-chapter reveal">'
                    .'<span class="chapter-year">'.$tahun.'</span>'
                    .'<h3>'.$judul.'</h3>'
                    .'<p>'.$desc.'</p>'
                    .'</article>';
            }

            $html = preg_replace(
                '/(<div class="story-rail"[^>]*>)([\s\S]*?)(<\/div>\s*<\/div>\s*<\/section>)/i',
                '$1'.$chapters.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Wedding Islam: .story-list
        if (str_contains($html, 'class="story-list"')) {
            $items = '';
            foreach ($cerita as $c) {
                $tahun = e(trim((string) ($c['tahun'] ?? '')));
                $judul = e(trim((string) ($c['judul'] ?? '')));
                $desc = e(trim((string) ($c['deskripsi'] ?? '')));
                $items .= '<article class="story-item" data-reveal>'
                    .'<span class="story-dot" aria-hidden="true"></span>'
                    .'<p class="story-year">'.$tahun.'</p>'
                    .'<h3 class="story-title">'.$judul.'</h3>'
                    .'<p class="story-body">'.$desc.'</p>'
                    .'</article>';
            }

            $html = preg_replace(
                '/(<div class="story-list"[^>]*>)([\s\S]*?)(<\/div>\s*<\/section>)/i',
                '$1'.$items.'$3',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function replaceGallery(string $html, array $u): string
    {
        $photos = array_values(array_filter($u['galeri'] ?? []));
        if (count($photos) === 0) {
            return $html;
        }

        // Hanya tampilkan foto yang file-nya benar-benar ada
        $urls = [];
        foreach ($photos as $p) {
            if (! $this->mediaFileExists($p)) {
                continue;
            }
            $url = $this->mediaUrl($p);
            if ($url) {
                $urls[] = $url;
            }
        }
        if (count($urls) === 0) {
            return $html;
        }
        $altCouple = trim(($u['nama_wanita'] ?? '').' & '.($u['nama_pria'] ?? ''));
        $altCouple = e($altCouple !== ' & ' ? $altCouple : 'Galeri');

        // Elegant (template1): formal + memories
        if (str_contains($html, 'class="gal-memories"')) {
            $formal = $urls[0];
            $html = preg_replace(
                '/(<figure class="gal-formal">\s*<img)(\s+[^>]*)(>)/i',
                '$1 src="'.e($formal).'" alt="'.$altCouple.'" onerror="window.__rmHideImg&&window.__rmHideImg(this)"$3',
                $html,
                1
            ) ?? $html;

            $memories = count($urls) > 1 ? array_slice($urls, 1) : $urls;
            $items = '';
            foreach ($memories as $url) {
                $items .= '<figure class="gal-item"><img src="'.e($url).'" alt="" onerror="window.__rmHideImg&&window.__rmHideImg(this)" loading="lazy"></figure>';
            }

            $html = preg_replace(
                '/(<div class="gal-memories">)([\s\S]*?)(<\/div>\s*<\/div>\s*<\/div>\s*<\/section>\s*<section id="gift")/i',
                '$1'.$items.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Classic (template2): gallery-wrap buttons
        if (str_contains($html, 'class="gallery-wrap"') && str_contains($html, 'data-gal=')) {
            $classes = ['gal-item gal-wide', 'gal-item', 'gal-item gal-tall', 'gal-item', 'gal-item', 'gal-item'];
            $buttons = '';
            foreach ($urls as $i => $url) {
                $cls = $classes[$i % count($classes)];
                $n = $i + 1;
                $buttons .= '<button type="button" class="'.$cls.'" data-gal="'.$i.'" aria-label="Foto '.$n.'">'
                    .'<img src="'.e($url).'" alt="Foto '.$n.'" loading="lazy">'
                    .'</button>';
            }

            $html = preg_replace(
                '/(<div class="gallery-wrap">)([\s\S]*?)(<\/div>\s*<\/div>\s*<\/section>\s*<section id="gift")/i',
                '$1'.$buttons.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Langit Malam (template3): gallery-masonry
        if (str_contains($html, 'class="gallery-masonry"')) {
            $zoom = '<span class="gal-zoom" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10" cy="10" r="6"/><path d="M14.5 14.5L20 20"/><path d="M10 7v6M7 10h6"/></svg></span>';
            $buttons = '';
            $last = count($urls) - 1;
            foreach ($urls as $i => $url) {
                $extra = $i === 0 ? ' gal-feature' : ($i === $last && $last > 0 ? ' gal-wide' : '');
                $n = $i + 1;
                $buttons .= '<button type="button" class="gal-item'.$extra.'" data-gal="'.$i.'" aria-label="Lihat foto '.$n.'">'
                    .'<img src="'.e($url).'" alt="">'
                    .$zoom
                    .'</button>';
            }

            $html = preg_replace(
                '/(<div class="gallery-masonry">)([\s\S]*?)(<\/div>\s*<\/div>\s*<\/section>\s*<section id="gift")/i',
                '$1'.$buttons.'$3',
                $html,
                1
            ) ?? $html;

            // Sync lightbox JS array
            $jsItems = [];
            foreach ($urls as $i => $url) {
                $jsItems[] = '{ src: '.json_encode($url).', alt: "Foto '.($i + 1).'" }';
            }
            $html = preg_replace(
                '/const galleryItems = \[[\s\S]*?\];/',
                'const galleryItems = ['.implode(',', $jsItems).'];',
                $html,
                1
            ) ?? $html;
        }

        // Adat Jawa (template4): .gallery-grid
        if (str_contains($html, 'class="gallery-grid"')) {
            $items = '';
            foreach ($urls as $i => $url) {
                $items .= '<button type="button" class="gal-item" data-gal="'.$i.'" aria-label="Foto '.($i + 1).'">'
                    .'<img src="'.e($url).'" alt="" loading="lazy">'
                    .'</button>';
            }
            $html = preg_replace(
                '/(<div class="gallery-grid"[^>]*>)([\s\S]*?)(<\/div>\s*<\/section>)/i',
                '$1'.$items.'$3',
                $html,
                1
            ) ?? $html;
        }

        // Wedding Islam: const GALLERY=[...] → #galleryGrid
        if (preg_match('/const\s+GALLERY\s*=\s*\[/', $html)) {
            $jsItems = [];
            foreach ($urls as $i => $url) {
                $cap = 'Foto '.($i + 1);
                $extra = ($i === 4 && count($urls) > 5) ? ',wide:1' : '';
                $jsItems[] = '{src:'.json_encode($url, JSON_UNESCAPED_SLASHES).',cap:'.json_encode($cap).$extra.'}';
            }
            $html = preg_replace(
                '/const\s+GALLERY\s*=\s*\[[\s\S]*?\];/',
                'const GALLERY=['.implode(',', $jsItems).'];',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    protected function replaceBanks(string $html, array $u): string
    {
        $rekening = array_values(array_filter(
            $u['rekening'] ?? [],
            fn ($r) => filled($r['bank'] ?? null) || filled($r['nomor'] ?? null)
        ));
        $ewallet = array_values(array_filter(
            $u['ewallet'] ?? [],
            fn ($e) => filled($e['tipe'] ?? null) || filled($e['nomor'] ?? null)
        ));

        // Wedding Islam: kartu di #kado (bukan #bankList)
        if (str_contains($html, 'id="kado"') && ! preg_match('/\bid=["\']bankList["\']/', $html)) {
            return $this->replaceIslamGiftCards($html, $rekening, $ewallet);
        }

        $cards = '';
        foreach ($rekening as $i => $r) {
            $name = trim((string) ($r['bank'] ?? ''));
            if ($name === '') {
                $name = 'Rekening';
            } elseif (! preg_match('/^bank\b/i', $name)) {
                $name = 'Bank '.$name;
            }

            $atas = trim((string) ($r['atas_nama'] ?? ''));
            $nomor = trim((string) ($r['nomor'] ?? ''));
            $id = 'rek'.($i + 1);

            $cards .= '<div class="bank-card">'
                .'<p class="bank-name">'.e($name).'</p>'
                .($atas !== '' ? '<p class="acc-name">'.e($atas).'</p>' : '')
                .'<div class="acc-row">'
                .'<span class="acc-num" id="'.$id.'">'.e($nomor).'</span>'
                .'<button type="button" class="copy-btn" data-target="'.$id.'">Salin</button>'
                .'</div></div>';
        }

        // Ganti isi #bankList saja; JANGAN telan </section> penutup #gift
        // (lookahead lama (?=<section) membuat #ucapan+footer jadi anak #gift → layout hancur)
        $replaced = preg_replace(
            '/(<div[^>]*\bid=["\']bankList["\'][^>]*>)[\s\S]*?(?=<\/section>\s*<section\b)/i',
            '$1'.$cards.'</div>'."\n        ",
            $html,
            1
        );

        if ($replaced === null || $replaced === $html) {
            // Fallback: template tanpa section berikutnya — ganti sampai penutup bankList ber-depth
            $replaced = $this->replaceBankListByDepth($html, $cards);
        }

        return $replaced;
    }

    /**
     * Kartu amplop digital Wedding Islam (#kado).
     */
    protected function replaceIslamGiftCards(string $html, array $rekening, array $ewallet): string
    {
        if (count($rekening) === 0 && count($ewallet) === 0) {
            return $html;
        }

        $cards = '';
        $sides = ['left', 'right'];
        $n = 0;

        foreach ($rekening as $r) {
            $name = trim((string) ($r['bank'] ?? ''));
            if ($name === '') {
                $name = 'Rekening';
            } elseif (! preg_match('/^bank\b/i', $name)) {
                $name = 'Bank '.$name;
            }
            $atas = trim((string) ($r['atas_nama'] ?? ''));
            $nomor = trim((string) ($r['nomor'] ?? ''));
            $digits = preg_replace('/\D+/', '', $nomor) ?: $nomor;
            $side = $sides[$n % 2];
            $n++;

            $cards .= '<div class="event-card rounded-2xl p-6" data-reveal data-from="'.$side.'">'
                .'<p class="font-kufi text-xs tracking-widest text-emerald-800 mb-2">'.e(mb_strtoupper($name)).'</p>'
                .'<p class="font-display text-2xl text-emerald-900 mb-1">'.e($nomor).'</p>'
                .($atas !== ''
                    ? '<p class="text-sm text-emerald-900/70 mb-4">a.n '.e($atas).'</p>'
                    : '<p class="mb-4"></p>')
                .'<button type="button" class="copy-btn btn-line text-xs font-kufi tracking-widest px-4 py-2 rounded" data-c="'.e($digits).'">SALIN NOMOR REKENING</button>'
                .'</div>';
        }

        foreach ($ewallet as $e) {
            $tipe = trim((string) ($e['tipe'] ?? ''));
            if ($tipe === '') {
                $tipe = 'E-Wallet';
            }
            $atas = trim((string) ($e['atas_nama'] ?? ''));
            $nomor = trim((string) ($e['nomor'] ?? ''));
            $digits = preg_replace('/\D+/', '', $nomor) ?: $nomor;
            $side = $sides[$n % 2];
            $n++;

            $cards .= '<div class="event-card rounded-2xl p-6" data-reveal data-from="'.$side.'">'
                .'<p class="font-kufi text-xs tracking-widest text-emerald-800 mb-2">'.e(mb_strtoupper($tipe)).'</p>'
                .'<p class="font-display text-2xl text-emerald-900 mb-1">'.e($nomor).'</p>'
                .($atas !== ''
                    ? '<p class="text-sm text-emerald-900/70 mb-4">a.n '.e($atas).'</p>'
                    : '<p class="mb-4"></p>')
                .'<button type="button" class="copy-btn btn-line text-xs font-kufi tracking-widest px-4 py-2 rounded" data-c="'.e($digits).'">SALIN NOMOR E-WALLET</button>'
                .'</div>';
        }

        $replaced = preg_replace(
            '/(<div class="relative z-10 max-w-3xl mx-auto grid[^"]*"[^>]*>)([\s\S]*?)(<\/div>\s*<\/section>\s*<section\b[^>]*\bid=["\']rsvp["\'])/i',
            '$1'.$cards.'$3',
            $html,
            1
        );

        return $replaced ?? $html;
    }

    /**
     * Ganti konten #bankList dengan menghitung kedalaman </div> (aman untuk nested .bank-card).
     */
    protected function replaceBankListByDepth(string $html, string $cards): string
    {
        if (! preg_match('/<div[^>]*\bid=["\']bankList["\'][^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
            return $html;
        }

        $open = $m[0][0];
        $start = $m[0][1];
        $pos = $start + strlen($open);
        $depth = 1;
        $len = strlen($html);

        while ($pos < $len && $depth > 0) {
            $nextOpen = stripos($html, '<div', $pos);
            $nextClose = stripos($html, '</div>', $pos);
            if ($nextClose === false) {
                return $html;
            }
            if ($nextOpen !== false && $nextOpen < $nextClose) {
                $depth++;
                $pos = $nextOpen + 4;
            } else {
                $depth--;
                $pos = $nextClose + 6;
            }
        }

        return substr($html, 0, $start).$open.$cards.'</div>'.substr($html, $pos);
    }

    /**
     * Isi data admin ke 2 template ulang tahun (Candyland + Pesta Bintang).
     */
    protected function injectUltahData(string $html, string $tema, array $u): string
    {
        if (! in_array($tema, ['ultah_candyland', 'ultah_bintang'], true)) {
            return $html;
        }

        $nama = trim((string) (($u['nama_anak'] ?? '') ?: ($u['nama_wanita'] ?? '')));
        if ($nama === '') {
            $nama = $tema === 'ultah_bintang' ? 'Keiko' : 'Kirana';
        }

        $usiaRaw = trim((string) ($u['usia'] ?? ($u['nama_pria'] ?? '')));
        $usiaNum = (int) preg_replace('/\D+/', '', $usiaRaw);
        if ($usiaNum <= 0) {
            $usiaNum = $tema === 'ultah_bintang' ? 7 : 5;
        }

        $demoName = $tema === 'ultah_bintang' ? 'Keiko' : 'Kirana';
        $demoAge = $tema === 'ultah_bintang' ? 7 : 5;

        // Ganti nama demo (urutan: panjang dulu)
        $html = str_replace(
            [$demoName, mb_strtoupper($demoName)],
            [$nama, mb_strtoupper($nama)],
            $html
        );

        // Usia: ke-5 / ke-7, "5 tahun", "udahan 5 tahun"
        $html = preg_replace('/\bke-'.$demoAge.'\b/iu', 'ke-'.$usiaNum, $html) ?? $html;
        $html = preg_replace('/\b'.$demoAge.'\s*tahun\b/iu', $usiaNum.' tahun', $html) ?? $html;
        $html = preg_replace('/\budah\s+'.$demoAge.'\s+tahun\b/iu', 'udah '.$usiaNum.' tahun', $html) ?? $html;

        $tanggal = $u['tanggal_acara'] ?? $u['tanggal_akad'] ?? $u['tanggal_spesial'] ?? null;
        $waktu = trim((string) ($u['waktu_acara'] ?? $u['waktu_akad'] ?? ''));
        $tempat = trim((string) (($u['tempat_acara'] ?? '') ?: ($u['tempat_akad'] ?? '')));
        $alamat = trim((string) (($u['alamat_acara'] ?? '') ?: ($u['alamat_akad'] ?? '')));
        $lokasi = trim($tempat.($alamat !== '' && $alamat !== $tempat ? ', '.$alamat : ''));
        $maps = trim((string) ($u['maps_url'] ?? ''));
        $kutipan = trim((string) ($u['kutipan'] ?? ''));
        $dress = trim((string) ($u['dress_code'] ?? ''));

        $ayah = trim((string) ($u['ayah_host'] ?? ($u['ayah_pria'] ?? '')));
        $ibu = trim((string) ($u['ibu_host'] ?? ($u['ibu_pria'] ?? '')));
        $ortu = '';
        if ($ayah !== '' || $ibu !== '') {
            $ortu = trim(
                ($ayah !== '' ? 'Bapak '.$ayah : '').
                ($ayah !== '' && $ibu !== '' ? ' & ' : '').
                ($ibu !== '' ? 'Ibu '.$ibu : '')
            );
        }

        $fotoAnak = null;
        foreach (['foto_anak', 'foto_wanita', 'foto_pria'] as $key) {
            if (! empty($u[$key]) && $this->mediaFileExists($u[$key])) {
                $fotoAnak = $this->mediaUrl($u[$key]);
                break;
            }
        }

        $galeriUrls = [];
        foreach ($u['galeri'] ?? [] as $g) {
            if ($this->mediaFileExists($g) && ($url = $this->mediaUrl($g))) {
                $galeriUrls[] = $url;
            }
        }

        // Tanggal & countdown
        if (filled($tanggal)) {
            try {
                $dt = \Carbon\Carbon::parse($tanggal)->locale('id');
                $dateLabel = $dt->translatedFormat('l, j F Y');
                $timeStart = '10:00';
                if (preg_match('/(\d{1,2}[:.]\d{2})/', $waktu, $m)) {
                    $timeStart = str_replace('.', ':', $m[1]);
                }
                if (strlen($timeStart) === 4) {
                    $timeStart = '0'.$timeStart;
                }
                $isoLocal = $dt->format('Y-m-d').'T'.$timeStart.':00+07:00';

                if ($tema === 'ultah_candyland') {
                    $html = preg_replace(
                        '/(<h3 class="font-display font-bold text-xl mb-1">Hari &amp; Tanggal<\/h3>\s*<p>)[^<]+(<\/p>)/iu',
                        '$1'.e($dateLabel).'$2',
                        $html,
                        1
                    ) ?? $html;
                    if ($waktu !== '') {
                        $html = preg_replace(
                            '/(<h3 class="font-display font-bold text-xl mb-1">Waktu<\/h3>\s*<p>)[^<]+(<\/p>)/iu',
                            '$1'.e($waktu).'$2',
                            $html,
                            1
                        ) ?? $html;
                    }
                    if ($lokasi !== '') {
                        $locHtml = e($tempat !== '' ? $tempat : $lokasi);
                        if ($alamat !== '' && $alamat !== $tempat) {
                            $locHtml .= '<br>'.e($alamat);
                        }
                        $html = preg_replace(
                            '/(<h3 class="font-display font-bold text-xl mb-1">Lokasi<\/h3>\s*<p>)[\s\S]*?(<\/p>)/iu',
                            '$1'.$locHtml.'$2',
                            $html,
                            1
                        ) ?? $html;
                    }
                    $html = preg_replace(
                        "/const target = new Date\('2026-08-09T10:00:00\+07:00'\)\.getTime\(\);/",
                        "const target = new Date('".$isoLocal."').getTime();",
                        $html,
                        1
                    ) ?? $html;
                }

                if ($tema === 'ultah_bintang') {
                    $html = preg_replace(
                        '/(Menuju Hari Spesialnya[\s\S]*?<h3[^>]*>)[\s\S]*?(<\/h3>)/iu',
                        '$1<i class="fa-solid fa-calendar-days mr-1"></i> '.e($dateLabel).'$2',
                        $html,
                        1
                    ) ?? $html;
                    $html = preg_replace(
                        '/(<p class="font-display font-600 text-lg">)Sabtu, 15 Agustus 2026(<\/p>)/u',
                        '$1'.e($dateLabel).'$2',
                        $html,
                        1
                    ) ?? $html;
                    $html = preg_replace(
                        "/const target = new Date\('2026-08-15T10:00:00\+07:00'\)\.getTime\(\);/",
                        "const target = new Date('".$isoLocal."').getTime();",
                        $html,
                        1
                    ) ?? $html;
                }
            } catch (\Throwable) {
                // ignore bad date
            }
        }

        if ($maps !== '') {
            $html = preg_replace(
                '/(<a\b[^>]*href=")https?:\/\/maps\.google\.com[^"]*(")/i',
                '$1'.e($maps).'$2',
                $html
            ) ?? $html;
            $html = preg_replace(
                '/(<a\b[^>]*href=")https?:\/\/(?:www\.)?google\.com\/maps[^"]*(")/i',
                '$1'.e($maps).'$2',
                $html
            ) ?? $html;
        }

        if ($ortu !== '') {
            $html = preg_replace(
                '/Dengan penuh cinta,<br>Bapak Andi &amp; Ibu Sinta Prananta/u',
                'Dengan penuh cinta,<br>'.e($ortu),
                $html,
                1
            ) ?? $html;
        }

        if ($dress !== '' && $tema === 'ultah_candyland') {
            $html = preg_replace(
                '/(Boleh banget! Kirana senang kalau teman-temannya datang dengan)[^<]+/u',
                'Boleh banget! '.e($nama).' senang kalau teman-temannya datang dengan '.e($dress).'.',
                $html,
                1
            ) ?? $html;
            // nama already replaced above so pattern might be nama not Kirana
            $html = preg_replace(
                '/(Boleh banget! '.preg_quote($nama, '/').' senang kalau teman-temannya datang dengan)[^<]+/u',
                'Boleh banget! '.e($nama).' senang kalau teman-temannya datang dengan '.e($dress).'.',
                $html,
                1
            ) ?? $html;
        }

        if ($kutipan !== '' && $tema === 'ultah_bintang') {
            $html = preg_replace(
                '/(<p class="font-script text-2xl text-\[var\(--pink-deeper\)\] leading-relaxed">)[\s\S]*?(<\/p>)/u',
                '$1"'.e($kutipan).'"$2',
                $html,
                1
            ) ?? $html;
        }

        // Foto anak (hero)
        if ($fotoAnak) {
            if ($tema === 'ultah_candyland') {
                $html = preg_replace(
                    '/(<img src=")assets\/images\/photo-1503454537195-1dcabb73ffb9_4759712\.jpg(")/i',
                    '$1'.e($fotoAnak).'$2',
                    $html,
                    1
                ) ?? $html;
            }
            if ($tema === 'ultah_bintang') {
                $html = preg_replace(
                    '/(<img src=")assets\/images\/photo-1717205964281-ab2bd111dcb2_8989027\.jpg(")/i',
                    '$1'.e($fotoAnak).'$2',
                    $html,
                    1
                ) ?? $html;
            }
        }

        // Galeri Candyland (HTML grid)
        if ($tema === 'ultah_candyland' && $galeriUrls !== []) {
            $items = '';
            foreach ($galeriUrls as $i => $url) {
                $items .= '<div class="gallery-item reveal" data-i="'.$i.'"><div class="shot"><img src="'.e($url).'" alt="" loading="lazy"></div><p class="cap">Momen '.($i + 1).'</p></div>';
            }
            $html = preg_replace(
                '/(<div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-3 gap-5 md:gap-7" id="gallery">)[\s\S]*?(<\/div>\s*<p class="text-center text-sm)/i',
                '$1'.$items.'$2',
                $html,
                1
            ) ?? $html;
        }

        // Galeri + timeline Bintang (JS arrays)
        if ($tema === 'ultah_bintang') {
            if ($galeriUrls !== []) {
                $film = [];
                foreach ($galeriUrls as $i => $url) {
                    $film[] = ['src' => $url, 'cap' => 'Momen '.($i + 1)];
                }
                $jsonFilm = json_encode($film, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $html = preg_replace(
                    '/const filmPhotos = \[[\s\S]*?\];/',
                    'const filmPhotos = '.$jsonFilm.';',
                    $html,
                    1
                ) ?? $html;
                $html = preg_replace(
                    '/const gridPhotos = \[[\s\S]*?\];/',
                    'const gridPhotos = '.$jsonFilm.';',
                    $html,
                    1
                ) ?? $html;
            }

            $cerita = array_values(array_filter($u['cerita'] ?? [], fn ($c) => filled($c['tahun'] ?? null) || filled($c['judul'] ?? null) || filled($c['deskripsi'] ?? null)));
            if ($cerita !== []) {
                $timeline = [];
                foreach ($cerita as $c) {
                    $age = (int) preg_replace('/\D+/', '', (string) ($c['tahun'] ?? ''));
                    $text = trim((string) (($c['deskripsi'] ?? '') ?: ($c['judul'] ?? '')));
                    if ($text === '') {
                        continue;
                    }
                    $timeline[] = [
                        'age' => $age > 0 ? $age : count($timeline) + 1,
                        'text' => $text,
                        'icon' => '✨',
                    ];
                }
                if ($timeline !== []) {
                    $jsonTl = json_encode($timeline, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $html = preg_replace(
                        '/const timelineData = \[[\s\S]*?\];/',
                        'const timelineData = '.$jsonTl.';',
                        $html,
                        1
                    ) ?? $html;
                }
            }
        }

        // Susunan acara Candyland
        if ($tema === 'ultah_candyland') {
            $jadwal = array_values(array_filter($u['jadwal'] ?? [], fn ($j) => filled($j['jam'] ?? null) || filled($j['judul'] ?? null)));
            if ($jadwal !== []) {
                $icons = ['fa-door-open', 'fa-gamepad', 'fa-cake-candles', 'fa-utensils', 'fa-gift', 'fa-heart'];
                $items = '';
                foreach ($jadwal as $i => $j) {
                    $icon = $icons[$i % count($icons)];
                    $title = trim(($j['jam'] ?? '').' — '.($j['judul'] ?? ''));
                    $desc = trim((string) ($j['deskripsi'] ?? ''));
                    $items .= '<div class="timeline-item"><div class="timeline-dot"><i class="fa-solid '.$icon.'"></i></div>'
                        .'<p class="font-bold font-display">'.e($title).'</p>'
                        .($desc !== '' ? '<p class="text-sm opacity-80">'.e($desc).'</p>' : '')
                        .'</div>';
                }
                $html = preg_replace(
                    '/(<div class="timeline">)[\s\S]*?(<\/div>\s*<\/div>\s*<!-- cake -->)/i',
                    '$1'.$items.'</div></div><!-- cake -->',
                    $html,
                    1
                ) ?? $html;
            }
        }

        $title = e($nama.' Ulang Tahun ke-'.$usiaNum.'!');
        $html = preg_replace('/<title>.*?<\/title>/is', '<title>'.$title.'</title>', $html, 1) ?? $html;

        return $html;
    }

    /**
     * Path musik custom dari admin (disimpan di kolom youtube_url).
     * Null = pakai default template.
     */
    protected function resolveMusicUrl(array $u): ?string
    {
        $raw = trim((string) ($u['youtube_url'] ?? ''));
        if ($raw === '' || preg_match('#^https?://#i', $raw)) {
            return null;
        }

        if (! $this->mediaFileExists($raw)) {
            return null;
        }

        return $this->mediaUrl($raw);
    }

    /**
     * Ganti sumber <audio> template dengan MP3 upload (kalau ada).
     */
    protected function replaceMusic(string $html, array $u): string
    {
        $url = $this->resolveMusicUrl($u);
        if ($url === null) {
            return $html;
        }

        $safe = e($url);

        // <source src="assets/audio/....mp3">
        $html = preg_replace(
            '/(<source\b[^>]*\bsrc=")[^"]*assets\/audio\/[^"]+(\.mp3[^"]*)(")/i',
            '$1'.$safe.'$3',
            $html
        ) ?? $html;

        // <audio ... src="assets/audio/....mp3">
        $html = preg_replace(
            '/(<audio\b[^>]*\bsrc=")[^"]*assets\/audio\/[^"]+(\.mp3[^"]*)(")/i',
            '$1'.$safe.'$3',
            $html
        ) ?? $html;

        // CONFIG.musikUrl (couple) — absolute atau relatif
        $html = preg_replace(
            '/("musikUrl"\s*:\s*")(?:[^"\\\\]|\\\\.)*(")/',
            '$1'.str_replace(['\\', '"'], ['\\\\', '\\"'], $url).'$2',
            $html,
            1
        ) ?? $html;

        return $html;
    }

    /**
     * Isi CONFIG JS di template Couple (Surat Spesial) dari data admin.
     */
    protected function injectCoupleConfig(string $html, string $tema, array $u): string
    {
        if ($tema !== 'couple_surat' || ! str_contains($html, 'const CONFIG = {')) {
            return $html;
        }

        $namaCewek = trim((string) ($u['nama_wanita'] ?? '')) ?: 'Sayangku';
        $namaPengirim = trim((string) ($u['nama_pria'] ?? '')) ?: 'Aku';
        $quote = trim((string) ($u['kutipan'] ?? ''));
        if ($quote === '') {
            $quote = 'kamu adalah bab favoritku dalam cerita yang belum selesai kutulis.';
        }

        $tanggal = $u['tanggal_spesial'] ?? $u['tanggal_akad'] ?? null;
        $tanggalLahir = '07-25';
        if (filled($tanggal)) {
            try {
                $tanggalLahir = \Carbon\Carbon::parse($tanggal)->format('m-d');
            } catch (\Throwable) {
                // keep default
            }
        }

        $pesan = trim((string) ($u['pesan_janji'] ?? ''));
        $suratLines = [];
        if ($pesan !== '') {
            $suratLines = preg_split('/\R+/u', $pesan) ?: [];
            $suratLines = array_values(array_filter(array_map('trim', $suratLines)));
        }
        if ($suratLines === []) {
            $suratLines = [$quote];
        }

        $alasan = array_values(array_filter(array_map(
            fn ($a) => trim((string) $a),
            $u['alasan_sayang'] ?? []
        )));
        if ($alasan === []) {
            $alasan = ['Karena kamu adalah kamu'];
        }

        $fotos = [];
        foreach (['foto_pria', 'foto_wanita'] as $key) {
            if (! empty($u[$key]) && $this->mediaFileExists($u[$key])) {
                $url = $this->mediaUrl($u[$key]);
                if ($url) {
                    $fotos[] = $url;
                }
            }
        }
        foreach ($u['galeri'] ?? [] as $g) {
            if (! $this->mediaFileExists($g)) {
                continue;
            }
            $url = $this->mediaUrl($g);
            if ($url) {
                $fotos[] = $url;
            }
        }
        $fotos = array_values(array_unique($fotos));
        if ($fotos === []) {
            // Fallback demo di folder template
            $fotos = [
                'assets/images/photo-1516589178581-6cd7833ae3b2_6731985.jpg',
                'assets/images/photo-1522673607200-164d1b6ce486_7916109.jpg',
                'assets/images/photo-1518199266791-5375a83190b7_4673335.jpg',
                'assets/images/photo-1519741497674-611481863552_1714527.jpg',
            ];
        }

        // Musik custom (MP3 upload) atau default template
        $musik = $this->resolveMusicUrl($u);
        if ($musik === null) {
            $musik = 'assets/audio/Donne-Maula-Bercinta-Lewat-Kata.mp3';
        }

        $janji = $pesan !== '' ? $pesan : 'Aku janji akan jadi rumah yang hangat buatmu.';

        $config = [
            'namaCewek' => $namaCewek,
            'namaPengirim' => $namaPengirim,
            'tanggalLahir' => $tanggalLahir,
            'quote' => $quote,
            'suratCinta' => $suratLines,
            'alasan' => $alasan,
            'foto' => $fotos,
            'musikUrl' => $musik,
            'janji' => $janji,
            'loveNotes' => [
                'Ciuman ini spesial buatmu',
                'Aku sayang kamu, lebih dari kemarin',
                'Semoga harimu selembut pelukan ini',
                'Kamu adalah favoritku di dunia ini',
            ],
        ];

        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return $html;
        }

        $block = "<script>\nconst CONFIG = {$json};\n</script>";

        $replaced = preg_replace(
            '/<!--\s*=+\s*CONFIG\s*=+\s*-->\s*<script>[\s\S]*?const CONFIG\s*=\s*\{[\s\S]*?\};\s*<\/script>/i',
            '<!-- =================== CONFIG =================== -->'."\n".$block,
            $html,
            1
        );

        if ($replaced === null || $replaced === $html) {
            $replaced = preg_replace(
                '/<script>\s*const CONFIG\s*=\s*\{[\s\S]*?\};\s*<\/script>/i',
                $block,
                $html,
                1
            ) ?? $html;
        }

        // Judul tab
        $title = e($namaCewek.' — Surat Spesial');
        $replaced = preg_replace('/<title>.*?<\/title>/is', '<title>'.$title.'</title>', $replaced, 1) ?? $replaced;

        return $replaced;
    }

    /**
     * Credit rayakanmomen.com — teks tipis di footer/closing yang sudah ada,
     * tanpa section/background baru.
     */
    protected function injectCopyright(string $html): string
    {
        if (str_contains($html, 'rm-copyright')) {
            return $html;
        }

        $year = date('Y');
        // Di <footer> yang sudah set color → ikut warna parent
        $creditFooter = $this->copyrightMarkup($year, 'inherit');
        // Di #closing gelap (islami/couple) body color = ink gelap → paksa ivory
        $creditClosing = $this->copyrightMarkup($year, 'rgba(250,246,236,.62)');

        $replaced = preg_replace('/<p[^>]*>\s*Dibuat dengan[\s\S]*?<\/p>/iu', $creditFooter, $html, 1, $count);
        if (is_string($replaced) && $count > 0) {
            return $replaced;
        }

        $replaced = preg_replace('/<p[^>]*class="[^"]*footer-credit[^"]*"[^>]*>[\s\S]*?<\/p>/iu', $creditFooter, $html, 1, $count);
        if (is_string($replaced) && $count > 0) {
            return $replaced;
        }

        if (stripos($html, '</footer>') !== false) {
            return (string) preg_replace('/<\/footer>/i', $creditFooter."\n</footer>", $html, 1);
        }

        if (preg_match('/id=["\']closing["\']/i', $html)) {
            $replaced = preg_replace(
                '/(<section[^>]*\bid=["\']closing["\'][^>]*>[\s\S]*?)(<\/section>)/i',
                '$1'.$creditClosing."\n$2",
                $html,
                1,
                $count
            );
            if (is_string($replaced) && $count > 0) {
                return $replaced;
            }
        }

        return $html;
    }

    protected function copyrightMarkup(int|string $year, string $color): string
    {
        return '<p class="rm-copyright relative" style="position:relative;z-index:5;display:block;margin:1.5rem auto 0;padding:0;background:transparent;border:0;box-shadow:none;text-align:center;font-family:Jost,system-ui,sans-serif;font-size:0.64rem;font-weight:400;letter-spacing:0.14em;text-transform:uppercase;line-height:1.5;opacity:1;color:'.$color.';">'
            .'&copy; '.$year.' &middot; '
            .'<a href="https://rayakanmomen.com" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;border-bottom:1px solid currentColor;padding-bottom:1px;">rayakanmomen.com</a>'
            .'</p>';
    }

    protected function injectBridge(string $html, array $u): string
    {
        $payload = [
            'slug' => $u['slug'] ?? '',
            'ucapanUrl' => url('/'.($u['slug'] ?? '').'/ucapan'),
            'csrf' => csrf_token(),
            'wishes' => collect($u['ucapan_tersimpan'] ?? [])->map(fn ($w) => [
                'nama' => preg_replace('/[<>\'"]+/u', '', (string) ($w['nama'] ?? '')) ?? '',
                'ucapan' => mb_substr(preg_replace('/[<>\'"]+/u', '', (string) ($w['ucapan'] ?? '')) ?? '', 0, 60),
                'kehadiran' => ($w['kehadiran'] ?? '') === 'hadir' ? 'Hadir' : 'Tidak Hadir',
            ])->values()->all(),
            'qris' => $this->mediaUrl($u['qris_image'] ?? null),
            'gallery' => array_values(array_filter(array_map(
                function ($p) {
                    if (! $this->mediaFileExists($p)) {
                        return null;
                    }

                    return $this->mediaUrl($p);
                },
                array_values(array_filter($u['galeri'] ?? []))
            ))),
            'ewallet' => $u['ewallet'] ?? [],
            'rekening' => $u['rekening'] ?? [],
            'mapsUrl' => $u['maps_url'] ?? null,
            'mapsUrlResepsi' => $u['maps_url_resepsi'] ?? null,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $bridge = asset('js/undangan-bridge.js');
        $bridge .= (str_contains($bridge, '?') ? '&' : '?').'v='.@filemtime(public_path('js/undangan-bridge.js'));

        // HARUS di <head> supaya siap sebelum <img> fire onerror
        $inlineHide = <<<'JS'
<script>
window.__rmHideImg=function(img){if(!img||img.dataset.rmHide==='1')return;img.dataset.rmHide='1';img.removeAttribute('alt');img.style.display='none';var p=img.closest('figure,button.gal-item,.gal-item,.arch-frame,.arch-photo,.mosaic-item');if(p)p.style.display='none';};
document.addEventListener('error',function(e){var t=e.target;if(t&&t.tagName==='IMG')window.__rmHideImg(t);},true);
</script>
JS;

        if (stripos($html, '</head>') !== false) {
            $html = str_ireplace('</head>', $inlineHide."\n</head>", $html);
        } else {
            $html = $inlineHide.$html;
        }

        $script = '<script>window.RAYAKAN_MOMEN = '.$json.'; window.WEB_UNTAL = window.RAYAKAN_MOMEN;</script>'."\n"
            .'<script src="'.e($bridge).'"></script>'."\n"
            .'<script>document.querySelectorAll("img").forEach(function(img){if(img.complete&&img.naturalWidth===0&&img.getAttribute("src"))window.__rmHideImg(img);});</script>';

        if (stripos($html, '</body>') !== false) {
            return str_ireplace('</body>', $script."\n</body>", $html);
        }

        return $html.$script;
    }

    /**
     * Pasang onerror hanya di foto upload/galeri — jangan sentuh deco template.
     */
    protected function attachImageErrorHandlers(string $html): string
    {
        return preg_replace_callback('/<img\b([^>]*?)>/i', function (array $m) {
            $attrs = $m[1];
            if (preg_match('/\bonerror\s*=/i', $attrs)) {
                return $m[0];
            }

            // Hanya foto undangan (uploads / galeri), biarkan asset template utuh
            $isUpload = preg_match('/src=(["\'])[^"\']*\/uploads\//i', $attrs)
                || preg_match('/src=(["\'])[^"\']*galeri-/i', $attrs)
                || preg_match('/src=(["\'])[^"\']*foto-(?:wanita|pria|anak)/i', $attrs);

            if (! $isUpload) {
                return $m[0];
            }

            if (! preg_match('/\balt\s*=/i', $attrs)) {
                $attrs .= ' alt=""';
            } else {
                $attrs = preg_replace('/\balt\s*=\s*(["\']).*?\1/i', 'alt=""', $attrs, 1) ?? $attrs;
            }

            return '<img'.$attrs.' onerror="window.__rmHideImg&&window.__rmHideImg(this)">';
        }, $html) ?? $html;
    }
}
