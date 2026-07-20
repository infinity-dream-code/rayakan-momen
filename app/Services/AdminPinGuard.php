<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * PIN 6 digit admin — hanya di PHP server (bukan DB, bukan Blade/JS).
 * Salah 5x → IP diblokir 1 jam (file lokal, bukan tabel DB).
 */
class AdminPinGuard
{
    public const MAX_ATTEMPTS = 5;

    /** Durasi blokir IP (detik) */
    public const BLOCK_SECONDS = 3600;

    /**
     * bcrypt hash PIN 6 digit (password_verify).
     * Ganti PIN: php -r "echo password_hash('PINBARU', PASSWORD_BCRYPT);"
     * lalu paste hasilnya ke sini. Jangan pernah kirim PIN ke view/JS.
     */
    private const PIN_HASH = '$2y$10$7i5OThGRS9Z0KMblDxum.Owknr90PC2ErYm8VX.Bb8rsC9AU0g.xa';

    public function clientIp(Request $request): string
    {
        $ip = (string) $request->ip();

        return $ip !== '' ? $ip : '0.0.0.0';
    }

    public function isBlocked(Request $request): bool
    {
        $ip = $this->clientIp($request);
        $data = $this->purgeExpired($this->readStore());

        if (empty($data['blocked'][$ip])) {
            return false;
        }

        $until = strtotime((string) ($data['blocked'][$ip]['until'] ?? ''));
        if ($until === false || $until <= time()) {
            unset($data['blocked'][$ip]);
            $this->writeStore($data);

            return false;
        }

        return true;
    }

    public function attemptsLeft(Request $request): int
    {
        $ip = $this->clientIp($request);
        $data = $this->readStore();
        $fails = (int) ($data['attempts'][$ip] ?? 0);

        return max(0, self::MAX_ATTEMPTS - $fails);
    }

    public function verify(string $pin, Request $request): bool
    {
        if ($this->isBlocked($request)) {
            return false;
        }

        $pin = preg_replace('/\D+/', '', $pin) ?? '';
        if (strlen($pin) !== 6 || ! password_verify($pin, self::PIN_HASH)) {
            $this->recordFailure($request);

            return false;
        }

        $this->clearAttempts($request);

        return true;
    }

    protected function recordFailure(Request $request): void
    {
        $ip = $this->clientIp($request);
        $data = $this->readStore();
        $fails = (int) ($data['attempts'][$ip] ?? 0) + 1;
        $data['attempts'][$ip] = $fails;

        if ($fails >= self::MAX_ATTEMPTS) {
            $data['blocked'][$ip] = [
                'at' => now()->toDateTimeString(),
                'until' => now()->addSeconds(self::BLOCK_SECONDS)->toDateTimeString(),
                'fails' => $fails,
            ];
            unset($data['attempts'][$ip]);
        }

        $this->writeStore($data);
    }

    /**
     * @param  array{attempts: array<string, int>, blocked: array<string, array>}  $data
     * @return array{attempts: array<string, int>, blocked: array<string, array>}
     */
    protected function purgeExpired(array $data): array
    {
        $now = time();
        $changed = false;
        foreach ($data['blocked'] as $ip => $row) {
            $until = strtotime((string) ($row['until'] ?? $row['at'] ?? ''));
            // Blokir lama tanpa "until" dianggap sudah lewat (migrasi dari blok permanen)
            if ($until === false || $until <= $now || empty($row['until'])) {
                unset($data['blocked'][$ip]);
                $changed = true;
            }
        }
        if ($changed) {
            $this->writeStore($data);
        }

        return $data;
    }

    protected function clearAttempts(Request $request): void
    {
        $ip = $this->clientIp($request);
        $data = $this->readStore();
        unset($data['attempts'][$ip]);
        $this->writeStore($data);
    }

    protected function storePath(): string
    {
        return storage_path('app/security/admin_ip_blocks.json');
    }

    /**
     * @return array{attempts: array<string, int>, blocked: array<string, array>}
     */
    protected function readStore(): array
    {
        $path = $this->storePath();
        if (! is_file($path)) {
            return ['attempts' => [], 'blocked' => []];
        }

        $raw = @file_get_contents($path);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (! is_array($decoded)) {
            return ['attempts' => [], 'blocked' => []];
        }

        return [
            'attempts' => is_array($decoded['attempts'] ?? null) ? $decoded['attempts'] : [],
            'blocked' => is_array($decoded['blocked'] ?? null) ? $decoded['blocked'] : [],
        ];
    }

    /**
     * @param  array{attempts: array<string, int>, blocked: array<string, array>}  $data
     */
    protected function writeStore(array $data): void
    {
        $dir = dirname($this->storePath());
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        File::put($this->storePath(), $json === false ? '{"attempts":{},"blocked":{}}' : $json);
    }
}
