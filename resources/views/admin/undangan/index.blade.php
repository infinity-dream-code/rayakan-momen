@extends('admin.layout')

@section('title', 'Daftar Undangan')
@section('heading', 'Daftar Undangan')
@section('subheading', 'Kelola semua undangan digital')

@section('content')
@php
    $catalogTemplates = app(\App\Repositories\CatalogRepository::class)->templates();
@endphp
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df] flex items-center justify-between gap-3 flex-wrap">
        <p class="text-sm text-gray-500">Total: <strong class="text-[#1a2234]">{{ $undangan->total() }}</strong> undangan</p>
        <div class="flex items-center gap-2 flex-wrap">
            @if (($purgeEligible ?? 0) > 0)
                <form method="POST" action="{{ route('admin.undangan.purge-expired') }}"
                      onsubmit="return confirm('Hapus {{ $purgeEligible }} undangan nonaktif lama? File foto ikut terhapus. Tindakan ini manual &amp; permanen.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm border border-red-200 text-red-600 bg-red-50 hover:bg-red-100">
                        <i class="fa-solid fa-trash-can"></i>
                        Hapus nonaktif lama ({{ $purgeEligible }})
                    </button>
                </form>
            @else
                <span class="text-xs text-gray-400">Nonaktifkan lewat toggle · hapus permanen pakai ikon sampah</span>
            @endif
            <a href="{{ route('admin.undangan.create') }}" class="btn-gold inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Baru
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.undangan.index') }}" class="px-5 py-4 border-b border-[#eee8df] flex items-center gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pasangan..."
                   class="w-full pl-9 pr-3 py-2 rounded-lg border border-[#e5e0d8] text-sm focus:outline-none focus:ring-2 focus:ring-[#c9a84c]/40">
        </div>

        <select name="tema" class="px-3 py-2 rounded-lg border border-[#e5e0d8] text-sm focus:outline-none focus:ring-2 focus:ring-[#c9a84c]/40">
            <option value="">Semua Tema</option>
            @foreach ($catalogTemplates as $key => $t)
                <option value="{{ $key }}" {{ $temaFilter === $key ? 'selected' : '' }}>{{ $t['nama'] ?? $key }}</option>
            @endforeach
        </select>

        <select name="status" class="px-3 py-2 rounded-lg border border-[#e5e0d8] text-sm focus:outline-none focus:ring-2 focus:ring-[#c9a84c]/40">
            <option value="">Semua Status</option>
            <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit" class="px-4 py-2 rounded-lg bg-[#1a2234] text-white text-sm hover:bg-[#232d45]">
            Terapkan
        </button>

        @if ($search !== '' || $temaFilter !== '' || $statusFilter !== '')
            <a href="{{ route('admin.undangan.index') }}" class="text-sm text-gray-500 hover:text-[#c9a84c]">
                Reset
            </a>
        @endif
    </form>

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
                    @php
                        $isAktif = ($item['status'] ?? '') === 'aktif';
                    @endphp
                    <tr class="border-t border-[#f0ebe3]">
                        <td class="px-5 py-3.5">
                            @php
                                $kat = $item['kategori'] ?? (config('templates.templates.'.($item['tema'] ?? '').'.kategori') ?? 'wedding');
                                $temaNama = $catalogTemplates[$item['tema'] ?? '']['nama'] ?? ($item['tema'] ?? '-');
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
                            <form method="POST" action="{{ route('admin.undangan.toggle-status', $item['id']) }}" class="inline-flex items-center gap-2.5">
                                @csrf
                                <button type="submit"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#c9a84c]/focus:ring-offset-1 {{ $isAktif ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                        role="switch"
                                        aria-checked="{{ $isAktif ? 'true' : 'false' }}"
                                        title="{{ $isAktif ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 {{ $isAktif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                                <span class="text-xs {{ $isAktif ? 'text-emerald-700' : 'text-gray-500' }}">
                                    {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </form>
                        </td>
                        <td class="px-5 py-3.5">{{ $item['views'] ?? 0 }}</td>
                        <td class="px-5 py-3.5">{{ count($item['ucapan_tersimpan'] ?? []) }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @php
                                    $rsvpUrl = app(\App\Services\RsvpDashboardCipher::class)->urlForSlug((string) ($item['slug'] ?? ''));
                                @endphp
                                <a href="{{ $rsvpUrl }}" target="_blank" class="w-8 h-8 rounded-lg bg-[#faf7f2] flex items-center justify-center text-[#1a2234] hover:text-[#c9a84c]" title="Dashboard RSVP">
                                    <i class="fa-solid fa-clipboard-list text-xs"></i>
                                </a>
                                <a href="{{ route('admin.undangan.laporan', $item['id']) }}" class="w-8 h-8 rounded-lg bg-[#faf7f2] flex items-center justify-center text-[#1a2234] hover:text-[#c9a84c]" title="Laporan">
                                    <i class="fa-solid fa-chart-simple text-xs"></i>
                                </a>
                                <a href="{{ route('admin.undangan.edit', $item['id']) }}" class="w-8 h-8 rounded-lg bg-[#faf7f2] flex items-center justify-center text-[#1a2234] hover:text-[#c9a84c]" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.undangan.destroy', $item['id']) }}" onsubmit="return confirm('Hapus permanen undangan ini?\nData, foto, galeri, dan folder uploads ikut terhapus.')">
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
                            @if ($search !== '' || $temaFilter !== '' || $statusFilter !== '')
                                Tidak ada undangan yang cocok dengan filter. <a href="{{ route('admin.undangan.index') }}" class="text-[#a8843a] underline">Reset filter</a>
                            @else
                                Belum ada undangan. <a href="{{ route('admin.undangan.create') }}" class="text-[#a8843a] underline">Buat sekarang</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($undangan->hasPages())
        <div class="px-5 py-4 border-t border-[#eee8df]">
            {{ $undangan->onEachSide(1)->links() }}
        </div>
    @endif
</div>
@endsection