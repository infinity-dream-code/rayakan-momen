<?php

namespace App\Http\Controllers;

use App\Repositories\InvitationRepository;
use App\Services\InvitationTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InvitationPublicController extends Controller
{
    public function __construct(
        protected InvitationRepository $invitations,
        protected InvitationTemplateRenderer $renderer
    ) {
    }

    public function show(string $slug)
    {
        // Slug sering diketik pakai spasi di address bar → normalisasi
        $slug = Str::slug(Str::lower(rawurldecode($slug)), '-');

        // Auto: lewat 90 hari → status jadi expired (tanpa tunggu cron)
        $this->invitations->expireIfDueBySlug($slug);

        $undangan = $this->invitations->findPublicBySlug($slug);

        if (! $undangan) {
            // Distinguish expired/nonaktif (still in DB) vs missing
            $any = $this->invitations->findBySlug($slug);
            if ($any) {
                return response()
                    ->view('undangan.expired', ['undangan' => $any], 410);
            }
            abort(404);
        }

        $this->invitations->incrementViews($slug);

        $ttl = (int) config('undangan.client_cache_seconds', 300);
        $cacheKey = config('undangan.cache_key_prefix', 'undangan:html:').$slug;

        $html = Cache::remember($cacheKey, $ttl, function () use ($slug) {
            $fresh = $this->invitations->findPublicBySlug($slug);
            if (! $fresh) {
                return '';
            }

            return $this->renderer->render($fresh);
        });

        if ($html === '') {
            abort(404);
        }

        // Token CSRF selalu fresh (HTML bisa dari cache)
        $token = csrf_token();
        $html = preg_replace('/"csrf"\s*:\s*"[^"]*"/', '"csrf":"'.$token.'"', $html) ?? $html;

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'private, no-cache, must-revalidate');
    }

    public function storeUcapan(Request $request, string $slug)
    {
        $undangan = $this->invitations->findPublicBySlug($slug);
        abort_if(! $undangan, 404);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'regex:/^[^<>\'"]+$/u'],
            'ucapan' => ['required', 'string', 'max:60', 'regex:/^[^<>\'"]+$/u'],
            'kehadiran' => 'required|in:hadir,tidak_hadir',
        ], [
            'nama.regex' => 'Nama tidak boleh berisi karakter < > \' "',
            'ucapan.max' => 'Ucapan maksimal 60 karakter.',
            'ucapan.regex' => 'Ucapan tidak boleh berisi karakter < > \' "',
        ]);

        // Extra sanitize (prepared statement sudah aman; ini cegah XSS di tampilan)
        $data['nama'] = preg_replace('/[<>\'"]+/u', '', trim($data['nama'])) ?? '';
        $data['ucapan'] = preg_replace('/[<>\'"]+/u', '', trim($data['ucapan'])) ?? '';

        $this->invitations->addUcapan($slug, $data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Ucapan tersimpan.']);
        }

        return back()->with('success', 'Terima kasih! Ucapanmu sudah tersimpan.');
    }

    public function sitemap()
    {
        $rows = $this->invitations->slugsForSitemap();

        $xml = view('seo.sitemap', [
            'landing' => url('/'),
            'items' => $rows,
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $body = "User-agent: *\nAllow: /\nDisallow: /panel\nDisallow: /admin\nDisallow: /SmartLoginAdmin\nDisallow: /dashboard-rsvp\nSitemap: ".url('/sitemap.xml')."\n";

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
