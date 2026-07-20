@extends('admin.layout')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan laporan undangan digital')

@section('content')
<div class="card p-5 sm:p-6 mb-8">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <h2 class="font-display text-lg text-[#1a2234]">Campaign Landing Page</h2>
            <p class="text-xs text-gray-500 mt-1">Popup gambar di halaman awal — auto tutup 3 detik, bisa ditutup dengan tombol X.</p>
        </div>
        @if (!empty($campaign['aktif']) && !empty($campaign['image_url']))
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
                <i class="fa-solid fa-circle text-[8px]"></i> Aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-500">
                <i class="fa-solid fa-circle text-[8px]"></i> Nonaktif
            </span>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.campaign.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="flex flex-wrap items-center gap-3">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="aktif" value="1" class="rounded border-gray-300"
                       @checked(old('aktif', $campaign['aktif'] ?? false))>
                Aktifkan campaign di landing page
            </label>
        </div>

        <div>
            <label class="form-label">Gambar Campaign</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input">
            <p class="text-xs text-gray-400 mt-1">JPG/PNG/WEBP, maks 5MB. Disimpan ke Cloudinary.</p>
        </div>

        @if (!empty($campaign['image_url']))
            <div class="flex flex-wrap items-start gap-4">
                <img src="{{ $campaign['image_url'] }}" alt="Preview campaign" class="max-w-xs w-full rounded-xl border border-[#eee8df] shadow-sm">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                    Hapus gambar campaign
                </label>
            </div>
        @endif

        <button type="submit" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Campaign
        </button>
    </form>
</div>

<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
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
