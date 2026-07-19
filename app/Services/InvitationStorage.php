<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InvitationStorage
{
    protected string $path;

    public function __construct()
    {
        $this->path = storage_path('app/invitations.json');
        $this->ensureFile();
    }

    protected function ensureFile(): void
    {
        if (! File::exists($this->path)) {
            File::put($this->path, json_encode($this->seedData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    protected function seedData(): array
    {
        return [
            [
                'id' => (string) Str::uuid(),
                'slug' => 'ica',
                'status' => 'aktif',
                'tema' => 'elegan',
                'nama_pria' => 'Rafi',
                'nama_wanita' => 'Ica',
                'nama_lengkap_pria' => 'Rafi Pratama, S.Kom.',
                'nama_lengkap_wanita' => 'Annisa Maharani, S.Psi.',
                'ortu_pria' => 'Bpk. Hendra Pratama & Ibu Sari Dewi',
                'ortu_wanita' => 'Bpk. Bambang Wijaya & Ibu Ratna Sari',
                'tanggal_akad' => '2026-09-12',
                'waktu_akad' => '08.00 – 10.00 WIB',
                'tempat_akad' => 'Kediaman Mempelai Wanita',
                'alamat_akad' => 'Jl. Melati No. 21, Jakarta Selatan',
                'tanggal_resepsi' => '2026-09-12',
                'waktu_resepsi' => '11.00 – 15.00 WIB',
                'tempat_resepsi' => 'Grand Ballroom Emerald',
                'alamat_resepsi' => 'Jl. Sudirman No. 88, Jakarta Pusat',
                'maps_url' => 'https://maps.google.com',
                'kutipan' => 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri.',
                'kutipan_sumber' => 'QS. Ar-Rum : 21',
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'foto_wanita' => null,
                'foto_pria' => null,
                'galeri' => [],
                'cerita' => [
                    ['tahun' => '2021', 'judul' => 'Pertemuan Pertama', 'deskripsi' => 'Kami bertemu di sebuah acara kampus dan mulai saling mengenal.'],
                    ['tahun' => '2023', 'judul' => 'Menjalin Hubungan', 'deskripsi' => 'Dari teman menjadi pasangan yang saling menguatkan.'],
                    ['tahun' => '2026', 'judul' => 'Hari Pernikahan', 'deskripsi' => 'Saatnya kami mengucap janji suci bersama.'],
                ],
                'rekening' => [
                    ['bank' => 'BCA', 'nomor' => '1234567890', 'atas_nama' => 'Annisa Maharani'],
                    ['bank' => 'Mandiri', 'nomor' => '9876543210', 'atas_nama' => 'Rafi Pratama'],
                ],
                'qris_image' => null,
                'ewallet' => [
                    ['tipe' => 'DANA', 'nomor' => '081234567890', 'atas_nama' => 'Annisa Maharani'],
                    ['tipe' => 'OVO', 'nomor' => '081298765432', 'atas_nama' => 'Rafi Pratama'],
                ],
                'ucapan_tersimpan' => [
                    ['nama' => 'Alya', 'ucapan' => 'Selamat menempuh hidup baru! Semoga bahagia selalu.', 'kehadiran' => 'hadir', 'created_at' => now()->subDays(2)->toDateTimeString()],
                    ['nama' => 'Budi', 'ucapan' => 'Doa terbaik untuk kalian berdua.', 'kehadiran' => 'hadir', 'created_at' => now()->subDay()->toDateTimeString()],
                ],
                'views' => 128,
                'created_at' => now()->subDays(10)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    public function all(): array
    {
        $data = json_decode(File::get($this->path), true);

        return is_array($data) ? $data : [];
    }

    public function saveAll(array $items): void
    {
        File::put($this->path, json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $item) {
            if (($item['id'] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }

    public function findBySlug(string $slug): ?array
    {
        $slug = Str::lower($slug);

        foreach ($this->all() as $item) {
            if (Str::lower($item['slug'] ?? '') === $slug) {
                return $item;
            }
        }

        return null;
    }

    public function create(array $data): array
    {
        $items = $this->all();
        $data['id'] = (string) Str::uuid();
        $data['created_at'] = now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();
        $data['views'] = $data['views'] ?? 0;
        $data['ucapan_tersimpan'] = $data['ucapan_tersimpan'] ?? [];
        $items[] = $data;
        $this->saveAll($items);

        return $data;
    }

    public function update(string $id, array $data): ?array
    {
        $items = $this->all();
        $updated = null;

        foreach ($items as $i => $item) {
            if (($item['id'] ?? '') === $id) {
                $data['id'] = $id;
                $data['created_at'] = $item['created_at'] ?? now()->toDateTimeString();
                $data['updated_at'] = now()->toDateTimeString();
                $data['views'] = $item['views'] ?? 0;
                $data['ucapan_tersimpan'] = $item['ucapan_tersimpan'] ?? [];
                $items[$i] = $data;
                $updated = $data;
                break;
            }
        }

        if ($updated) {
            $this->saveAll($items);
        }

        return $updated;
    }

    public function delete(string $id): bool
    {
        $items = $this->all();
        $filtered = array_values(array_filter($items, fn ($item) => ($item['id'] ?? '') !== $id));

        if (count($filtered) === count($items)) {
            return false;
        }

        $this->saveAll($filtered);

        return true;
    }

    public function incrementViews(string $slug): void
    {
        $items = $this->all();

        foreach ($items as $i => $item) {
            if (Str::lower($item['slug'] ?? '') === Str::lower($slug)) {
                $items[$i]['views'] = ($item['views'] ?? 0) + 1;
                $this->saveAll($items);
                break;
            }
        }
    }

    public function addUcapan(string $slug, array $ucapan): ?array
    {
        $items = $this->all();
        $updated = null;

        foreach ($items as $i => $item) {
            if (Str::lower($item['slug'] ?? '') === Str::lower($slug)) {
                $ucapan['created_at'] = now()->toDateTimeString();
                $items[$i]['ucapan_tersimpan'] = $items[$i]['ucapan_tersimpan'] ?? [];
                array_unshift($items[$i]['ucapan_tersimpan'], $ucapan);
                $updated = $items[$i];
                break;
            }
        }

        if ($updated) {
            $this->saveAll($items);
        }

        return $updated;
    }

    public function stats(): array
    {
        $items = $this->all();
        $totalUcapan = 0;
        $totalViews = 0;
        $totalHadir = 0;

        foreach ($items as $item) {
            $totalViews += (int) ($item['views'] ?? 0);
            foreach ($item['ucapan_tersimpan'] ?? [] as $ucapan) {
                $totalUcapan++;
                if (($ucapan['kehadiran'] ?? '') === 'hadir') {
                    $totalHadir++;
                }
            }
        }

        return [
            'total_undangan' => count($items),
            'total_aktif' => count(array_filter($items, fn ($i) => ($i['status'] ?? '') === 'aktif')),
            'total_views' => $totalViews,
            'total_ucapan' => $totalUcapan,
            'total_hadir' => $totalHadir,
        ];
    }

    public function storeUpload(?UploadedFile $file, string $folder = 'covers'): ?string
    {
        if (! $file) {
            return null;
        }

        $dir = public_path('uploads/'.$folder);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $name = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'uploads/'.$folder.'/'.$name;
    }

    public function storeMultipleUploads(array $files, string $folder = 'galeri'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storeUpload($file, $folder);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }
}
