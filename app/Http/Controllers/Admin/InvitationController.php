<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\InvitationRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationRepository $storage,
        protected CatalogRepository $catalog,
        protected CategoryRepository $categories,
        protected TransactionRepository $transactions
    ) {
    }

    public function index()
    {
        $undangan = $this->storage->allForAdmin();
        $purgeEligible = $this->storage->countPurgeEligible();

        return view('admin.undangan.index', compact('undangan', 'purgeEligible'));
    }

    public function create(Request $request)
    {
        $categories = $this->categories->all();
        $templates = collect($this->catalog->templates())
            ->filter(fn ($t) => ($t['aktif'] ?? false))
            ->all();

        return view('admin.undangan.pilih-template', compact('categories', 'templates'));
    }

    public function pilihTemplate(Request $request)
    {
        $aktifKeys = collect(config('templates.templates', []))
            ->filter(fn ($t) => ($t['aktif'] ?? false))
            ->keys()
            ->all();

        $request->validate([
            'tema' => ['required', Rule::in($aktifKeys)],
        ]);

        $request->session()->put('undangan_template', $request->tema);

        return redirect()->route('admin.undangan.form');
    }

    public function form(Request $request)
    {
        $tema = $request->session()->get('undangan_template');
        $info = config('templates.templates.'.$tema);

        if (! $tema || ! $info || ! ($info['aktif'] ?? false)) {
            return redirect()
                ->route('admin.undangan.create')
                ->with('error', 'Pilih template dulu sebelum mengisi data undangan.');
        }

        return view('admin.undangan.form', [
            'undangan' => null,
            'mode' => 'create',
            'tema' => $tema,
            'templateInfo' => $this->catalog->templates()[$tema] ?? $info,
            'categories' => $this->categories->all(),
            'allTemplates' => $this->catalog->templates(),
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->filled('tema')) {
            $temaSession = $request->session()->get('undangan_template');
            if ($temaSession) {
                $request->merge(['tema' => $temaSession]);
            }
        }

        $data = $this->validated($request);
        $data = $this->mapFormData($request, $data);
        $created = $this->storage->create($data);
        $this->recordSale($created);

        $request->session()->forget('undangan_template');

        return redirect()
            ->route('admin.undangan.index')
            ->with('success', 'Undangan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $undangan = $this->storage->find($id);
        abort_if(! $undangan, 404);

        return view('admin.undangan.form', [
            'undangan' => $undangan,
            'mode' => 'edit',
            'tema' => $undangan['tema'] ?? 'elegan',
            'templateInfo' => $this->catalog->templates()[$undangan['tema'] ?? 'elegan'] ?? config('templates.templates.'.($undangan['tema'] ?? 'elegan')),
            'categories' => $this->categories->all(),
            'allTemplates' => $this->catalog->templates(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $existing = $this->storage->find($id);
        abort_if(! $existing, 404);

        if (! $request->filled('tema')) {
            $request->merge(['tema' => $existing['tema'] ?? 'elegan']);
        }

        $data = $this->validated($request, $id);
        $data = $this->mapFormData($request, $data, $existing);
        $this->storage->update($id, $data);

        return redirect()->route('admin.undangan.index')->with('success', 'Undangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->storage->delete($id);

        return redirect()->route('admin.undangan.index')->with('success', 'Undangan berhasil dihapus.');
    }

    public function toggleStatus(string $id)
    {
        $updated = $this->storage->toggleStatus($id);
        abort_if(! $updated, 404);

        $label = ($updated['status'] ?? '') === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.undangan.index')
            ->with('success', 'Undangan berhasil '.$label.'.');
    }

    public function purgeExpired()
    {
        $count = $this->storage->deletePurgeEligible();

        return redirect()
            ->route('admin.undangan.index')
            ->with('success', $count > 0
                ? "Berhasil menghapus {$count} undangan expired (manual)."
                : 'Tidak ada undangan expired yang siap dihapus (≥180 hari).');
    }

    public function laporan(string $id)
    {
        $undangan = $this->storage->find($id);
        abort_if(! $undangan, 404);

        $ucapan = $undangan['ucapan_tersimpan'] ?? [];
        $hadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'hadir'));
        $tidakHadir = count(array_filter($ucapan, fn ($u) => ($u['kehadiran'] ?? '') === 'tidak_hadir'));
        $rsvpDashboardUrl = app(\App\Services\RsvpDashboardCipher::class)->urlForSlug((string) ($undangan['slug'] ?? ''));

        return view('admin.undangan.laporan', compact('undangan', 'ucapan', 'hadir', 'tidakHadir', 'rsvpDashboardUrl'));
    }

    protected function validated(Request $request, ?string $ignoreId = null): array
    {
        // Normalisasi slug dulu biar huruf besar / spasi tidak bikin error regex
        if ($request->filled('slug')) {
            $request->merge([
                'slug' => Str::slug((string) $request->input('slug'), '-'),
            ]);
        }

        $reserved = ['admin', 'panel', 'SmartLoginAdmin', 'login', 'logout', 'api', 'css', 'js', 'images', 'uploads', 'storage', 'sitemap.xml', 'robots.txt', 'dashboard-rsvp', 'katalog'];
        $tema = $request->input('tema');
        $meta = config('templates.templates.'.$tema, []);
        $kategori = $meta['kategori'] ?? 'wedding';
        $fields = $meta['fields'] ?? [];
        $has = fn (string $g) => in_array($g, $fields, true);
        $storage = $this->storage;

        $rules = [
            'slug' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                function ($attribute, $value, $fail) use ($storage, $reserved, $ignoreId) {
                    $value = Str::lower((string) $value);
                    if (in_array($value, $reserved, true)) {
                        $fail('Slug ini tidak boleh digunakan.');
                    }
                    if ($storage->slugExists($value, $ignoreId)) {
                        $fail('Slug sudah dipakai undangan lain.');
                    }
                },
            ],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'tema' => ['required', Rule::in(array_keys(config('templates.templates', [])))],
        ];

        // Optional shared
        $optional = [
            'kutipan' => 'nullable|string|max:800',
            'kutipan_sumber' => 'nullable|string|max:100',
            'youtube_url' => 'nullable|string|max:500',
            'music_mp3' => 'nullable|file|max:8192',
            'music_reset' => 'nullable|boolean',
            'maps_url' => 'nullable|string|max:1000',
            'maps_url_resepsi' => 'nullable|string|max:1000',
            'galeri.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'qris_image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'foto_wanita' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'foto_pria' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'foto_anak' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'foto_formal' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'pesan_janji' => 'nullable|string|max:800',
            'usia' => 'nullable|string|max:30',
            'ayah_host' => 'nullable|string|max:100',
            'ibu_host' => 'nullable|string|max:100',
            'dress_code' => 'nullable|string|max:200',
            'tanggal_spesial' => 'nullable|date',
            'tanggal_acara' => 'nullable|date',
            'waktu_acara_mulai' => 'nullable|date_format:H:i',
            'waktu_acara_selesai' => 'nullable|date_format:H:i',
            'tempat_acara' => 'nullable|string|max:200',
            'alamat_acara' => 'nullable|string|max:300',
            'nama_lengkap_pria' => 'nullable|string|max:150',
            'nama_lengkap_wanita' => 'nullable|string|max:150',
            'ayah_pria' => 'nullable|string|max:100',
            'ibu_pria' => 'nullable|string|max:100',
            'ayah_wanita' => 'nullable|string|max:100',
            'ibu_wanita' => 'nullable|string|max:100',
            'tanggal_akad' => 'nullable|date',
            'waktu_akad_mulai' => 'nullable|date_format:H:i',
            'waktu_akad_selesai' => 'nullable|date_format:H:i',
            'tempat_akad' => 'nullable|string|max:200',
            'alamat_akad' => 'nullable|string|max:300',
            'tanggal_resepsi' => 'nullable|date',
            'waktu_resepsi_mulai' => 'nullable|date_format:H:i',
            'waktu_resepsi_selesai' => 'nullable|date_format:H:i',
            'tempat_resepsi' => 'nullable|string|max:200',
            'alamat_resepsi' => 'nullable|string|max:300',
            'rekening_bank' => 'nullable|array',
            'rekening_bank.*' => 'nullable|string|max:50',
            'rekening_nomor' => 'nullable|array',
            'rekening_nomor.*' => 'nullable|string|max:50',
            'rekening_nama' => 'nullable|array',
            'rekening_nama.*' => 'nullable|string|max:100',
            'ewallet_tipe' => 'nullable|array',
            'ewallet_tipe.*' => 'nullable|string|max:50',
            'ewallet_nomor' => 'nullable|array',
            'ewallet_nomor.*' => 'nullable|string|max:50',
            'ewallet_nama' => 'nullable|array',
            'ewallet_nama.*' => 'nullable|string|max:100',
            'cerita_tahun' => 'nullable|array',
            'cerita_tahun.*' => 'nullable|string|max:20',
            'cerita_judul' => 'nullable|array',
            'cerita_judul.*' => 'nullable|string|max:100',
            'cerita_deskripsi' => 'nullable|array',
            'cerita_deskripsi.*' => 'nullable|string|max:400',
            'jadwal_jam' => 'nullable|array',
            'jadwal_jam.*' => 'nullable|string|max:20',
            'jadwal_judul' => 'nullable|array',
            'jadwal_judul.*' => 'nullable|string|max:100',
            'jadwal_deskripsi' => 'nullable|array',
            'jadwal_deskripsi.*' => 'nullable|string|max:400',
            'alasan_sayang' => 'nullable|array',
            'alasan_sayang.*' => 'nullable|string|max:200',
            'nama_pria' => 'nullable|string|max:100',
            'nama_wanita' => 'nullable|string|max:100',
            'nama_anak' => 'nullable|string|max:100',
        ];

        $rules = array_merge($rules, $optional);

        if ($has('mempelai') || $has('couple_nama')) {
            $rules['nama_pria'] = 'required|string|max:100';
            $rules['nama_wanita'] = 'required|string|max:100';
        }
        if ($has('anak')) {
            $rules['nama_anak'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules, [
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung.',
        ]);

        $validated['kategori'] = $kategori;
        $validated['fields'] = $fields;

        return $validated;
    }

    protected function mapFormData(Request $request, array $validated, ?array $existing = null): array
    {
        $kategori = $validated['kategori'] ?? 'wedding';
        $fields = $validated['fields'] ?? (config('templates.templates.'.($validated['tema'] ?? '').'.fields') ?? []);
        $has = fn (string $g) => in_array($g, $fields, true);

        unset(
            $validated['galeri'],
            $validated['qris_image'],
            $validated['foto_wanita'],
            $validated['foto_pria'],
            $validated['foto_anak'],
            $validated['music_mp3'],
            $validated['music_reset'],
            $validated['waktu_akad_mulai'],
            $validated['waktu_akad_selesai'],
            $validated['waktu_resepsi_mulai'],
            $validated['waktu_resepsi_selesai'],
            $validated['waktu_acara_mulai'],
            $validated['waktu_acara_selesai'],
            $validated['fields'],
        );

        if ($has('anak')) {
            $validated['nama_anak'] = trim((string) ($request->input('nama_anak') ?? ''));
            $validated['nama_wanita'] = $validated['nama_anak'];
            $validated['nama_pria'] = $request->input('usia') ?: ($existing['nama_pria'] ?? 'Ultah');
            $validated['usia'] = $request->input('usia') ?: null;
        }

        if ($has('ortu_host')) {
            $validated['ayah_host'] = $request->input('ayah_host') ?: null;
            $validated['ibu_host'] = $request->input('ibu_host') ?: null;
            $validated['ortu_wanita'] = $this->composeParents(
                $request->input('ayah_host'),
                $request->input('ibu_host')
            );
        }

        if ($has('acara_pesta')) {
            $validated['tanggal_acara'] = $request->input('tanggal_acara') ?: null;
            $validated['tanggal_akad'] = $validated['tanggal_acara'];
            $validated['waktu_acara'] = $this->formatTimeRange(
                $request->input('waktu_acara_mulai'),
                $request->input('waktu_acara_selesai')
            );
            $validated['waktu_akad'] = $validated['waktu_acara'];
            $validated['waktu_acara_mulai'] = $request->input('waktu_acara_mulai') ?: null;
            $validated['waktu_acara_selesai'] = $request->input('waktu_acara_selesai') ?: null;
            $validated['tempat_acara'] = $request->input('tempat_acara') ?: null;
            $validated['tempat_akad'] = $validated['tempat_acara'];
            $validated['alamat_acara'] = $request->input('alamat_acara') ?: null;
            $validated['alamat_akad'] = $validated['alamat_acara'];
        }

        if ($has('dress_code')) {
            $validated['dress_code'] = $request->input('dress_code') ?: null;
        }

        if ($has('tanggal_spesial')) {
            $validated['tanggal_spesial'] = $request->input('tanggal_spesial') ?: null;
            $validated['tanggal_akad'] = $validated['tanggal_spesial'];
        }

        if ($has('surat_janji')) {
            $validated['pesan_janji'] = $request->input('pesan_janji') ?: null;
            if (! filled($validated['kutipan'] ?? null) && filled($validated['pesan_janji'])) {
                $validated['kutipan'] = $validated['pesan_janji'];
            }
        }

        if ($has('akad') || $has('resepsi')) {
            if ($has('akad')) {
                $validated['waktu_akad'] = $this->formatTimeRange(
                    $request->input('waktu_akad_mulai'),
                    $request->input('waktu_akad_selesai')
                );
                $validated['waktu_akad_mulai'] = $request->input('waktu_akad_mulai') ?: null;
                $validated['waktu_akad_selesai'] = $request->input('waktu_akad_selesai') ?: null;
            }
            if ($has('resepsi')) {
                $validated['waktu_resepsi'] = $this->formatTimeRange(
                    $request->input('waktu_resepsi_mulai'),
                    $request->input('waktu_resepsi_selesai')
                );
                $validated['waktu_resepsi_mulai'] = $request->input('waktu_resepsi_mulai') ?: null;
                $validated['waktu_resepsi_selesai'] = $request->input('waktu_resepsi_selesai') ?: null;
            }
        }

        if ($has('ortu_mempelai')) {
            $validated['ortu_wanita'] = $this->composeParents(
                $request->input('ayah_wanita'),
                $request->input('ibu_wanita')
            );
            $validated['ortu_pria'] = $this->composeParents(
                $request->input('ayah_pria'),
                $request->input('ibu_pria')
            );
        }

        $validated['maps_url'] = $this->normalizeMapsUrl($request->input('maps_url'));
        $validated['maps_url_resepsi'] = $this->normalizeMapsUrl($request->input('maps_url_resepsi'));

        // Kolom youtube_url sekarang menyimpan path MP3 lokal; buang URL lama / nilai aneh dari request
        if (! empty($validated['youtube_url'])) {
            $yu = (string) $validated['youtube_url'];
            if (preg_match('#^https?://#i', $yu) || ! str_starts_with($yu, 'uploads/')) {
                $validated['youtube_url'] = null;
            }
        }

        try {
            // uploads/mempelai/{slug}/foto-mempelai|galeri|qris|music/
            $slugKey = Str::slug((string) ($validated['slug'] ?? ($existing['slug'] ?? 'undangan')), '-');
            if ($slugKey === '') {
                $slugKey = 'undangan';
            }
            $dirFoto = 'mempelai/'.$slugKey.'/foto-mempelai';
            $dirGaleri = 'mempelai/'.$slugKey.'/galeri';
            $dirQris = 'mempelai/'.$slugKey.'/qris';
            $dirMusic = 'mempelai/'.$slugKey.'/music';

            $fotoWanita = $request->hasFile('foto_wanita')
                ? $this->storage->storeUpload($request->file('foto_wanita'), $dirFoto, 'foto-wanita')
                : null;
            if ($fotoWanita) {
                $this->storage->deletePublicPath($existing['foto_wanita'] ?? null);
                $validated['foto_wanita'] = $fotoWanita;
            } else {
                $validated['foto_wanita'] = $existing['foto_wanita'] ?? null;
            }

            $fotoPria = $request->hasFile('foto_pria')
                ? $this->storage->storeUpload($request->file('foto_pria'), $dirFoto, 'foto-pria')
                : null;
            if ($fotoPria) {
                $this->storage->deletePublicPath($existing['foto_pria'] ?? null);
                $validated['foto_pria'] = $fotoPria;
            } else {
                $validated['foto_pria'] = $existing['foto_pria'] ?? null;
            }

            $fotoAnak = $request->hasFile('foto_anak')
                ? $this->storage->storeUpload($request->file('foto_anak'), $dirFoto, 'foto-anak')
                : null;
            if ($fotoAnak) {
                $this->storage->deletePublicPath($existing['foto_anak'] ?? null);
                $validated['foto_anak'] = $fotoAnak;
            } else {
                $validated['foto_anak'] = $existing['foto_anak'] ?? null;
            }
            if ($has('foto_anak') && $validated['foto_anak'] && ! $validated['foto_wanita']) {
                $validated['foto_wanita'] = $validated['foto_anak'];
            }

            $fotoFormal = $request->hasFile('foto_formal')
                ? $this->storage->storeUpload($request->file('foto_formal'), $dirFoto, 'foto-formal')
                : null;
            if ($fotoFormal) {
                $this->storage->deletePublicPath($existing['foto_formal'] ?? null);
                $validated['foto_formal'] = $fotoFormal;
            } else {
                $validated['foto_formal'] = $existing['foto_formal'] ?? null;
            }

            $qris = $request->hasFile('qris_image')
                ? $this->storage->storeUpload($request->file('qris_image'), $dirQris, 'qris')
                : null;
            if ($qris) {
                $this->storage->deletePublicPath($existing['qris_image'] ?? null);
                $validated['qris_image'] = $qris;
            } else {
                $validated['qris_image'] = $existing['qris_image'] ?? null;
            }

            $galeriFiles = $request->file('galeri');
            $newGaleri = [];
            if (is_array($galeriFiles) && count(array_filter($galeriFiles)) > 0) {
                $newGaleri = $this->storage->storeMultipleUploads($galeriFiles, $dirGaleri);
            }
            $validated['galeri'] = array_values(array_merge($existing['galeri'] ?? [], $newGaleri));

            // Musik MP3 per undangan → uploads/mempelai/{slug}/music/
            $existingMusic = $existing['youtube_url'] ?? null;
            if (is_string($existingMusic) && preg_match('#^https?://#i', $existingMusic)) {
                $existingMusic = null; // buang URL YouTube lama
            }

            if ($request->boolean('music_reset')) {
                $this->storage->deletePublicPath($existingMusic);
                $validated['youtube_url'] = null;
            } elseif ($request->hasFile('music_mp3')) {
                $musicPath = $this->storage->storeAudioUpload($request->file('music_mp3'), $dirMusic, 'musik');
                if ($musicPath) {
                    $this->storage->deletePublicPath($existingMusic);
                    $validated['youtube_url'] = $musicPath;
                } else {
                    $validated['youtube_url'] = $existingMusic;
                }
            } else {
                $validated['youtube_url'] = $existingMusic;
            }
        } catch (\InvalidArgumentException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'music_mp3' => $e->getMessage(),
            ]);
        }
        $validated['cover_image'] = null;

        $cerita = [];
        if ($has('cerita')) {
            $tahunList = $request->input('cerita_tahun', []);
            $judulList = $request->input('cerita_judul', []);
            $descList = $request->input('cerita_deskripsi', []);
            foreach ($tahunList as $i => $tahun) {
                if (! filled($tahun) && ! filled($judulList[$i] ?? null)) {
                    continue;
                }
                $cerita[] = [
                    'tahun' => $tahun ?? '',
                    'judul' => $judulList[$i] ?? '',
                    'deskripsi' => $descList[$i] ?? '',
                ];
            }
        }
        $validated['cerita'] = $cerita;

        $jadwal = [];
        if ($has('jadwal')) {
            $jamList = $request->input('jadwal_jam', []);
            $judulList = $request->input('jadwal_judul', []);
            $descList = $request->input('jadwal_deskripsi', []);
            foreach ($jamList as $i => $jam) {
                if (! filled($jam) && ! filled($judulList[$i] ?? null)) {
                    continue;
                }
                $jadwal[] = [
                    'jam' => $jam ?? '',
                    'judul' => $judulList[$i] ?? '',
                    'deskripsi' => $descList[$i] ?? '',
                ];
            }
        }
        $validated['jadwal'] = $jadwal;

        $alasan = [];
        if ($has('alasan_sayang')) {
            foreach ($request->input('alasan_sayang', []) as $a) {
                if (filled($a)) {
                    $alasan[] = trim((string) $a);
                }
            }
        }
        $validated['alasan_sayang'] = $alasan;

        $rekening = [];
        $ewallet = [];
        if ($has('gift')) {
            $banks = $request->input('rekening_bank', []);
            $nomors = $request->input('rekening_nomor', []);
            $namas = $request->input('rekening_nama', []);
            foreach ($banks as $i => $bank) {
                if (! filled($bank) && ! filled($nomors[$i] ?? null)) {
                    continue;
                }
                $rekening[] = [
                    'bank' => $bank ?? '',
                    'nomor' => $nomors[$i] ?? '',
                    'atas_nama' => $namas[$i] ?? '',
                ];
            }

            $tipeList = $request->input('ewallet_tipe', []);
            $ewNomor = $request->input('ewallet_nomor', []);
            $ewNama = $request->input('ewallet_nama', []);
            foreach ($tipeList as $i => $tipe) {
                if (! filled($tipe) && ! filled($ewNomor[$i] ?? null)) {
                    continue;
                }
                $ewallet[] = [
                    'tipe' => $tipe ?? '',
                    'nomor' => $ewNomor[$i] ?? '',
                    'atas_nama' => $ewNama[$i] ?? '',
                ];
            }
        }
        $validated['rekening'] = $rekening;
        $validated['ewallet'] = $ewallet;
        $validated['slug'] = Str::lower($validated['slug']);

        return $validated;
    }

    protected function formatTimeRange(?string $mulai, ?string $selesai): ?string
    {
        if (! filled($mulai) && ! filled($selesai)) {
            return null;
        }

        $fmt = function ($time) {
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

        return ($a ?: $b).' WIB';
    }

    protected function composeParents(?string $ayah, ?string $ibu): string
    {
        $ayah = trim((string) $ayah);
        $ibu = trim((string) $ibu);
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
     * Normalisasi link Google Maps (terima short link & tanpa https).
     */
    protected function normalizeMapsUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        // Beberapa share-link Maps gagal FILTER_VALIDATE_URL tapi tetap valid dipakai
        if (preg_match('#(google\.[^/]+/maps|maps\.app\.goo\.gl|goo\.gl/maps|maps\.google\.)#i', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * Catat penjualan ke menu Transaksi memakai harga final katalog saat undangan dibuat.
     * Contoh: harga 100rb, diskon 50% → tercatat 50rb.
     */
    protected function recordSale(array $undangan): void
    {
        $tema = (string) ($undangan['tema'] ?? '');
        if ($tema === '') {
            return;
        }

        $templates = $this->catalog->templates();
        $tpl = $templates[$tema] ?? null;
        $hargaAsli = (int) ($tpl['harga'] ?? 0);
        $diskon = (float) ($tpl['diskon_persen'] ?? 0);
        $hargaFinal = (int) ($tpl['harga_final'] ?? $this->catalog->hargaFinal($hargaAsli, $diskon));

        $namaWanita = trim((string) ($undangan['nama_wanita'] ?? $undangan['nama_anak'] ?? ''));
        $namaPria = trim((string) ($undangan['nama_pria'] ?? ''));
        if ($namaWanita !== '' && $namaPria !== '' && ($undangan['kategori'] ?? '') !== 'ultah_anak') {
            $pelanggan = $namaWanita.' & '.$namaPria;
        } else {
            $pelanggan = $namaWanita !== '' ? $namaWanita : ($namaPria !== '' ? $namaPria : ($undangan['slug'] ?? '—'));
        }

        $this->transactions->record([
            'invitation_id' => $undangan['id'] ?? null,
            'slug' => $undangan['slug'] ?? null,
            'template_key' => $tema,
            'template_nama' => $tpl['nama'] ?? $tema,
            'kategori' => $undangan['kategori'] ?? ($tpl['kategori'] ?? null),
            'pelanggan' => $pelanggan,
            'harga_asli' => $hargaAsli,
            'diskon_persen' => $diskon,
            'harga_final' => $hargaFinal,
        ]);
    }
}
