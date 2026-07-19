@extends('admin.layout')

@section('title', 'Laporan')
@section('heading', 'Laporan Undangan')
@section('subheading', $undangan['nama_wanita'].' & '.$undangan['nama_pria'])

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.undangan.index') }}" class="text-sm text-[#a8843a] hover:underline">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs text-gray-500 mb-1">URL Undangan</p>
        <a href="{{ url('/'.$undangan['slug']) }}" target="_blank" class="text-[#a8843a] font-medium hover:underline">
            /{{ $undangan['slug'] }}
        </a>
    </div>
    <div class="card p-5">
        <p class="text-xs text-gray-500 mb-1">Total Views</p>
        <p class="text-2xl font-display">{{ number_format($undangan['views'] ?? 0) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-gray-500 mb-1">Konfirmasi Hadir</p>
        <p class="text-2xl font-display text-emerald-600">{{ $hadir }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-gray-500 mb-1">Tidak Hadir</p>
        <p class="text-2xl font-display text-rose-500">{{ $tidakHadir }}</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df]">
        <h2 class="font-display text-lg">Ucapan &amp; RSVP</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Ucapan</th>
                    <th class="px-5 py-3">Kehadiran</th>
                    <th class="px-5 py-3">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ucapan as $item)
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5 font-medium">{{ $item['nama'] }}</td>
                        <td class="px-5 py-3.5 text-gray-600 max-w-md">{{ $item['ucapan'] }}</td>
                        <td class="px-5 py-3.5">
                            @if (($item['kehadiran'] ?? '') === 'hadir')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Hadir</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-rose-50 text-rose-600">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">
                            {{ \Illuminate\Support\Carbon::parse($item['created_at'] ?? now())->format('d M Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada ucapan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
