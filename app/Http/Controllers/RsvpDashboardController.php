<?php

namespace App\Http\Controllers;

use App\Repositories\InvitationRepository;
use App\Services\RsvpDashboardCipher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RsvpDashboardController extends Controller
{
    public const TAMU_MAX = 50;

    public const TAMU_PER_PAGE = 10;

    public function __construct(
        protected InvitationRepository $invitations,
        protected RsvpDashboardCipher $cipher
    ) {
    }

    public function show(string $token)
    {
        $undangan = $this->resolveUndangan($token);
        $ucapan = $undangan['ucapan_tersimpan'] ?? [];
        $hadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'hadir'));
        $tidakHadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'tidak_hadir'));
        $total = count($ucapan);
        $tamuTotal = count(array_filter(
            is_array($undangan['tamu_links'] ?? null) ? $undangan['tamu_links'] : [],
            fn ($g) => is_array($g) && filled($g['nama'] ?? null)
        ));

        return view('rsvp-dashboard', [
            'token' => $token,
            'undangan' => $undangan,
            'ucapan' => $ucapan,
            'hadir' => $hadir,
            'tidakHadir' => $tidakHadir,
            'total' => $total,
            'title' => $this->displayTitle($undangan),
            'tamuTotal' => $tamuTotal,
            'tamuMax' => self::TAMU_MAX,
            'baseInviteUrl' => url('/'.$undangan['slug']),
        ]);
    }

    public function bagikan(Request $request, string $token)
    {
        $undangan = $this->resolveUndangan($token);
        $allTamu = array_values(array_reverse(array_filter(
            is_array($undangan['tamu_links'] ?? null) ? $undangan['tamu_links'] : [],
            fn ($g) => is_array($g) && filled($g['nama'] ?? null)
        )));

        $tamuTotal = count($allTamu);
        $page = max(1, (int) $request->query('page', 1));
        $perPage = self::TAMU_PER_PAGE;
        $lastPage = max(1, (int) ceil($tamuTotal / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $tamuLinks = new LengthAwarePaginator(
            array_slice($allTamu, ($page - 1) * $perPage, $perPage),
            $tamuTotal,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('rsvp-bagikan', [
            'token' => $token,
            'undangan' => $undangan,
            'title' => $this->displayTitle($undangan),
            'tamuLinks' => $tamuLinks,
            'tamuTotal' => $tamuTotal,
            'tamuMax' => self::TAMU_MAX,
            'tamuFull' => $tamuTotal >= self::TAMU_MAX,
            'baseInviteUrl' => url('/'.$undangan['slug']),
        ]);
    }

    public function storeTamu(Request $request, string $token)
    {
        $undangan = $this->resolveUndangan($token);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
        ], [
            'nama.required' => 'Nama tamu wajib diisi.',
            'nama.max' => 'Nama maksimal 80 karakter.',
        ]);

        $result = $this->invitations->addTamuLink((string) $undangan['id'], $validated['nama']);

        if (! ($result['ok'] ?? false)) {
            return redirect()
                ->route('rsvp.bagikan', ['token' => $token])
                ->withInput()
                ->with('tamu_error', $result['error'] ?? 'Gagal menambah nama.');
        }

        $nama = (string) ($result['item']['nama'] ?? $validated['nama']);
        $link = $this->guestInviteUrl((string) $undangan['slug'], $nama);

        return redirect()
            ->route('rsvp.bagikan', ['token' => $token])
            ->with([
                'tamu_success' => 'Link untuk '.$nama.' siap dibagikan.',
                'tamu_last_link' => $link,
                'tamu_last_nama' => $nama,
            ]);
    }

    public function destroyTamu(Request $request, string $token, string $tamuId)
    {
        $undangan = $this->resolveUndangan($token);
        $this->invitations->removeTamuLink((string) $undangan['id'], $tamuId);

        $page = max(1, (int) $request->query('page', 1));

        return redirect()
            ->route('rsvp.bagikan', array_filter([
                'token' => $token,
                'page' => $page > 1 ? $page : null,
            ]))
            ->with('tamu_success', 'Nama dihapus dari daftar.');
    }

    protected function resolveUndangan(string $token): array
    {
        $slug = $this->cipher->decryptSlug($token);
        abort_if(! $slug, 404);

        $undangan = $this->invitations->findBySlug($slug);
        abort_if(! $undangan, 404);

        return $undangan;
    }

    public function guestInviteUrl(string $slug, string $nama): string
    {
        return url('/'.$slug).'?to='.rawurlencode($nama);
    }

    protected function displayTitle(array $u): string
    {
        $kat = $u['kategori'] ?? 'wedding';
        if ($kat === 'ultah_anak') {
            return trim((string) ($u['nama_anak'] ?? $u['nama_wanita'] ?? 'Undangan'));
        }
        if ($kat === 'couple') {
            $a = trim((string) ($u['nama_wanita'] ?? ''));
            $b = trim((string) ($u['nama_pria'] ?? ''));

            return $a !== '' && $b !== '' ? $a.' & '.$b : ($a ?: $b ?: 'Undangan');
        }

        $w = trim((string) ($u['nama_wanita'] ?? ''));
        $p = trim((string) ($u['nama_pria'] ?? ''));

        return $w !== '' && $p !== '' ? $w.' & '.$p : ($w ?: $p ?: 'Undangan');
    }
}
