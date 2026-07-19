@extends('admin.layout')

@section('title', $mode === 'create' ? 'Tambah Undangan' : 'Edit Undangan')
@section('heading', $mode === 'create' ? 'Isi Data Undangan' : 'Edit Undangan')
@section('subheading', 'Input menyesuaikan template yang dipilih')

@section('content')
@php
    $u = $undangan ?? [];
    $allTemplates = $allTemplates ?? config('templates.templates', []);
    $categories = $categories ?? config('templates.categories', []);
    $selectedTema = old('tema', $tema ?? ($u['tema'] ?? 'elegan'));
    $templateInfo = $allTemplates[$selectedTema] ?? ($templateInfo ?? []);
    $kategori = $templateInfo['kategori'] ?? 'wedding';
    $katInfo = $categories[$kategori] ?? [];
    $fields = $templateInfo['fields'] ?? [];

    $waktuAkadMulai = old('waktu_akad_mulai', $u['waktu_akad_mulai'] ?? '');
    $waktuAkadSelesai = old('waktu_akad_selesai', $u['waktu_akad_selesai'] ?? '');
    $waktuResepsiMulai = old('waktu_resepsi_mulai', $u['waktu_resepsi_mulai'] ?? '');
    $waktuResepsiSelesai = old('waktu_resepsi_selesai', $u['waktu_resepsi_selesai'] ?? '');
    $waktuAcaraMulai = old('waktu_acara_mulai', $u['waktu_acara_mulai'] ?? '');
    $waktuAcaraSelesai = old('waktu_acara_selesai', $u['waktu_acara_selesai'] ?? '');

    $fieldsByTema = collect($allTemplates)->mapWithKeys(fn ($t, $k) => [$k => $t['fields'] ?? []])->all();
@endphp

<div class="card p-4 mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <div class="w-12 h-14 rounded-lg flex items-center justify-center text-white text-lg" style="background: {{ $templateInfo['warna'] ?? '#1a2234' }}">
            <i class="fa-solid {{ $katInfo['icon'] ?? 'fa-layer-group' }}"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider">{{ $katInfo['nama'] ?? 'Template' }}</p>
            <p class="font-display text-lg" id="temaNamaLabel">{{ $templateInfo['nama'] ?? $selectedTema }}</p>
            <p class="text-xs text-gray-400">Form menyesuaikan field template ini</p>
        </div>
    </div>
    @if ($mode === 'create')
        <a href="{{ route('admin.undangan.create') }}" class="text-sm text-[#a8843a] hover:underline">Ganti dari katalog</a>
    @endif
</div>

{{-- Chip daftar field aktif --}}
<div class="flex flex-wrap gap-2 mb-5" id="fieldChips">
    @foreach ($fields as $fg)
        <span class="text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#faf7f2] border border-[#e5e0d8] text-gray-600">
            {{ config('templates.field_groups.'.$fg.'.label', $fg) }}
        </span>
    @endforeach
</div>

<form method="POST"
      action="{{ $mode === 'create' ? route('admin.undangan.store') : route('admin.undangan.update', $u['id']) }}"
      enctype="multipart/form-data"
      class="space-y-6 pb-20 md:pb-0"
      id="undanganForm">
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    <div class="card p-5 sm:p-6">
        <h3 class="font-display text-lg mb-4">Info Dasar &amp; Tema</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Slug URL *</label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 shrink-0">untal.id/</span>
                    <input type="text" name="slug" value="{{ old('slug', $u['slug'] ?? '') }}" class="form-input" placeholder="ica" required>
                </div>
            </div>
            <div>
                <label class="form-label">Status *</label>
                <select name="status" class="form-input" required>
                    <option value="aktif" @selected(old('status', $u['status'] ?? 'aktif') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status', $u['status'] ?? '') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Template *</label>
                <select name="tema" id="temaSelect" class="form-input" required>
                    @foreach ($categories as $katId => $kat)
                        <optgroup label="{{ $kat['nama'] }}">
                            @foreach ($allTemplates as $key => $t)
                                @if (($t['kategori'] ?? '') === $katId && ($t['aktif'] ?? false))
                                    <option value="{{ $key }}"
                                            data-nama="{{ $t['nama'] }}"
                                            data-warna="{{ $t['warna'] }}"
                                            @selected($selectedTema === $key)>
                                        {{ $t['nama'] }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="text-xs text-amber-700 mt-1.5 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                    Ganti template = ganti form. Field yang tidak dipakai template baru akan disembunyikan.
                </p>
            </div>
        </div>
    </div>

    {{-- Render ALL field groups once; JS shows/hides by tema fields --}}
    @php
        // Union of all groups so switching tema works without reload
        $allFieldKeys = collect($allTemplates)->pluck('fields')->flatten()->unique()->values()->all();
        $fields = $allFieldKeys; // render all partials
    @endphp
    <div id="dynamicFields" class="space-y-6">
        @include('admin.undangan._fields', [
            'u' => $u,
            'fields' => $fields,
            'waktuAkadMulai' => $waktuAkadMulai,
            'waktuAkadSelesai' => $waktuAkadSelesai,
            'waktuResepsiMulai' => $waktuResepsiMulai,
            'waktuResepsiSelesai' => $waktuResepsiSelesai,
            'waktuAcaraMulai' => $waktuAcaraMulai,
            'waktuAcaraSelesai' => $waktuAcaraSelesai,
        ])
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn-gold px-6 py-3 rounded-full text-sm">
            <i class="fa-solid fa-floppy-disk mr-2"></i>
            {{ $mode === 'create' ? 'Simpan Undangan' : 'Update Undangan' }}
        </button>
        <a href="{{ $mode === 'create' ? route('admin.undangan.create') : route('admin.undangan.index') }}" class="px-6 py-3 rounded-full text-sm border border-gray-300 text-gray-600 hover:bg-gray-50">
            Batal
        </a>
    </div>
</form>

<script>
const fieldsByTema = @json($fieldsByTema);
const fieldLabels = @json(collect(config('templates.field_groups'))->mapWithKeys(fn($g,$k)=>[$k=>$g['label']??$k]));
const initialTema = @json($selectedTema);

function syncFieldsByTema() {
    const sel = document.getElementById('temaSelect');
    if (!sel) return;
    const tema = sel.value;
    const active = fieldsByTema[tema] || [];
    const activeSet = new Set(active);

    document.querySelectorAll('.field-group').forEach(function (block) {
        const group = block.getAttribute('data-group');
        const show = activeSet.has(group);
        block.style.display = show ? '' : 'none';
        block.querySelectorAll('input, select, textarea, button').forEach(function (el) {
            el.disabled = !show;
        });
    });

    // required flags
    document.querySelectorAll('[data-req-for]').forEach(function (el) {
        const g = el.getAttribute('data-req-for');
        el.required = activeSet.has(g) && !el.disabled;
    });

    // chips
    const chips = document.getElementById('fieldChips');
    if (chips) {
        chips.innerHTML = active.map(function (fg) {
            const label = fieldLabels[fg] || fg;
            return '<span class="text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full bg-[#faf7f2] border border-[#e5e0d8] text-gray-600">' + label + '</span>';
        }).join('');
    }

    const opt = sel.options[sel.selectedIndex];
    const namaLabel = document.getElementById('temaNamaLabel');
    if (namaLabel && opt) namaLabel.textContent = opt.getAttribute('data-nama') || tema;
}

document.getElementById('temaSelect')?.addEventListener('change', syncFieldsByTema);
syncFieldsByTema();

function addCerita() {
    const list = document.getElementById('cerita-list');
    if (!list) return;
    const row = document.createElement('div');
    row.className = 'grid sm:grid-cols-4 gap-3 p-3 rounded-xl bg-[#faf7f2] cerita-row';
    row.innerHTML = `
        <input type="text" name="cerita_tahun[]" class="form-input" placeholder="Tahun / umur">
        <input type="text" name="cerita_judul[]" class="form-input" placeholder="Judul">
        <input type="text" name="cerita_deskripsi[]" class="form-input sm:col-span-2" placeholder="Deskripsi">
    `;
    list.appendChild(row);
}
function addJadwal() {
    const list = document.getElementById('jadwal-list');
    if (!list) return;
    const row = document.createElement('div');
    row.className = 'grid sm:grid-cols-4 gap-3 p-3 rounded-xl bg-[#faf7f2] jadwal-row';
    row.innerHTML = `
        <input type="text" name="jadwal_jam[]" class="form-input" placeholder="10.00">
        <input type="text" name="jadwal_judul[]" class="form-input" placeholder="Judul">
        <input type="text" name="jadwal_deskripsi[]" class="form-input sm:col-span-2" placeholder="Deskripsi">
    `;
    list.appendChild(row);
}
function addAlasan() {
    const list = document.getElementById('alasan-list');
    if (!list) return;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'alasan_sayang[]';
    input.className = 'form-input';
    input.placeholder = 'Alasan...';
    list.appendChild(input);
}
function addRekening() {
    const list = document.getElementById('rekening-list');
    if (!list) return;
    const row = document.createElement('div');
    row.className = 'grid sm:grid-cols-3 gap-3 p-3 rounded-xl bg-[#faf7f2] rekening-row';
    row.innerHTML = `
        <input type="text" name="rekening_bank[]" class="form-input" placeholder="Bank">
        <input type="text" name="rekening_nomor[]" class="form-input" placeholder="Nomor rekening">
        <input type="text" name="rekening_nama[]" class="form-input" placeholder="Atas nama">
    `;
    list.appendChild(row);
}
function addEwallet() {
    const list = document.getElementById('ewallet-list');
    if (!list) return;
    const row = document.createElement('div');
    row.className = 'grid sm:grid-cols-3 gap-3 p-3 rounded-xl bg-[#faf7f2] ewallet-row';
    row.innerHTML = `
        <select name="ewallet_tipe[]" class="form-input">
            <option value="">Pilih e-wallet</option>
            <option value="DANA">DANA</option>
            <option value="OVO">OVO</option>
            <option value="GoPay">GoPay</option>
            <option value="ShopeePay">ShopeePay</option>
            <option value="LinkAja">LinkAja</option>
        </select>
        <input type="text" name="ewallet_nomor[]" class="form-input" placeholder="No. HP / ID">
        <input type="text" name="ewallet_nama[]" class="form-input" placeholder="Atas nama">
    `;
    list.appendChild(row);
}
</script>
@endsection
