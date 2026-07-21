@extends('admin.layout')

@section('title', 'Laporan')
@section('heading', 'Laporan Undangan')
@section('subheading', $undangan['nama_wanita'].' & '.$undangan['nama_pria'])

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.undangan.index') }}" class="text-sm text-[#a8843a] hover:underline">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="card p-5 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
        <div>
            <h2 class="font-display text-lg text-[#1a2234]">Bagikan Dashboard RSVP</h2>
            <p class="text-xs text-gray-500 mt-1">Link tanpa login — cocok dikirim ke mempelai. Token dari slug yang dienkripsi.</p>
        </div>
        <a href="{{ $rsvpDashboardUrl }}" target="_blank" class="btn-gold inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka
        </a>
    </div>
    <div class="flex flex-wrap gap-2">
        <input id="rsvpShareUrl" type="text" readonly value="{{ $rsvpDashboardUrl }}"
               class="form-input flex-1 min-w-[220px] text-xs bg-[#faf7f2]">
        <button type="button" id="rsvpCopyBtn" class="px-4 py-2 rounded-full text-sm border border-[#e5e0d8] hover:bg-[#faf7f2]">
            <i class="fa-solid fa-copy mr-1"></i> Salin
        </button>
    </div>
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

<script>
(function () {
    var btn = document.getElementById('rsvpCopyBtn');
    var input = document.getElementById('rsvpShareUrl');
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
        input.select();
        input.setSelectionRange(0, 99999);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(input.value).then(function () {
                btn.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Tersalin';
                setTimeout(function () { btn.innerHTML = '<i class="fa-solid fa-copy mr-1"></i> Salin'; }, 1600);
            });
        } else {
            try { document.execCommand('copy'); } catch (e) {}
        }
    });
})();
</script>
@endsection
