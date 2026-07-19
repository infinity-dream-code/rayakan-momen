@extends('admin.layout')

@section('title', 'Harga Katalog')
@section('heading', 'Harga & Diskon Katalog')
@section('subheading', 'Atur harga tiap produk — diskon % otomatis menghitung harga final (tersimpan di database)')

@section('content')
<form method="POST" action="{{ route('admin.katalog.update') }}" class="space-y-8 pb-24 md:pb-0" id="katalogForm">
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
                <table class="w-full text-sm">
                    <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-5 py-3 w-40">Harga awal (Rp)</th>
                            <th class="px-5 py-3 w-32">Diskon %</th>
                            <th class="px-5 py-3 w-40">Harga final</th>
                            <th class="px-5 py-3 w-28 text-center">Tampil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $t)
                            <tr class="border-t border-[#f0ebe3] katalog-row" data-key="{{ $key }}">
                                <td class="px-5 py-3.5">
                                    <p class="font-medium">{{ $t['nama'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $key }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <input type="number"
                                           name="items[{{ $key }}][harga]"
                                           value="{{ old('items.'.$key.'.harga', $t['harga']) }}"
                                           min="0"
                                           step="1000"
                                           class="form-input harga-awal"
                                           required>
                                </td>
                                <td class="px-5 py-3.5">
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
                                <td class="px-5 py-3.5">
                                    <p class="font-display text-base harga-final-label text-[#1a2234]">
                                        Rp {{ number_format($t['harga_final'], 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 harga-hemat mt-0.5 @if(!$t['punya_diskon']) hidden @endif">
                                        Hemat {{ rtrim(rtrim(number_format($t['diskon_persen'], 1, ',', ''), '0'), ',') }}%
                                    </p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox"
                                               name="items[{{ $key }}][aktif_katalog]"
                                               value="1"
                                               class="rounded border-gray-300 text-[#c9a84c] focus:ring-[#c9a84c]"
                                               @checked(old('items.'.$key.'.aktif_katalog', $t['aktif_katalog']))>
                                        <span class="text-xs text-gray-500">Ya</span>
                                    </label>
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
            Simpan Semua Harga
        </button>
        <a href="{{ route('landing') }}#template" target="_blank" class="px-6 py-3 rounded-full text-sm border border-gray-300 text-gray-600 hover:bg-white bg-white/90">
            Lihat di beranda
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
    const hemat = row.querySelector('.harga-hemat');
    if (hemat) {
        if (diskon > 0 && harga > 0) {
            hemat.classList.remove('hidden');
            hemat.textContent = 'Hemat ' + diskon + '%';
        } else {
            hemat.classList.add('hidden');
        }
    }
}

document.querySelectorAll('.katalog-row').forEach(function (row) {
    row.querySelectorAll('.harga-awal, .diskon-persen').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(row);
        });
    });
});
</script>
@endsection
