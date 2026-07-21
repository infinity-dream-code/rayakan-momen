<?php

namespace App\Http\Controllers;

use App\Repositories\InvitationRepository;
use App\Services\RsvpDashboardCipher;

class RsvpDashboardController extends Controller
{
    public function __construct(
        protected InvitationRepository $invitations,
        protected RsvpDashboardCipher $cipher
    ) {
    }

    public function show(string $token)
    {
        $slug = $this->cipher->decryptSlug($token);
        abort_if(! $slug, 404);

        $undangan = $this->invitations->findBySlug($slug);
        abort_if(! $undangan, 404);

        $ucapan = $undangan['ucapan_tersimpan'] ?? [];
        $hadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'hadir'));
        $tidakHadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'tidak_hadir'));
        $total = count($ucapan);

        $title = $this->displayTitle($undangan);

        return view('rsvp-dashboard', [
            'undangan' => $undangan,
            'ucapan' => $ucapan,
            'hadir' => $hadir,
            'tidakHadir' => $tidakHadir,
            'total' => $total,
            'title' => $title,
            'shareUrl' => $this->cipher->urlForSlug($slug),
        ]);
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
