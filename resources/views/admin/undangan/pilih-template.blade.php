@extends('admin.layout')

@section('title', 'Pilih Template')
@section('heading', 'Pilih Template')
@section('subheading', 'Pilih kategori & tema — data sementara di cookie/JSON, nanti pindah DB')

@section('content')
@php
    // preserveKeys=true agar key tetap 'elegan', 'ultah_candyland', dll (bukan 0,1,2)
    $grouped = collect($templates)->groupBy('kategori', true);
@endphp

<div class="mb-6 max-w-2xl">
    <p class="text-sm text-gray-600 leading-relaxed">
        Semua kategori bisa dipilih: <strong>Wedding</strong>, <strong>Ultah Anak</strong>, dan <strong>Couple</strong>.
        Setelah pilih, kamu bisa ubah nama, tanggal, dan detail lain di form. Ganti tema juga bisa dari form edit.
    </p>
</div>

@forelse ($grouped as $katId => $items)
    @php $kat = $categories[$katId] ?? ['nama' => ucfirst($katId)]; @endphp
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-full bg-[#faf7f2] border border-[#e5e0d8] flex items-center justify-center text-[#c9a84c]">
                <i class="fa-solid {{ $kat['icon'] ?? 'fa-layer-group' }} text-sm"></i>
            </span>
            <div>
                <h3 class="font-display text-xl">{{ $kat['nama'] }}</h3>
                <p class="text-xs text-gray-500">{{ $kat['tagline'] ?? '' }}</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($items as $key => $t)
                <form method="POST" action="{{ route('admin.undangan.pilih-template') }}" class="card overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
                    @csrf
                    <input type="hidden" name="tema" value="{{ $t['id'] ?? $key }}">
                    <div class="relative aspect-[3/4] overflow-hidden bg-[#1a2234]">
                        @if (! empty($t['preview']))
                            <img src="{{ $t['preview'] }}" alt="{{ $t['nama'] }}" class="absolute inset-0 w-full h-full object-cover object-top">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white/40">
                                <i class="fa-solid {{ $kat['icon'] ?? 'fa-image' }} text-3xl"></i>
                                <span class="text-[10px] uppercase tracking-wider">Preview segera</span>
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <span class="absolute top-3 left-3 text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full text-white" style="background: {{ $t['warna'] }};">
                            {{ $t['nama'] }}
                        </span>
                        @if (empty($t['file']))
                            <span class="absolute bottom-3 left-3 text-[10px] px-2 py-0.5 rounded bg-white/15 text-white/90 backdrop-blur">Preview mode</span>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display text-xl mb-1.5">{{ $t['nama'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mb-4 flex-1">{{ $t['deskripsi'] }}</p>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-gold flex-1 py-2.5 rounded-full text-sm">
                                Pilih &amp; Isi Data
                            </button>
                            @if (! empty($t['demo_url']))
                                <a href="{{ $t['demo_url'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full border border-[#e5e0d8] flex items-center justify-center text-gray-500 hover:text-[#c9a84c]" title="Lihat demo live">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
@empty
    <div class="card p-8 text-center text-gray-500">Belum ada template aktif.</div>
@endforelse
@endsection
