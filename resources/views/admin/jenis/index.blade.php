@extends('admin.layout')

@section('title', 'Jenis')
@section('heading', 'Jenis Katalog')
@section('subheading', 'Kelola jenis undangan untuk dropdown di Setting template')

@section('content')

<div class="card overflow-hidden">
    {{-- Tambah --}}
    <div class="px-5 py-4 border-b border-[#eee8df] bg-[#fcfaf7]">
        <form method="POST" action="{{ route('admin.jenis.store') }}" class="flex flex-wrap items-center gap-3">
            @csrf
            <span class="text-sm font-medium text-gray-700 shrink-0">Tambah jenis</span>
            <input type="text" name="nama" value="{{ old('nama') }}" class="form-input flex-1 min-w-[200px] max-w-md py-2" placeholder="Pernikahan, Ulang Tahun, Couple…" required maxlength="100">
            <button type="submit" class="btn-gold px-4 py-2 rounded-lg text-sm shrink-0">
                <i class="fa-solid fa-plus mr-1"></i> Tambah
            </button>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#faf7f2] text-left text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3 w-12">#</th>
                    <th class="px-5 py-3">Nama jenis</th>
                    <th class="px-5 py-3 w-24 text-center">Aktif</th>
                    <th class="px-5 py-3 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jenisList as $slug => $jenis)
                    <tr class="border-t border-[#f0ebe3] hover:bg-[#fcfaf7]/50">
                        <td class="px-5 py-3 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3">
                            <form id="jenis-form-{{ $slug }}" method="POST" action="{{ route('admin.jenis.update', $slug) }}">
                                @csrf
                                @method('PUT')
                            </form>
                            <input form="jenis-form-{{ $slug }}" type="text" name="nama" value="{{ $jenis['nama'] }}" class="form-input py-2 w-full max-w-sm" required maxlength="100">
                        </td>
                        <td class="px-5 py-3 text-center">
                            <input form="jenis-form-{{ $slug }}" type="checkbox" name="aktif" value="1" class="rounded border-gray-300 text-[#c9a84c] focus:ring-[#c9a84c]"
                                @checked($jenis['aktif'])>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button form="jenis-form-{{ $slug }}" type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-[#e5e0d8] hover:bg-white bg-[#faf7f2]">
                                    Simpan
                                </button>
                                <form method="POST" action="{{ route('admin.jenis.destroy', $slug) }}" onsubmit="return confirm('Hapus jenis &quot;{{ $jenis['nama'] }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg text-red-500 border border-red-100 hover:bg-red-50 inline-flex items-center justify-center" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada jenis. Tambah di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (count($jenisList) > 0)
        <div class="px-5 py-3 border-t border-[#f0ebe3] text-xs text-gray-400">
            {{ count($jenisList) }} jenis · dipakai di filter katalog &amp; dropdown Setting
        </div>
    @endif
</div>

@endsection
