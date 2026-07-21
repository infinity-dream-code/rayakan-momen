@extends('admin.layout')

@section('title', 'Jenis Katalog')
@section('heading', 'Jenis Katalog')
@section('subheading', 'Kelola kategori: Pernikahan, Ulang Tahun, Couple, dll')

@section('content')
@php
    $fallback = fn (string $slug) => match ($slug) {
        'wedding' => cdn_image('cat_wedding', 'f_auto,q_auto:eco,w_240,c_fill,g_auto'),
        'ultah_anak' => cdn_image('cat_ultah', 'f_auto,q_auto:eco,w_240,c_fill,g_auto'),
        'couple' => cdn_image('cat_couple', 'f_auto,q_auto:eco,w_240,c_fill,g_auto'),
        default => '',
    };
@endphp

<form method="POST" action="{{ route('admin.jenis.update') }}" class="space-y-4 mb-8">
    @csrf
    @foreach ($jenisList as $slug => $jenis)
        @php
            $img = $jenis['image'] ?? null;
            $thumb = $img ?: $fallback($slug);
        @endphp
        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row">
                <div class="sm:w-40 shrink-0 relative aspect-[4/3] bg-[#1a2234]">
                    @if ($thumb)
                        <img src="{{ $thumb }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1 p-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="form-label">Nama</label>
                        <input type="text" name="jenis[{{ $slug }}][nama]" value="{{ old('jenis.'.$slug.'.nama', $jenis['nama']) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Tagline</label>
                        <input type="text" name="jenis[{{ $slug }}][tagline]" value="{{ old('jenis.'.$slug.'.tagline', $jenis['tagline']) }}" class="form-input">
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm pb-2">
                        <input type="checkbox" name="jenis[{{ $slug }}][aktif]" value="1" class="rounded border-gray-300 text-[#c9a84c]"
                            @checked(old('jenis.'.$slug.'.aktif', $jenis['aktif']))>
                        Aktif (filter katalog)
                    </label>
                </div>
            </div>
        </div>
    @endforeach
    <button type="submit" class="btn-gold px-6 py-3 rounded-full text-sm">
        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan
    </button>
</form>

<div class="space-y-3">
    <p class="text-sm font-medium text-gray-700">Cover jenis (opsional)</p>
    @foreach ($jenisList as $slug => $jenis)
        <div class="card px-4 py-3">
            <form method="POST" action="{{ route('admin.jenis.image', $slug) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                @csrf
                <span class="text-sm font-medium w-32">{{ $jenis['nama'] }}</span>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-input text-xs py-1.5 flex-1 min-w-[160px]">
                @if ($jenis['image'] ?? null)
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300"> Hapus
                    </label>
                @endif
                <button type="submit" class="btn-gold px-4 py-2 rounded-lg text-xs">Unggah</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
