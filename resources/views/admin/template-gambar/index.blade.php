@extends('admin.layout')

@section('title', 'Gambar Template')
@section('heading', 'Gambar Template')
@section('subheading', 'Upload preview katalog per template — disimpan di Cloudinary')

@section('content')
@php
    $fallback = fn (string $kat) => match ($kat) {
        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_480,c_fill,g_auto'),
        default => '',
    };
@endphp

<p class="text-sm text-gray-600 mb-8 max-w-2xl">
    Gambar ini tampil di halaman <strong>Katalog</strong> dan saat admin pilih template.
    Format JPG/PNG/WEBP, maks 5MB. Kosong = pakai gambar kategori default.
</p>

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

        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($items as $key => $t)
                @php
                    $preview = $t['preview'] ?? null;
                    $thumb = $preview ?: $fallback($t['kategori'] ?? '');
                @endphp
                <div class="card overflow-hidden">
                    <div class="relative aspect-[3/4] bg-[#1a2234]">
                        @if ($thumb)
                            <img src="{{ $thumb }}" alt="{{ $t['nama'] }}" class="absolute inset-0 w-full h-full object-cover object-top">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-white/30">
                                <i class="fa-solid fa-image text-3xl"></i>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full text-white" style="background: {{ $t['warna'] ?? '#1a2234' }};">
                            {{ $t['nama'] }}
                        </span>
                        @if ($preview)
                            <span class="absolute top-3 right-3 text-[10px] px-2 py-0.5 rounded-full bg-emerald-500 text-white">Custom</span>
                        @else
                            <span class="absolute top-3 right-3 text-[10px] px-2 py-0.5 rounded-full bg-gray-500/80 text-white">Default</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.template-gambar.update', $key) }}" enctype="multipart/form-data" class="p-4 space-y-3">
                        @csrf
                        <p class="text-xs text-gray-400 font-mono">{{ $key }}</p>
                        <div>
                            <label class="form-label">Upload gambar</label>
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input text-xs">
                        </div>
                        @if ($preview)
                            <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                                Hapus &amp; pakai default
                            </label>
                        @endif
                        <button type="submit" class="btn-gold w-full py-2 rounded-lg text-xs">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Simpan
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <p class="text-gray-400">Belum ada template.</p>
@endforelse

<div class="mt-6">
    <a href="{{ route('katalog') }}" target="_blank" class="text-sm text-[#a8843a] hover:underline">
        Lihat katalog publik →
    </a>
</div>
@endsection
