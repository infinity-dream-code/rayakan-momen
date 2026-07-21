<?php

namespace App\Services;

class RsvpDashboardCipher
{
    /**
     * Enkripsi ringan slug → token URL-safe.
     * Key dari config (default: Rama Sat119).
     */
    public function encryptSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $payload = openssl_encrypt(
            $slug,
            'AES-128-CBC',
            $this->keyBytes(),
            OPENSSL_RAW_DATA,
            $this->iv()
        );

        if ($payload === false) {
            return '';
        }

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    public function decryptSlug(string $token): ?string
    {
        $token = trim($token);
        if ($token === '' || strlen($token) > 200) {
            return null;
        }

        $raw = base64_decode(strtr($token, '-_', '+/'), true);
        if ($raw === false || $raw === '') {
            return null;
        }

        $plain = openssl_decrypt(
            $raw,
            'AES-128-CBC',
            $this->keyBytes(),
            OPENSSL_RAW_DATA,
            $this->iv()
        );

        if ($plain === false) {
            return null;
        }

        $slug = strtolower(trim($plain));
        if ($slug === '' || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            return null;
        }

        return $slug;
    }

    public function urlForSlug(string $slug): string
    {
        $token = $this->encryptSlug($slug);

        return url('/dashboard-rsvp/'.$token);
    }

    protected function keyBytes(): string
    {
        $key = (string) config('undangan.rsvp_dashboard_key', 'Rama Sat119');

        return substr(hash('sha256', $key, true), 0, 16);
    }

    protected function iv(): string
    {
        // IV deterministik → link share stabil untuk slug yang sama
        return substr(hash('sha256', 'rsvp-iv:'.config('undangan.rsvp_dashboard_key', 'Rama Sat119'), true), 0, 16);
    }
}
