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
            'couple_surat' => 'template_undangan/template couple/index.html',
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

        $image = null;
        foreach (['foto_wanita', 'foto_pria', 'foto_anak', 'cover_image'] as $key) {
            if (! empty($u[$key])) {
                $image = $this->mediaUrl($u[$key]);
                break;
            }
        }
        if (! $image && ! empty($u['galeri'][0])) {
            $image = $this->mediaUrl($u['galeri'][0]);
        }

        return compact('title', 'desc', 'url', 'image', 'name');
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

        foreach ($pairs as [$from, $to]) {
            if ($from !== '' && $to !== '') {
                $html = str_replace($from, e($to), $html);
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
        }

        return $html;
    }

    protected function replaceBanks(string $html, array $u): string
    {
        $rekening = array_values(array_filter(
            $u['rekening'] ?? [],
            fn ($r) => filled($r['bank'] ?? null) || filled($r['nomor'] ?? null)
        ));

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

        // YouTube tidak bisa di <audio>; pakai file lokal template kalau ada
        $musik = '';
        $localMusik = public_path('templates/couple_surat/assets/audio/Donne-Maula-Bercinta-Lewat-Kata.mp3');
        if (is_file($localMusik)) {
            $musik = asset('templates/couple_surat/assets/audio/Donne-Maula-Bercinta-Lewat-Kata.mp3');
        } elseif (filled($u['youtube_url'] ?? null) && preg_match('#\.(mp3|m4a|ogg)(\?|$)#i', (string) $u['youtube_url'])) {
            $musik = (string) $u['youtube_url'];
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
     * Footer credit Rayakan Momen.
     */
    protected function injectCopyright(string $html): string
    {
        $year = date('Y');
        $credit = '<p class="rm-copyright" style="margin-top:20px;opacity:0.55;font-family:Jost,sans-serif;font-size:0.68rem;letter-spacing:1px;">'
            .'Copyright &copy; '.$year
            .' <a href="https://rayakanmomen.com" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline;">rayakanmomen.com</a>'
            .'</p>';

        // Ganti teks demo "Dibuat dengan ❤ — 2026"
        $html = preg_replace(
            '/<p[^>]*>\s*Dibuat dengan[\s\S]*?<\/p>/iu',
            $credit,
            $html,
            1
        ) ?? $html;

        // Kalau belum ada, sisipkan sebelum penutup footer
        if (! str_contains($html, 'rm-copyright') && stripos($html, '</footer>') !== false) {
            $html = str_ireplace('</footer>', $credit."\n</footer>", $html);
        }

        return $html;
    }

    protected function injectBridge(string $html, array $u): string
    {
        $payload = [
            'slug' => $u['slug'] ?? '',
            'ucapanUrl' => url('/'.($u['slug'] ?? '').'/ucapan'),
            'csrf' => csrf_token(),
            'wishes' => collect($u['ucapan_tersimpan'] ?? [])->map(fn ($w) => [
                'nama' => $w['nama'] ?? '',
                'ucapan' => $w['ucapan'] ?? '',
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
