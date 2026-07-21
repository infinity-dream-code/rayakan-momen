@extends('admin.layout')

@section('title', 'Jenis')
@section('heading', 'Jenis Katalog')
@section('subheading', 'Tambah, ubah, atau hapus jenis undangan — dipakai di dropdown Setting template')

@section('content')

{{-- Tambah --}}
<div class="card p-5 mb-6">
    <h2 class="font-display text-lg mb-4">Tambah Jenis</h2>
    <form method="POST" action="{{ route('admin.jenis.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="form-label">Nama jenis</label>
            <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" placeholder="Mis. Pernikahan, Ulang Tahun, Couple" required maxlength="100">
        </div>
        <button type="submit" class="btn-gold px-5 py-2.5 rounded-lg text-sm">
            <i class="fa-solid fa-plus mr-1"></i> Tambah
        </button>
    </form>
</div>

{{-- Daftar --}}
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-[#eee8df]">
        <h2 class="font-display text-lg">Daftar Jenis</h2>
        <p class="text-xs text-gray-500 mt-0.5">{{ count($jenisList) }} jenis</p>
    </div>

    @if ($jenisList === [])
        <p class="px-5 py-8 text-sm text-gray-400 text-center">Belum ada jenis. Tambah di atas.</p>
    @else
        <div class="divide-y divide-[#f0ebe3]">
            @foreach ($jenisList as $slug => $jenis)
                <div class="px-5 py-4 flex flex-wrap items-center gap-4">
                    <form method="POST" action="{{ route('admin.jenis.update', $slug) }}" class="flex flex-wrap flex-1 items-end gap-3 min-w-0">
                        @csrf
                        @method('PUT')
                        <div class="flex-1 min-w-[180px]">
                            <label class="form-label">Nama jenis</label>
                            <input type="text" name="nama" value="{{ old('nama', $jenis['nama']) }}" class="form-input" required maxlength="100">
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm pb-2.5">
                            <input type="checkbox" name="aktif" value="1" class="rounded border-gray-300 text-[#c9a84c]"
                                @checked(old('aktif', $jenis['aktif']))>
                            Aktif
                        </label>
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm border border-[#e5e0d8] hover:bg-[#faf7f2]">
                            Simpan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.jenis.destroy', $slug) }}" onsubmit="return confirm('Hapus jenis &quot;{{ $jenis['nama'] }}&quot;?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm text-red-600 border border-red-200 hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
