@extends('admin.layout')

@section('title', 'Setting Template')
@section('heading', 'Setting Template')
@section('subheading', 'Atur jenis, harga, tampil, dan cover tiap template')

@section('content')
@php
    $fallback = fn (string $kat) => match ($kat) {
        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        default => '',
    };
@endphp

<form method="POST" action="{{ route('admin.setting.update') }}" id="tplForm">
    @csrf
    <div class="card overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px]">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3 w-16">Cover</th>
                    <th class="px-4 py-3 w-44">Nama tampilan</th>
                    <th class="px-4 py-3 w-28">Kode</th>
                    <th class="px-4 py-3 w-36">Jenis</th>
                    <th class="px-4 py-3 w-32">Harga</th>
                    <th class="px-4 py-3 w-24">Diskon</th>
                    <th class="px-4 py-3 w-28">Final</th>
                    <th class="px-4 py-3 w-16 text-center" title="Tampil di halaman katalog">Katalog</th>
                    <th class="px-4 py-3 w-16 text-center" title="Tampil di beranda">Home</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $key => $t)
                    @php
                        $preview = $t['preview'] ?? null;
                        $thumb = $preview ?: $fallback($t['kategori'] ?? '');
                    @endphp
                    <tr class="border-t border-[#f0ebe3] tpl-row">
                        <td class="px-4 py-3">
                            <div class="w-12 h-16 rounded overflow-hidden bg-[#1a2234] relative">
                                @if ($thumb)<img src="{{ $thumb }}" alt="" class="absolute inset-0 w-full h-full object-cover object-top">@endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="items[{{ $key }}][nama]" value="{{ old('items.'.$key.'.nama', $t['nama']) }}" class="form-input py-2" required maxlength="100" placeholder="Nama di katalog">
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-gray-400 font-mono">{{ $key }}</p>
                            @if (($t['nama_asli'] ?? '') !== ($t['nama'] ?? ''))
                                <p class="text-[10px] text-gray-300 mt-0.5">default: {{ $t['nama_asli'] }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <select name="items[{{ $key }}][kategori]" class="form-input text-xs">
                                @foreach ($jenisList as $slug => $jenis)
                                    <option value="{{ $slug }}" @selected(old('items.'.$key.'.kategori', $t['kategori'] ?? '') === $slug)>{{ $jenis['nama'] }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="items[{{ $key }}][harga]" value="{{ old('items.'.$key.'.harga', $t['harga']) }}" min="0" step="1000" class="form-input harga-awal" required>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="items[{{ $key }}][diskon_persen]" value="{{ old('items.'.$key.'.diskon_persen', $t['diskon_persen']) }}" min="0" max="100" class="form-input diskon-persen" required>
                        </td>
                        <td class="px-4 py-3">
                            <span class="harga-final-label font-display text-sm">Rp {{ number_format($t['harga_final'], 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" name="items[{{ $key }}][aktif_katalog]" value="1" class="rounded border-gray-300 text-[#c9a84c]"
                                @checked(old('items.'.$key.'.aktif_katalog', $t['aktif_katalog']))>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" name="items[{{ $key }}][tampil_home]" value="1" class="rounded border-gray-300 text-[#c9a84c]"
                                @checked(old('items.'.$key.'.tampil_home', $t['tampil_home'] ?? false))>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex gap-3 mt-5 sticky bottom-4 md:static">
        <button type="submit" class="btn-gold px-6 py-3 rounded-full text-sm shadow-lg">
            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan
        </button>
        <a href="{{ route('katalog') }}" target="_blank" class="px-6 py-3 rounded-full text-sm border border-gray-300 text-gray-600 bg-white">Lihat katalog</a>
    </div>
</form>

<div class="mt-8 space-y-2">
    <p class="text-sm font-medium text-gray-700 mb-3">Unggah cover template</p>
    @foreach ($templates as $key => $t)
        <div class="card px-4 py-3 flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium w-28 shrink-0">{{ $t['nama'] }}</span>
            <form method="POST" action="{{ route('admin.setting.image', $key) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 flex-1">
                @csrf
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input text-xs py-1.5 flex-1 min-w-[140px]">
                @if ($t['preview'] ?? null)
                    <label class="inline-flex items-center gap-1 text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300"> Hapus
                    </label>
                @endif
                <button type="submit" class="btn-gold px-4 py-2 rounded-lg text-xs">Unggah cover</button>
            </form>
        </div>
    @endforeach
</div>

<script>
document.querySelectorAll('.tpl-row').forEach(function (row) {
    function recalc() {
        const h = Math.max(0, parseFloat(row.querySelector('.harga-awal').value) || 0);
        let d = Math.max(0, Math.min(100, parseFloat(row.querySelector('.diskon-persen').value) || 0));
        row.querySelector('.harga-final-label').textContent = 'Rp ' + Math.round(h * (1 - d / 100)).toLocaleString('id-ID');
    }
    row.querySelectorAll('.harga-awal, .diskon-persen').forEach(function (el) { el.addEventListener('input', recalc); });
});
</script>
@endsection
