<?php

namespace App\Repositories;

use App\Services\FileUploadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationRepository
{
    public function __construct(protected FileUploadService $uploads)
    {
    }

    /** Columns stored on invitations table (not payload). */
    protected array $columns = [
        'id', 'slug', 'status', 'access_state', 'tema', 'kategori',
        'nama_pria', 'nama_wanita', 'nama_anak', 'ortu_pria', 'ortu_wanita',
        'tanggal_akad', 'waktu_akad', 'tempat_akad', 'alamat_akad',
        'tanggal_resepsi', 'waktu_resepsi', 'tempat_resepsi', 'alamat_resepsi',
        'maps_url', 'kutipan', 'kutipan_sumber', 'youtube_url',
        'foto_wanita', 'foto_pria', 'foto_anak', 'cover_image', 'qris_image',
        'views', 'expires_at', 'purge_at', 'created_at', 'updated_at',
    ];

    protected array $payloadKeys = [
        'galeri', 'jadwal', 'alasan_sayang', 'dress_code', 'usia',
        'ayah_host', 'ibu_host', 'ayah_pria', 'ibu_pria', 'ayah_wanita', 'ibu_wanita',
        'nama_lengkap_pria', 'nama_lengkap_wanita',
        'waktu_akad_mulai', 'waktu_akad_selesai',
        'waktu_resepsi_mulai', 'waktu_resepsi_selesai',
        'tanggal_spesial', 'tanggal_acara', 'waktu_acara', 'waktu_acara_mulai', 'waktu_acara_selesai',
        'tempat_acara', 'alamat_acara', 'pesan_janji',
    ];

    public function allForAdmin(): array
    {
        $rows = DB::select(
            'SELECT i.id, i.slug, i.status, i.access_state, i.tema, i.kategori,
                    i.nama_pria, i.nama_wanita, i.nama_anak, i.views,
                    i.expires_at, i.purge_at, i.updated_at, i.created_at,
                    (SELECT COUNT(*) FROM invitation_wishes w WHERE w.invitation_id = i.id) AS wish_count
             FROM invitations i
             ORDER BY i.updated_at DESC'
        );

        return array_map(function ($row) {
            $arr = (array) $row;
            $arr['ucapan_tersimpan'] = array_fill(0, (int) ($arr['wish_count'] ?? 0), []);
            unset($arr['wish_count']);

            return $arr;
        }, $rows);
    }

    public function recentForAdmin(int $limit = 5): array
    {
        $rows = DB::select(
            'SELECT id, slug, status, access_state, tema, kategori,
                    nama_pria, nama_wanita, nama_anak, views, updated_at, created_at
             FROM invitations
             ORDER BY updated_at DESC
             LIMIT ?',
            [$limit]
        );

        return array_map(fn ($r) => (array) $r, $rows);
    }

    public function find(string $id): ?array
    {
        $rows = DB::select('SELECT * FROM invitations WHERE id = ? LIMIT 1', [$id]);

        return $rows ? $this->hydrate($rows[0]) : null;
    }

    public function findBySlug(string $slug): ?array
    {
        $slug = Str::lower($slug);
        $rows = DB::select('SELECT * FROM invitations WHERE slug = ? LIMIT 1', [$slug]);

        return $rows ? $this->hydrate($rows[0]) : null;
    }

    /** Public-accessible only: aktif + live (+ not past expires_at). */
    public function findPublicBySlug(string $slug): ?array
    {
        $slug = Str::lower($slug);
        $rows = DB::select(
            'SELECT * FROM invitations
             WHERE slug = ?
               AND status = ?
               AND access_state = ?
               AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1',
            [$slug, 'aktif', 'live']
        );

        return $rows ? $this->hydrate($rows[0]) : null;
    }

    public function slugsForSitemap(): array
    {
        return DB::select(
            'SELECT slug, updated_at FROM invitations
             WHERE status = ? AND access_state = ?
               AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY updated_at DESC',
            ['aktif', 'live']
        );
    }

    public function slugExists(string $slug, ?string $ignoreId = null): bool
    {
        $slug = Str::lower($slug);
        if ($ignoreId) {
            $rows = DB::select(
                'SELECT id FROM invitations WHERE slug = ? AND id != ? LIMIT 1',
                [$slug, $ignoreId]
            );
        } else {
            $rows = DB::select('SELECT id FROM invitations WHERE slug = ? LIMIT 1', [$slug]);
        }

        return ! empty($rows);
    }

    public function create(array $data): array
    {
        $id = (string) Str::uuid();
        $now = now();
        $expireDays = (int) config('undangan.expire_days', 90);
        $purgeDays = (int) config('undangan.purge_days', 180);

        $data['id'] = $id;
        $data['slug'] = Str::lower($data['slug'] ?? '');
        $data['status'] = $data['status'] ?? 'aktif';
        $data['access_state'] = 'live';
        $data['views'] = (int) ($data['views'] ?? 0);
        $data['created_at'] = $now->toDateTimeString();
        $data['updated_at'] = $now->toDateTimeString();
        $data['expires_at'] = $now->copy()->addDays($expireDays)->toDateTimeString();
        $data['purge_at'] = $now->copy()->addDays($purgeDays)->toDateTimeString();

        [$row, $payload, $cerita, $rekening, $ewallet] = $this->splitData($data);

        DB::beginTransaction();
        try {
            DB::insert(
                'INSERT INTO invitations (
                    id, slug, status, access_state, tema, kategori,
                    nama_pria, nama_wanita, nama_anak, ortu_pria, ortu_wanita,
                    tanggal_akad, waktu_akad, tempat_akad, alamat_akad,
                    tanggal_resepsi, waktu_resepsi, tempat_resepsi, alamat_resepsi,
                    maps_url, kutipan, kutipan_sumber, youtube_url,
                    foto_wanita, foto_pria, foto_anak, cover_image, qris_image,
                    views, payload_json, expires_at, purge_at, created_at, updated_at
                ) VALUES (
                    ?,?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,
                    ?,?,?,?,
                    ?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,?
                )',
                [
                    $row['id'], $row['slug'], $row['status'], $row['access_state'], $row['tema'], $row['kategori'],
                    $row['nama_pria'], $row['nama_wanita'], $row['nama_anak'], $row['ortu_pria'], $row['ortu_wanita'],
                    $row['tanggal_akad'], $row['waktu_akad'], $row['tempat_akad'], $row['alamat_akad'],
                    $row['tanggal_resepsi'], $row['waktu_resepsi'], $row['tempat_resepsi'], $row['alamat_resepsi'],
                    $row['maps_url'], $row['kutipan'], $row['kutipan_sumber'], $row['youtube_url'],
                    $row['foto_wanita'], $row['foto_pria'], $row['foto_anak'], $row['cover_image'], $row['qris_image'],
                    $row['views'], json_encode($payload, JSON_UNESCAPED_UNICODE),
                    $row['expires_at'], $row['purge_at'], $row['created_at'], $row['updated_at'],
                ]
            );

            $this->syncChildren($id, $cerita, $rekening, $ewallet);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->find($id);
    }

    public function update(string $id, array $data): ?array
    {
        $existing = $this->find($id);
        if (! $existing) {
            return null;
        }

        $data['id'] = $id;
        $data['slug'] = Str::lower($data['slug'] ?? $existing['slug']);
        $data['views'] = (int) ($existing['views'] ?? 0);
        $data['access_state'] = $existing['access_state'] ?? 'live';
        $data['expires_at'] = $existing['expires_at'] ?? null;
        $data['purge_at'] = $existing['purge_at'] ?? null;
        $data['created_at'] = $existing['created_at'] ?? now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();
        // Keep wishes as-is (not in $data from form)

        [$row, $payload, $cerita, $rekening, $ewallet] = $this->splitData($data);

        DB::beginTransaction();
        try {
            DB::update(
                'UPDATE invitations SET
                    slug=?, status=?, access_state=?, tema=?, kategori=?,
                    nama_pria=?, nama_wanita=?, nama_anak=?, ortu_pria=?, ortu_wanita=?,
                    tanggal_akad=?, waktu_akad=?, tempat_akad=?, alamat_akad=?,
                    tanggal_resepsi=?, waktu_resepsi=?, tempat_resepsi=?, alamat_resepsi=?,
                    maps_url=?, kutipan=?, kutipan_sumber=?, youtube_url=?,
                    foto_wanita=?, foto_pria=?, foto_anak=?, cover_image=?, qris_image=?,
                    payload_json=?, updated_at=?
                 WHERE id=?',
                [
                    $row['slug'], $row['status'], $row['access_state'], $row['tema'], $row['kategori'],
                    $row['nama_pria'], $row['nama_wanita'], $row['nama_anak'], $row['ortu_pria'], $row['ortu_wanita'],
                    $row['tanggal_akad'], $row['waktu_akad'], $row['tempat_akad'], $row['alamat_akad'],
                    $row['tanggal_resepsi'], $row['waktu_resepsi'], $row['tempat_resepsi'], $row['alamat_resepsi'],
                    $row['maps_url'], $row['kutipan'], $row['kutipan_sumber'], $row['youtube_url'],
                    $row['foto_wanita'], $row['foto_pria'], $row['foto_anak'], $row['cover_image'], $row['qris_image'],
                    json_encode($payload, JSON_UNESCAPED_UNICODE), $row['updated_at'],
                    $id,
                ]
            );

            DB::delete('DELETE FROM invitation_stories WHERE invitation_id = ?', [$id]);
            DB::delete('DELETE FROM invitation_accounts WHERE invitation_id = ?', [$id]);
            DB::delete('DELETE FROM invitation_ewallets WHERE invitation_id = ?', [$id]);
            $this->syncChildren($id, $cerita, $rekening, $ewallet);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $this->forgetClientCache($row['slug']);
        if (($existing['slug'] ?? '') !== $row['slug']) {
            $this->forgetClientCache($existing['slug']);
        }

        return $this->find($id);
    }

    public function delete(string $id): bool
    {
        $existing = $this->find($id);
        if (! $existing) {
            return false;
        }

        $this->deleteMediaFiles($existing);
        DB::delete('DELETE FROM invitations WHERE id = ?', [$id]);
        $this->forgetClientCache($existing['slug'] ?? '');

        return true;
    }

    public function incrementViews(string $slug): void
    {
        DB::update(
            'UPDATE invitations SET views = views + 1 WHERE slug = ?',
            [Str::lower($slug)]
        );
    }

    public function addUcapan(string $slug, array $ucapan): ?array
    {
        $inv = $this->findPublicBySlug($slug);
        if (! $inv) {
            return null;
        }

        DB::insert(
            'INSERT INTO invitation_wishes (invitation_id, nama, ucapan, kehadiran, created_at)
             VALUES (?, ?, ?, ?, ?)',
            [
                $inv['id'],
                $ucapan['nama'],
                $ucapan['ucapan'],
                $ucapan['kehadiran'],
                now()->toDateTimeString(),
            ]
        );

        $this->forgetClientCache($slug);

        return $this->find($inv['id']);
    }

    public function markExpiredDue(): int
    {
        return DB::update(
            "UPDATE invitations
             SET access_state = 'expired', status = 'nonaktif', updated_at = ?
             WHERE access_state = 'live'
               AND expires_at IS NOT NULL
               AND expires_at <= NOW()",
            [now()->toDateTimeString()]
        );
    }

    /**
     * Auto-expire one invitation by slug if past expires_at (no cron needed).
     */
    public function expireIfDueBySlug(string $slug): bool
    {
        $slug = Str::lower($slug);
        $affected = DB::update(
            "UPDATE invitations
             SET access_state = 'expired', status = 'nonaktif', updated_at = ?
             WHERE slug = ?
               AND access_state = 'live'
               AND expires_at IS NOT NULL
               AND expires_at <= NOW()",
            [now()->toDateTimeString(), $slug]
        );

        if ($affected > 0) {
            $this->forgetClientCache($slug);

            return true;
        }

        return false;
    }

    /**
     * Also expire any due rows (lightweight) — call from admin list.
     */
    public function expireAllDue(): int
    {
        $rows = DB::select(
            "SELECT slug FROM invitations
             WHERE access_state = 'live'
               AND expires_at IS NOT NULL
               AND expires_at <= NOW()"
        );
        $count = $this->markExpiredDue();
        foreach ($rows as $row) {
            $this->forgetClientCache($row->slug ?? '');
        }

        return $count;
    }

    public function countPurgeEligible(): int
    {
        $rows = DB::select(
            "SELECT COUNT(*) AS c FROM invitations
             WHERE access_state = 'expired'
               AND purge_at IS NOT NULL
               AND purge_at <= NOW()"
        );

        return (int) ($rows[0]->c ?? 0);
    }

    public function deletePurgeEligible(): int
    {
        $rows = DB::select(
            "SELECT * FROM invitations
             WHERE access_state = 'expired'
               AND purge_at IS NOT NULL
               AND purge_at <= NOW()"
        );

        $count = 0;
        foreach ($rows as $row) {
            $data = $this->hydrate($row);
            $this->deleteMediaFiles($data);
            $this->forgetClientCache($data['slug'] ?? '');
            DB::delete('DELETE FROM invitations WHERE id = ?', [$data['id']]);
            $count++;
        }

        return $count;
    }

    public function stats(): array
    {
        $inv = DB::select(
            "SELECT
                COUNT(*) AS total_undangan,
                SUM(CASE WHEN status = 'aktif' AND access_state = 'live' THEN 1 ELSE 0 END) AS total_aktif,
                SUM(views) AS total_views
             FROM invitations"
        );
        $wishes = DB::select(
            "SELECT
                COUNT(*) AS total_ucapan,
                SUM(CASE WHEN kehadiran = 'hadir' THEN 1 ELSE 0 END) AS total_hadir
             FROM invitation_wishes"
        );

        return [
            'total_undangan' => (int) ($inv[0]->total_undangan ?? 0),
            'total_aktif' => (int) ($inv[0]->total_aktif ?? 0),
            'total_views' => (int) ($inv[0]->total_views ?? 0),
            'total_ucapan' => (int) ($wishes[0]->total_ucapan ?? 0),
            'total_hadir' => (int) ($wishes[0]->total_hadir ?? 0),
        ];
    }

    public function storeUpload($file, string $folder = 'covers'): ?string
    {
        return $this->uploads->storeUpload($file, $folder);
    }

    public function storeMultipleUploads(array $files, string $folder = 'galeri'): array
    {
        return $this->uploads->storeMultipleUploads($files, $folder);
    }

    public function forgetClientCache(string $slug): void
    {
        if ($slug === '') {
            return;
        }
        Cache::forget(config('undangan.cache_key_prefix', 'undangan:html:').Str::lower($slug));
    }

    protected function hydrate(object $row): array
    {
        $arr = (array) $row;
        $payload = [];
        if (! empty($arr['payload_json'])) {
            $decoded = is_string($arr['payload_json'])
                ? json_decode($arr['payload_json'], true)
                : (array) $arr['payload_json'];
            $payload = is_array($decoded) ? $decoded : [];
        }
        unset($arr['payload_json']);

        $id = $arr['id'];

        $stories = DB::select(
            'SELECT tahun, judul, deskripsi FROM invitation_stories
             WHERE invitation_id = ? ORDER BY sort_order ASC, id ASC',
            [$id]
        );
        $accounts = DB::select(
            'SELECT bank, nomor, atas_nama FROM invitation_accounts
             WHERE invitation_id = ? ORDER BY sort_order ASC, id ASC',
            [$id]
        );
        $ewallets = DB::select(
            'SELECT tipe, nomor, atas_nama FROM invitation_ewallets
             WHERE invitation_id = ? ORDER BY sort_order ASC, id ASC',
            [$id]
        );
        $wishes = DB::select(
            'SELECT nama, ucapan, kehadiran, created_at FROM invitation_wishes
             WHERE invitation_id = ? ORDER BY created_at DESC, id DESC',
            [$id]
        );

        $merged = array_merge($payload, $arr);
        $merged['cerita'] = array_map(fn ($r) => (array) $r, $stories);
        $merged['rekening'] = array_map(fn ($r) => (array) $r, $accounts);
        $merged['ewallet'] = array_map(fn ($r) => (array) $r, $ewallets);
        $merged['ucapan_tersimpan'] = array_map(fn ($r) => (array) $r, $wishes);
        $merged['galeri'] = $merged['galeri'] ?? [];
        $merged['jadwal'] = $merged['jadwal'] ?? [];
        $merged['alasan_sayang'] = $merged['alasan_sayang'] ?? [];

        return $merged;
    }

    protected function splitData(array $data): array
    {
        $row = [];
        foreach ($this->columns as $col) {
            if ($col === 'payload_json') {
                continue;
            }
            $row[$col] = $data[$col] ?? null;
        }
        $row['status'] = $row['status'] ?? 'aktif';
        $row['access_state'] = $row['access_state'] ?? 'live';
        $row['kategori'] = $row['kategori'] ?? 'wedding';
        $row['views'] = (int) ($row['views'] ?? 0);

        $payload = [];
        foreach ($this->payloadKeys as $key) {
            if (array_key_exists($key, $data)) {
                $payload[$key] = $data[$key];
            }
        }

        $cerita = is_array($data['cerita'] ?? null) ? $data['cerita'] : [];
        $rekening = is_array($data['rekening'] ?? null) ? $data['rekening'] : [];
        $ewallet = is_array($data['ewallet'] ?? null) ? $data['ewallet'] : [];

        return [$row, $payload, $cerita, $rekening, $ewallet];
    }

    protected function syncChildren(string $id, array $cerita, array $rekening, array $ewallet): void
    {
        foreach ($cerita as $i => $c) {
            DB::insert(
                'INSERT INTO invitation_stories (invitation_id, tahun, judul, deskripsi, sort_order)
                 VALUES (?, ?, ?, ?, ?)',
                [$id, $c['tahun'] ?? '', $c['judul'] ?? '', $c['deskripsi'] ?? '', $i]
            );
        }
        foreach ($rekening as $i => $r) {
            DB::insert(
                'INSERT INTO invitation_accounts (invitation_id, bank, nomor, atas_nama, sort_order)
                 VALUES (?, ?, ?, ?, ?)',
                [$id, $r['bank'] ?? '', $r['nomor'] ?? '', $r['atas_nama'] ?? '', $i]
            );
        }
        foreach ($ewallet as $i => $e) {
            DB::insert(
                'INSERT INTO invitation_ewallets (invitation_id, tipe, nomor, atas_nama, sort_order)
                 VALUES (?, ?, ?, ?, ?)',
                [$id, $e['tipe'] ?? '', $e['nomor'] ?? '', $e['atas_nama'] ?? '', $i]
            );
        }
    }

    protected function deleteMediaFiles(array $data): void
    {
        foreach (['foto_wanita', 'foto_pria', 'foto_anak', 'cover_image', 'qris_image'] as $key) {
            $this->uploads->deletePublicPath($data[$key] ?? null);
        }
        foreach ($data['galeri'] ?? [] as $g) {
            $this->uploads->deletePublicPath($g);
        }
    }
}
