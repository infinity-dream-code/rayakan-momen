@extends('admin.layout')

@section('title', 'Setting')
@section('heading', 'Setting Katalog')
@section('subheading', 'Atur harga, tampil katalog, dan upload gambar preview per template')

@section('content')
@php
    $fallback = fn (string $kat) => match ($kat) {
        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_120,c_fill,g_auto'),
        default => '',
    };
@endphp

<p class="text-sm text-gray-600 mb-6 max-w-3xl">
    Centang <strong>Katalog</strong> untuk menampilkan produk di halaman publik.
    Gambar preview (JPG/PNG/WEBP, maks 5MB) disimpan di Cloudinary — kosong = gambar kategori default.
</p>

<form method="POST" action="{{ route('admin.setting.update') }}" class="space-y-8 pb-24 md:pb-0" id="settingForm">
    @csrf

    @foreach ($grouped as $katId => $items)
        @php $kat = $categories[$katId] ?? ['nama' => $katId, 'icon' => 'fa-layer-group', 'warna' => '#c9a84c']; @endphp
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-[#eee8df] flex items-center gap-3" style="border-left: 4px solid {{ $kat['warna'] ?? '#c9a84c' }}">
                <span class="w-9 h-9 rounded-full bg-[#faf7f2] flex items-center justify-center text-[#a8843a]">
                    <i class="fa-solid {{ $kat['icon'] ?? 'fa-layer-group' }} text-sm"></i>
                </span>
                <div>
                    <h3 class="font-display text-lg">{{ $kat['nama'] }}</h3>
                    <p class="text-xs text-gray-400">{{ count($items) }} produk</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 w-20">Preview</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 w-36">Harga (Rp)</th>
                            <th class="px-4 py-3 w-28">Diskon %</th>
                            <th class="px-4 py-3 w-32">Final</th>
                            <th class="px-4 py-3 w-24 text-center">Katalog</th>
                            <th class="px-4 py-3 w-52">Upload gambar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $t)
                            @php
                                $preview = $t['preview'] ?? null;
                                $thumb = $preview ?: $fallback($t['kategori'] ?? '');
                            @endphp
                            <tr class="border-t border-[#f0ebe3] setting-row" data-key="{{ $key }}">
                                <td class="px-4 py-3">
                                    <div class="w-14 h-[4.5rem] rounded-lg overflow-hidden bg-[#1a2234] relative shrink-0">
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="absolute inset-0 w-full h-full object-cover object-top">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center text-white/30">
                                                <i class="fa-solid fa-image text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $t['nama'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $key }}</p>
                                    @if ($preview)
                                        <span class="inline-block mt-1 text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Custom</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           name="items[{{ $key }}][harga]"
                                           value="{{ old('items.'.$key.'.harga', $t['harga']) }}"
                                           min="0"
                                           step="1000"
                                           class="form-input harga-awal"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <input type="number"
                                               name="items[{{ $key }}][diskon_persen]"
                                               value="{{ old('items.'.$key.'.diskon_persen', $t['diskon_persen']) }}"
                                               min="0"
                                               max="100"
                                               step="1"
                                               class="form-input diskon-persen"
                                               required>
                                        <span class="text-xs text-gray-400">%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-display text-sm harga-final-label text-[#1a2234]">
                                        Rp {{ number_format($t['harga_final'], 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               name="items[{{ $key }}][aktif_katalog]"
                                               value="1"
                                               class="rounded border-gray-300 text-[#c9a84c] focus:ring-[#c9a84c]"
                                               @checked(old('items.'.$key.'.aktif_katalog', $t['aktif_katalog']))>
                                    </label>
                                </td>
                                <td class="px-4 py-3">
                                    {{-- Form terpisah: HTML tidak boleh form bersarang --}}
                                </td>
                            </tr>
                            <tr class="border-t border-dashed border-[#f0ebe3] bg-[#fcfaf7]">
                                <td colspan="7" class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.setting.image', $key) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <div class="flex-1 min-w-[180px]">
                                            <label class="text-xs text-gray-500 block mb-1">Gambar preview — {{ $t['nama'] }}</label>
                                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input text-xs py-1.5">
                                        </div>
                                        @if ($preview)
                                            <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer pb-2">
                                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                                                Hapus gambar
                                            </label>
                                        @endif
                                        <button type="submit" class="btn-gold px-4 py-2 rounded-lg text-xs whitespace-nowrap">
                                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Simpan gambar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="flex flex-wrap gap-3 sticky bottom-4 md:static">
        <button type="submit" class="btn-gold px-6 py-3 rounded-full text-sm shadow-lg">
            <i class="fa-solid fa-floppy-disk mr-2"></i>
            Simpan Katalog
        </button>
        <a href="{{ route('katalog') }}" target="_blank" class="px-6 py-3 rounded-full text-sm border border-gray-300 text-gray-600 hover:bg-white bg-white/90">
            Lihat katalog publik
        </a>
    </div>
</form>

<script>
function formatRp(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function recalcRow(row) {
    const harga = Math.max(0, parseFloat(row.querySelector('.harga-awal').value) || 0);
    let diskon = parseFloat(row.querySelector('.diskon-persen').value) || 0;
    diskon = Math.max(0, Math.min(100, diskon));
    const final = Math.round(harga * (1 - diskon / 100));
    row.querySelector('.harga-final-label').textContent = formatRp(final);
}

document.querySelectorAll('.setting-row').forEach(function (row) {
    row.querySelectorAll('.harga-awal, .diskon-persen').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(row);
        });
    });
});
</script>
@endsection
