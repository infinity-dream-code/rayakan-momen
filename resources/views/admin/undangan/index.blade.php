@extends('admin.layout')

@section('title', 'Daftar Undangan')
@section('heading', 'Daftar Undangan')
@section('subheading', 'Kelola semua undangan digital')

@section('content')
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df] flex items-center justify-between gap-3 flex-wrap">
        <p class="text-sm text-gray-500">Total: <strong class="text-[#1a2234]">{{ count($undangan) }}</strong> undangan</p>
        <div class="flex items-center gap-2 flex-wrap">
            @if (($purgeEligible ?? 0) > 0)
                <form method="POST" action="{{ route('admin.undangan.purge-expired') }}"
                      onsubmit="return confirm('Hapus {{ $purgeEligible }} undangan nonaktif yang sudah ≥6 bulan? File foto ikut terhapus.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm border border-red-200 text-red-600 bg-red-50 hover:bg-red-100">
                        <i class="fa-solid fa-trash-can"></i>
                        Hapus nonaktif ({{ $purgeEligible }})
                    </button>
                </form>
            @else
                <span class="text-xs text-gray-400">Tidak ada data siap purge (≥6 bln)</span>
            @endif
            <a href="{{ route('admin.undangan.create') }}" class="btn-gold inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Baru
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Pasangan</th>
                    <th class="px-5 py-3">URL</th>
                    <th class="px-5 py-3">Tema</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Views</th>
                    <th class="px-5 py-3">Ucapan</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($undangan as $item)
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5">
                            @php
                                $kat = $item['kategori'] ?? (config('templates.templates.'.($item['tema'] ?? '').'.kategori') ?? 'wedding');
                                $temaNama = config('templates.templates.'.($item['tema'] ?? '').'.nama') ?? ($item['tema'] ?? '-');
                            @endphp
                            @if ($kat === 'ultah_anak')
                                <p class="font-medium">{{ $item['nama_anak'] ?? $item['nama_wanita'] ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Ultah · {{ $item['usia'] ?? '' }}</p>
                            @elseif ($kat === 'couple')
                                <p class="font-medium">{{ $item['nama_pria'] ?? '-' }} → {{ $item['nama_wanita'] ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Couple</p>
                            @else
                                <p class="font-medium">{{ $item['nama_wanita'] ?? '-' }} &amp; {{ $item['nama_pria'] ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Update: {{ \Illuminate\Support\Carbon::parse($item['updated_at'] ?? now())->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ url('/'.$item['slug']) }}" target="_blank" class="text-[#a8843a] hover:underline">
                                /{{ $item['slug'] }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-medium">{{ $temaNama }}</span>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">{{ str_replace('_', ' ', $kat) }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if (($item['access_state'] ?? 'live') === 'expired')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-amber-50 text-amber-800">Expired</span>
                            @elseif (($item['status'] ?? '') === 'aktif')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">{{ $item['views'] ?? 0 }}</td>
                        <td class="px-5 py-3.5">{{ count($item['ucapan_tersimpan'] ?? []) }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.undangan.laporan', $item['id']) }}" class="w-8 h-8 rounded-lg bg-[#faf7f2] flex items-center justify-center text-[#1a2234] hover:text-[#c9a84c]" title="Laporan">
                                    <i class="fa-solid fa-chart-simple text-xs"></i>
                                </a>
                                <a href="{{ route('admin.undangan.edit', $item['id']) }}" class="w-8 h-8 rounded-lg bg-[#faf7f2] flex items-center justify-center text-[#1a2234] hover:text-[#c9a84c]" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.undangan.destroy', $item['id']) }}" onsubmit="return confirm('Hapus undangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100" title="Hapus">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            Belum ada undangan. <a href="{{ route('admin.undangan.create') }}" class="text-[#a8843a] underline">Buat sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
