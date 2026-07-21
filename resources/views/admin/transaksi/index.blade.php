@extends('admin.layout')

@section('title', 'Transaksi')
@section('heading', 'Transaksi')
@section('subheading', 'Pencatatan penjualan template — harga final setelah diskon')

@section('content')
@php
    $fmt = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.');
@endphp

<div class="flex flex-wrap items-center justify-end gap-3 mb-6">
    <a href="{{ route('admin.transaksi.export') }}" class="btn-gold inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm">
        <i class="fa-solid fa-file-excel"></i> Export Excel
    </a>
</div>

<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="stat-icon bg-[#faf7f2] text-[#c9a84c] mb-3"><i class="fa-solid fa-receipt"></i></div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $stats['total_transaksi'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Terjual</p>
    </div>
    <div class="card p-5">
        <div class="stat-icon bg-emerald-50 text-emerald-600 mb-3"><i class="fa-solid fa-wallet"></i></div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $fmt($stats['total_penghasilan']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Penghasilan</p>
    </div>
    <div class="card p-5">
        <div class="stat-icon bg-sky-50 text-sky-600 mb-3"><i class="fa-solid fa-calendar-day"></i></div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $stats['bulan_ini_transaksi'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Terjual Bulan Ini</p>
    </div>
    <div class="card p-5">
        <div class="stat-icon bg-violet-50 text-violet-600 mb-3"><i class="fa-solid fa-coins"></i></div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $fmt($stats['bulan_ini_penghasilan']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Penghasilan Bulan Ini</p>
    </div>
</div>

<div class="card overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-[#eee8df]">
        <h2 class="font-display text-lg">Per Template</h2>
        <p class="text-xs text-gray-500 mt-0.5">Berapa yang laku &amp; penghasilan (harga setelah diskon)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Template</th>
                    <th class="px-5 py-3">Terjual</th>
                    <th class="px-5 py-3">Penghasilan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byTemplate as $row)
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5 font-medium">{{ $row['template_nama'] }}</td>
                        <td class="px-5 py-3.5">{{ $row['terjual'] }}</td>
                        <td class="px-5 py-3.5 text-emerald-700 font-medium">{{ $fmt($row['penghasilan']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-gray-400">Belum ada penjualan tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df]">
        <h2 class="font-display text-lg">Riwayat Transaksi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Otomatis tercatat saat undangan baru dibuat</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Template</th>
                    <th class="px-5 py-3">Harga</th>
                    <th class="px-5 py-3">Diskon</th>
                    <th class="px-5 py-3">Diterima</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $asli = (int) ($item['harga_asli'] ?? 0);
                        $diskon = (float) ($item['diskon_persen'] ?? 0);
                        $final = (int) ($item['harga_final'] ?? 0);
                    @endphp
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-600">
                            {{ \Carbon\Carbon::parse($item['created_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-medium">{{ $item['pelanggan'] ?: '—' }}</div>
                            @if (!empty($item['slug']))
                                <a href="{{ url('/'.$item['slug']) }}" target="_blank" class="text-xs text-[#a8843a] hover:underline">/{{ $item['slug'] }}</a>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">{{ $item['template_nama'] ?: $item['template_key'] }}</td>
                        <td class="px-5 py-3.5 {{ $diskon > 0 ? 'line-through text-gray-400' : '' }}">{{ $fmt($asli) }}</td>
                        <td class="px-5 py-3.5">
                            @if ($diskon > 0)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-rose-50 text-rose-700">{{ rtrim(rtrim(number_format($diskon, 2, ',', '.'), '0'), ',') }}%</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-emerald-700">{{ $fmt($final) }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <form method="POST" action="{{ route('admin.transaksi.destroy', $item['id']) }}" onsubmit="return confirm('Hapus transaksi ini dari laporan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600" title="Hapus dari laporan">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-gray-400">
                            Belum ada transaksi. Setiap undangan baru otomatis masuk ke sini dengan harga final katalog saat itu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
