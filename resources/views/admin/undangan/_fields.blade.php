{{-- Shared vars expected: $u, $fields (array of active groups), times --}}
@php
    $has = fn (string $g) => in_array($g, $fields, true);
@endphp

@if ($has('mempelai'))
<div class="card p-5 sm:p-6 field-group" data-group="mempelai">
    <h3 class="font-display text-lg mb-4">Data Mempelai</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Nama Panggilan Wanita *</label>
            <input type="text" name="nama_wanita" value="{{ old('nama_wanita', $u['nama_wanita'] ?? '') }}" class="form-input" data-req-for="mempelai">
        </div>
        <div>
            <label class="form-label">Nama Panggilan Pria *</label>
            <input type="text" name="nama_pria" value="{{ old('nama_pria', $u['nama_pria'] ?? '') }}" class="form-input" data-req-for="mempelai">
        </div>
        <div>
            <label class="form-label">Nama Lengkap Wanita</label>
            <input type="text" name="nama_lengkap_wanita" value="{{ old('nama_lengkap_wanita', $u['nama_lengkap_wanita'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Nama Lengkap Pria</label>
            <input type="text" name="nama_lengkap_pria" value="{{ old('nama_lengkap_pria', $u['nama_lengkap_pria'] ?? '') }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('ortu_mempelai'))
<div class="card p-5 sm:p-6 field-group" data-group="ortu_mempelai">
    <h3 class="font-display text-lg mb-4">Orang Tua Mempelai</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Ayah Mempelai Wanita</label>
            <input type="text" name="ayah_wanita" value="{{ old('ayah_wanita', $u['ayah_wanita'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Ibu Mempelai Wanita</label>
            <input type="text" name="ibu_wanita" value="{{ old('ibu_wanita', $u['ibu_wanita'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Ayah Mempelai Pria</label>
            <input type="text" name="ayah_pria" value="{{ old('ayah_pria', $u['ayah_pria'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Ibu Mempelai Pria</label>
            <input type="text" name="ibu_pria" value="{{ old('ibu_pria', $u['ibu_pria'] ?? '') }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('foto_mempelai'))
<div class="card p-5 sm:p-6 field-group" data-group="foto_mempelai">
    <h3 class="font-display text-lg mb-4">Foto Mempelai</h3>
    <p class="text-xs text-gray-500 mb-3">Foto terpisah untuk bagian profil mempelai wanita &amp; pria.</p>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Foto Mempelai Wanita</label>
            <input type="file" name="foto_wanita" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
            @if (!empty($u['foto_wanita']))
                <img src="{{ asset($u['foto_wanita']) }}?v={{ is_file(public_path($u['foto_wanita'])) ? filemtime(public_path($u['foto_wanita'])) : time() }}" alt="Foto wanita" class="mt-2 w-20 h-20 object-cover rounded-full border">
                <p class="text-xs text-gray-400 mt-1 break-all">{{ $u['foto_wanita'] }}</p>
            @endif
        </div>
        <div>
            <label class="form-label">Foto Mempelai Pria</label>
            <input type="file" name="foto_pria" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
            @if (!empty($u['foto_pria']))
                <img src="{{ asset($u['foto_pria']) }}?v={{ is_file(public_path($u['foto_pria'])) ? filemtime(public_path($u['foto_pria'])) : time() }}" alt="Foto pria" class="mt-2 w-20 h-20 object-cover rounded-full border">
                <p class="text-xs text-gray-400 mt-1 break-all">{{ $u['foto_pria'] }}</p>
            @endif
        </div>
    </div>
</div>
@endif

@if ($has('foto_formal'))
<div class="card p-5 sm:p-6 field-group" data-group="foto_formal">
    <h3 class="font-display text-lg mb-2">Foto Formal</h3>
    <p class="text-xs text-gray-500 mb-4">Foto berdua (formal) untuk bagian intro undangan Adat Jawa — terpisah dari galeri.</p>
    <input type="file" name="foto_formal" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
    @if (!empty($u['foto_formal']))
        <img src="{{ asset($u['foto_formal']) }}?v={{ is_file(public_path($u['foto_formal'])) ? filemtime(public_path($u['foto_formal'])) : time() }}" alt="Foto formal" class="mt-3 w-28 h-36 object-cover rounded-lg border">
        <p class="text-xs text-gray-400 mt-1 break-all">{{ $u['foto_formal'] }}</p>
    @endif
</div>
@endif

@if ($has('anak'))
<div class="card p-5 sm:p-6 field-group" data-group="anak">
    <h3 class="font-display text-lg mb-4">Data Anak</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Nama Anak *</label>
            <input type="text" name="nama_anak" value="{{ old('nama_anak', $u['nama_anak'] ?? ($u['nama_wanita'] ?? '')) }}" class="form-input" data-req-for="anak">
        </div>
        <div>
            <label class="form-label">Usia / Ultah ke-</label>
            <input type="text" name="usia" value="{{ old('usia', $u['usia'] ?? '') }}" class="form-input" placeholder="Contoh: 5 tahun">
        </div>
    </div>
</div>
@endif

@if ($has('ortu_host'))
<div class="card p-5 sm:p-6 field-group" data-group="ortu_host">
    <h3 class="font-display text-lg mb-4">Orang Tua / Host</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Nama Ayah</label>
            <input type="text" name="ayah_host" value="{{ old('ayah_host', $u['ayah_host'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Nama Ibu</label>
            <input type="text" name="ibu_host" value="{{ old('ibu_host', $u['ibu_host'] ?? '') }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('foto_anak'))
<div class="card p-5 sm:p-6 field-group" data-group="foto_anak">
    <h3 class="font-display text-lg mb-4">Foto Anak</h3>
    <input type="file" name="foto_anak" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
    @if (!empty($u['foto_anak'] ?? $u['foto_wanita'] ?? null))
        <img src="{{ asset($u['foto_anak'] ?? $u['foto_wanita']) }}" alt="" class="mt-2 w-20 h-20 object-cover rounded-full border">
    @endif
</div>
@endif

@if ($has('couple_nama'))
<div class="card p-5 sm:p-6 field-group" data-group="couple_nama">
    <h3 class="font-display text-lg mb-4">Data Pasangan</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Nama Pengirim *</label>
            <input type="text" name="nama_pria" value="{{ old('nama_pria', $u['nama_pria'] ?? '') }}" class="form-input" data-req-for="couple_nama" placeholder="Dari aku...">
        </div>
        <div>
            <label class="form-label">Nama Penerima *</label>
            <input type="text" name="nama_wanita" value="{{ old('nama_wanita', $u['nama_wanita'] ?? '') }}" class="form-input" data-req-for="couple_nama" placeholder="Untuk sayang...">
        </div>
    </div>
</div>
@endif

@if ($has('foto_couple'))
<div class="card p-5 sm:p-6 field-group" data-group="foto_couple">
    <h3 class="font-display text-lg mb-4">Foto Pasangan</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Foto Pengirim</label>
            <input type="file" name="foto_pria" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
            @if (!empty($u['foto_pria']))
                <img src="{{ asset($u['foto_pria']) }}" alt="" class="mt-2 w-20 h-20 object-cover rounded-full border">
            @endif
        </div>
        <div>
            <label class="form-label">Foto Penerima</label>
            <input type="file" name="foto_wanita" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input">
            @if (!empty($u['foto_wanita']))
                <img src="{{ asset($u['foto_wanita']) }}" alt="" class="mt-2 w-20 h-20 object-cover rounded-full border">
            @endif
        </div>
    </div>
</div>
@endif

@if ($has('akad'))
<div class="card p-5 sm:p-6 field-group" data-group="akad">
    <h3 class="font-display text-lg mb-4">Akad Nikah</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Tanggal Akad</label>
            <input type="date" name="tanggal_akad" value="{{ old('tanggal_akad', $u['tanggal_akad'] ?? '') }}" class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="waktu_akad_mulai" value="{{ $waktuAkadMulai }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="waktu_akad_selesai" value="{{ $waktuAkadSelesai }}" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Tempat Akad</label>
            <input type="text" name="tempat_akad" value="{{ old('tempat_akad', $u['tempat_akad'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Alamat Akad</label>
            <input type="text" name="alamat_akad" value="{{ old('alamat_akad', $u['alamat_akad'] ?? '') }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('resepsi'))
<div class="card p-5 sm:p-6 field-group" data-group="resepsi">
    <h3 class="font-display text-lg mb-4">Resepsi</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Tanggal Resepsi</label>
            <input type="date" name="tanggal_resepsi" value="{{ old('tanggal_resepsi', $u['tanggal_resepsi'] ?? '') }}" class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="waktu_resepsi_mulai" value="{{ $waktuResepsiMulai }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="waktu_resepsi_selesai" value="{{ $waktuResepsiSelesai }}" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Tempat Resepsi</label>
            <input type="text" name="tempat_resepsi" value="{{ old('tempat_resepsi', $u['tempat_resepsi'] ?? '') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Alamat Resepsi</label>
            <input type="text" name="alamat_resepsi" value="{{ old('alamat_resepsi', $u['alamat_resepsi'] ?? '') }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('acara_pesta'))
<div class="card p-5 sm:p-6 field-group" data-group="acara_pesta">
    <h3 class="font-display text-lg mb-4">Detail Pesta</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Tanggal Acara</label>
            <input type="date" name="tanggal_acara" value="{{ old('tanggal_acara', $u['tanggal_acara'] ?? ($u['tanggal_akad'] ?? '')) }}" class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="waktu_acara_mulai" value="{{ $waktuAcaraMulai }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="waktu_acara_selesai" value="{{ $waktuAcaraSelesai }}" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Tempat</label>
            <input type="text" name="tempat_acara" value="{{ old('tempat_acara', $u['tempat_acara'] ?? ($u['tempat_akad'] ?? '')) }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat_acara" value="{{ old('alamat_acara', $u['alamat_acara'] ?? ($u['alamat_akad'] ?? '')) }}" class="form-input">
        </div>
    </div>
</div>
@endif

@if ($has('tanggal_spesial'))
<div class="card p-5 sm:p-6 field-group" data-group="tanggal_spesial">
    <h3 class="font-display text-lg mb-4">Tanggal Spesial</h3>
    <div>
        <label class="form-label">Tanggal countdown (ulang tahun / hari spesial)</label>
        <input type="date" name="tanggal_spesial" value="{{ old('tanggal_spesial', $u['tanggal_spesial'] ?? ($u['tanggal_akad'] ?? '')) }}" class="form-input max-w-xs">
    </div>
</div>
@endif

@if ($has('dress_code'))
<div class="card p-5 sm:p-6 field-group" data-group="dress_code">
    <h3 class="font-display text-lg mb-4">Dress Code</h3>
    <input type="text" name="dress_code" value="{{ old('dress_code', $u['dress_code'] ?? '') }}" class="form-input" placeholder="Contoh: Warna-warni / kostum favorit">
</div>
@endif

@if ($has('maps'))
<div class="card p-5 sm:p-6 field-group" data-group="maps">
    <h3 class="font-display text-lg mb-4">Google Maps</h3>
    <p class="text-xs text-gray-500 mb-3">
        Buka Google Maps → pilih lokasi → <strong>Bagikan</strong> → salin link.
        Boleh pakai link pendek (<code>maps.app.goo.gl/...</code>).
    </p>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Lokasi Akad / Acara utama</label>
            <input type="text" name="maps_url" value="{{ old('maps_url', $u['maps_url'] ?? '') }}" class="form-input" placeholder="https://maps.app.goo.gl/... atau https://maps.google.com/...">
        </div>
        <div>
            <label class="form-label">Lokasi Resepsi <span class="text-gray-400">(opsional)</span></label>
            <input type="text" name="maps_url_resepsi" value="{{ old('maps_url_resepsi', $u['maps_url_resepsi'] ?? '') }}" class="form-input" placeholder="Kosongkan jika sama dengan akad">
            <p class="text-xs text-gray-400 mt-1">Kalau kosong, tombol Resepsi pakai link Akad.</p>
        </div>
    </div>
</div>
@endif

@if ($has('kutipan'))
<div class="card p-5 sm:p-6 field-group" data-group="kutipan">
    <h3 class="font-display text-lg mb-4">Kutipan / Pesan Undangan</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="form-label">Teks kutipan / undangan</label>
            <textarea name="kutipan" rows="2" class="form-input">{{ old('kutipan', $u['kutipan'] ?? '') }}</textarea>
        </div>
        <div>
            <label class="form-label">Sumber kutipan</label>
            <input type="text" name="kutipan_sumber" value="{{ old('kutipan_sumber', $u['kutipan_sumber'] ?? '') }}" class="form-input" placeholder="Opsional — QS. / Mama & Papa">
        </div>
    </div>
</div>
@endif

@if ($has('surat_janji'))
<div class="card p-5 sm:p-6 field-group" data-group="surat_janji">
    <h3 class="font-display text-lg mb-4">Surat &amp; Janji</h3>
    <div>
        <label class="form-label">Isi surat / janji manis</label>
        <textarea name="pesan_janji" rows="4" class="form-input" placeholder="Selamat ulang tahun, sayangku...">{{ old('pesan_janji', $u['pesan_janji'] ?? '') }}</textarea>
    </div>
</div>
@endif

@if ($has('alasan_sayang'))
@php
    $alasan = old('alasan_sayang')
        ? array_values(array_filter(old('alasan_sayang')))
        : ($u['alasan_sayang'] ?? ['']);
    if (count($alasan) === 0) $alasan = [''];
@endphp
<div class="card p-5 sm:p-6 field-group" data-group="alasan_sayang">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-lg">Alasan Aku Sayang Kamu</h3>
        <button type="button" onclick="addAlasan()" class="text-sm text-[#a8843a] font-medium"><i class="fa-solid fa-plus mr-1"></i>Tambah</button>
    </div>
    <div id="alasan-list" class="space-y-3">
        @foreach ($alasan as $a)
            <input type="text" name="alasan_sayang[]" value="{{ $a }}" class="form-input" placeholder="Alasan...">
        @endforeach
    </div>
</div>
@endif

@if ($has('youtube'))
@php
    $musicPath = old('youtube_url', $u['youtube_url'] ?? '');
    $isLocalMusic = is_string($musicPath) && $musicPath !== '' && ! preg_match('#^https?://#i', $musicPath);
@endphp
<div class="card p-5 sm:p-6 field-group" data-group="youtube">
    <h3 class="font-display text-lg mb-4">Musik Undangan (MP3)</h3>
    <p class="text-xs text-gray-500 mb-3">Upload lagu .mp3 (maks 8MB). Kosongkan = pakai musik default template.</p>
    <input type="file" name="music_mp3" accept=".mp3,audio/mpeg" class="form-input">
    @if ($isLocalMusic)
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <audio controls preload="none" class="max-w-full" src="{{ asset($musicPath) }}?v={{ is_file(public_path($musicPath)) ? filemtime(public_path($musicPath)) : time() }}"></audio>
            <label class="text-sm text-gray-600 flex items-center gap-2">
                <input type="checkbox" name="music_reset" value="1" class="rounded border-gray-300">
                Hapus &amp; pakai musik default template
            </label>
            <p class="text-xs text-gray-400 break-all w-full">{{ $musicPath }}</p>
        </div>
    @endif
</div>
@endif

@if ($has('galeri'))
<div class="card p-5 sm:p-6 field-group" data-group="galeri">
    <h3 class="font-display text-lg mb-2">Galeri Foto</h3>
    @php $temaForm = $tema ?? ($u['tema'] ?? request('tema', '')); @endphp
    @if (in_array($temaForm, ['elegan', 'langit_malam', 'classic'], true))
        <p class="text-xs text-gray-500 mb-3 galeri-hint" data-for="elegan,classic,langit_malam">Urutan penting: <strong>foto pertama</strong> dipakai sebagai foto utama / featured di template (dan preview WhatsApp).</p>
    @elseif ($temaForm === 'adat_jawa')
        <p class="text-xs text-gray-500 mb-3 galeri-hint" data-for="adat_jawa">Galeri kenangan (terpisah dari Foto Formal intro).</p>
    @else
        <p class="text-xs text-gray-500 mb-3 galeri-hint" data-for="other">Bisa pilih beberapa foto sekaligus.</p>
    @endif
    <input type="file" name="galeri[]" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple class="form-input">
    @if (!empty($u['galeri']))
        <div class="flex flex-wrap gap-2 mt-3">
            @foreach ($u['galeri'] as $g)
                <img src="{{ asset($g) }}" alt="" class="w-16 h-16 object-cover rounded-lg border">
            @endforeach
        </div>
    @endif
</div>
@endif

@if ($has('cerita'))
@php
    $cerita = old('cerita_tahun')
        ? collect(old('cerita_tahun'))->map(fn($t, $i) => ['tahun' => $t, 'judul' => old('cerita_judul.'.$i), 'deskripsi' => old('cerita_deskripsi.'.$i)])->all()
        : ($u['cerita'] ?? [['tahun' => '', 'judul' => '', 'deskripsi' => '']]);
@endphp
<div class="card p-5 sm:p-6 field-group" data-group="cerita">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-lg">Timeline / Our Story</h3>
        <button type="button" onclick="addCerita()" class="text-sm text-[#a8843a] font-medium"><i class="fa-solid fa-plus mr-1"></i>Tambah</button>
    </div>
    <div id="cerita-list" class="space-y-3">
        @foreach ($cerita as $c)
            <div class="grid sm:grid-cols-4 gap-3 p-3 rounded-xl bg-[#faf7f2] cerita-row">
                <input type="text" name="cerita_tahun[]" value="{{ $c['tahun'] ?? '' }}" class="form-input" placeholder="Tahun / umur">
                <input type="text" name="cerita_judul[]" value="{{ $c['judul'] ?? '' }}" class="form-input" placeholder="Judul">
                <input type="text" name="cerita_deskripsi[]" value="{{ $c['deskripsi'] ?? '' }}" class="form-input sm:col-span-2" placeholder="Deskripsi">
            </div>
        @endforeach
    </div>
</div>
@endif

@if ($has('jadwal'))
@php
    $jadwal = old('jadwal_jam')
        ? collect(old('jadwal_jam'))->map(fn($j, $i) => ['jam' => $j, 'judul' => old('jadwal_judul.'.$i), 'deskripsi' => old('jadwal_deskripsi.'.$i)])->all()
        : ($u['jadwal'] ?? [['jam' => '', 'judul' => '', 'deskripsi' => '']]);
@endphp
<div class="card p-5 sm:p-6 field-group" data-group="jadwal">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-lg">Susunan Acara</h3>
        <button type="button" onclick="addJadwal()" class="text-sm text-[#a8843a] font-medium"><i class="fa-solid fa-plus mr-1"></i>Tambah</button>
    </div>
    <div id="jadwal-list" class="space-y-3">
        @foreach ($jadwal as $j)
            <div class="grid sm:grid-cols-4 gap-3 p-3 rounded-xl bg-[#faf7f2] jadwal-row">
                <input type="text" name="jadwal_jam[]" value="{{ $j['jam'] ?? '' }}" class="form-input" placeholder="10.00">
                <input type="text" name="jadwal_judul[]" value="{{ $j['judul'] ?? '' }}" class="form-input" placeholder="Judul">
                <input type="text" name="jadwal_deskripsi[]" value="{{ $j['deskripsi'] ?? '' }}" class="form-input sm:col-span-2" placeholder="Deskripsi">
            </div>
        @endforeach
    </div>
</div>
@endif

@if ($has('gift'))
@php
    $rekening = old('rekening_bank')
        ? collect(old('rekening_bank'))->map(fn($b, $i) => ['bank' => $b, 'nomor' => old('rekening_nomor.'.$i), 'atas_nama' => old('rekening_nama.'.$i)])->all()
        : ($u['rekening'] ?? [['bank' => '', 'nomor' => '', 'atas_nama' => '']]);
    $ewallet = old('ewallet_tipe')
        ? collect(old('ewallet_tipe'))->map(fn($t, $i) => ['tipe' => $t, 'nomor' => old('ewallet_nomor.'.$i), 'atas_nama' => old('ewallet_nama.'.$i)])->all()
        : ($u['ewallet'] ?? [['tipe' => '', 'nomor' => '', 'atas_nama' => '']]);
@endphp
<div class="card p-5 sm:p-6 field-group" data-group="gift">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-lg">Rekening Bank</h3>
        <button type="button" onclick="addRekening()" class="text-sm text-[#a8843a] font-medium"><i class="fa-solid fa-plus mr-1"></i>Tambah</button>
    </div>
    <div id="rekening-list" class="space-y-3 mb-6">
        @foreach ($rekening as $r)
            <div class="grid sm:grid-cols-3 gap-3 p-3 rounded-xl bg-[#faf7f2] rekening-row">
                <input type="text" name="rekening_bank[]" value="{{ $r['bank'] ?? '' }}" class="form-input" placeholder="Bank">
                <input type="text" name="rekening_nomor[]" value="{{ $r['nomor'] ?? '' }}" class="form-input" placeholder="Nomor rekening">
                <input type="text" name="rekening_nama[]" value="{{ $r['atas_nama'] ?? '' }}" class="form-input" placeholder="Atas nama">
            </div>
        @endforeach
    </div>

    <h3 class="font-display text-lg mb-4">QRIS</h3>
    <input type="file" name="qris_image" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="form-input mb-6">
    @if (!empty($u['qris_image']))
        <img src="{{ asset($u['qris_image']) }}" alt="QRIS" class="mb-6 w-40 h-40 object-contain rounded-xl border bg-white p-2">
    @endif

    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-lg">E-Wallet</h3>
        <button type="button" onclick="addEwallet()" class="text-sm text-[#a8843a] font-medium"><i class="fa-solid fa-plus mr-1"></i>Tambah</button>
    </div>
    <div id="ewallet-list" class="space-y-3">
        @foreach ($ewallet as $e)
            <div class="grid sm:grid-cols-3 gap-3 p-3 rounded-xl bg-[#faf7f2] ewallet-row">
                <select name="ewallet_tipe[]" class="form-input">
                    <option value="">Pilih e-wallet</option>
                    @foreach (['DANA', 'OVO', 'GoPay', 'ShopeePay', 'LinkAja'] as $tipe)
                        <option value="{{ $tipe }}" @selected(($e['tipe'] ?? '') === $tipe)>{{ $tipe }}</option>
                    @endforeach
                </select>
                <input type="text" name="ewallet_nomor[]" value="{{ $e['nomor'] ?? '' }}" class="form-input" placeholder="No. HP / ID">
                <input type="text" name="ewallet_nama[]" value="{{ $e['atas_nama'] ?? '' }}" class="form-input" placeholder="Atas nama">
            </div>
        @endforeach
    </div>
</div>
@endif
