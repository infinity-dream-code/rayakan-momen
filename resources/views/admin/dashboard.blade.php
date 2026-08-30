@extends('admin.layout')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan laporan undangan digital')

@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-8">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-[#faf7f2] text-[#c9a84c]"><i class="fa-solid fa-envelope"></i></div>
        </div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $stats['total_undangan'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Undangan</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-emerald-50 text-emerald-600"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $stats['total_aktif'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Undangan Aktif</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-sky-50 text-sky-600"><i class="fa-solid fa-eye"></i></div>
        </div>
        <p class="text-2xl font-display text-[#1a2234]">{{ number_format($stats['total_views']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Kunjungan</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-violet-50 text-violet-600"><i class="fa-solid fa-comments"></i></div>
        </div>
        <p class="text-2xl font-display text-[#1a2234]">{{ $stats['total_ucapan'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Ucapan / RSVP (Hadir: {{ $stats['total_hadir'] }})</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-green-50 text-green-600"><i class="fa-brands fa-whatsapp"></i></div>
        </div>
        <p class="text-2xl font-display text-[#1a2234]">{{ number_format($stats['wa_clicks'] ?? 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">Klik nomor WhatsApp ({{ wa_display() }})</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df] flex items-center justify-between">
        <h2 class="font-display text-lg">Undangan Terbaru</h2>
        <a href="{{ route('admin.undangan.index') }}" class="text-sm text-[#a8843a] hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Pasangan</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Views</th>
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($undangan as $item)
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5 font-medium">{{ $item['nama_wanita'] }} &amp; {{ $item['nama_pria'] }}</td>
                        <td class="px-5 py-3.5">
                            <a href="{{ url('/'.$item['slug']) }}" target="_blank" class="text-[#a8843a] hover:underline">/{{ $item['slug'] }}</a>
                        </td>
                        <td class="px-5 py-3.5">
                            @if (($item['status'] ?? '') === 'aktif')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">{{ $item['views'] ?? 0 }}</td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('admin.undangan.laporan', $item['id']) }}" class="text-[#1a2234] hover:text-[#c9a84c] mr-2" title="Laporan"><i class="fa-solid fa-chart-simple"></i></a>
                            <a href="{{ route('admin.undangan.edit', $item['id']) }}" class="text-[#1a2234] hover:text-[#c9a84c]" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada undangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
