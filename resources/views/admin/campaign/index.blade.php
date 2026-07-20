@extends('admin.layout')

@section('title', 'Campaign')
@section('heading', 'Campaign Landing Page')
@section('subheading', 'Popup gambar di halaman awal — muncul 0,8 detik setelah load, auto tutup 1,5 detik, bisa ditutup dengan tombol X')

@section('content')
<div class="card p-5 sm:p-6 max-w-2xl">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <h2 class="font-display text-lg text-[#1a2234]">Pengaturan Campaign</h2>
            <p class="text-xs text-gray-500 mt-1">Gambar di-upload ke Cloudinary. Kosongkan / nonaktifkan jika tidak dipakai.</p>
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
            <p class="text-xs text-gray-400 mt-1">JPG/PNG/WEBP, maks 5MB.</p>
        </div>

        @if (!empty($campaign['image_url']))
            <div class="space-y-3">
                <img src="{{ $campaign['image_url'] }}" alt="Preview campaign" class="max-w-xs w-full rounded-xl border border-[#eee8df] shadow-sm">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                    Hapus gambar campaign
                </label>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Campaign
            </button>
            <a href="{{ route('landing') }}" target="_blank" class="text-sm text-[#a8843a] hover:underline">
                Lihat landing page →
            </a>
        </div>
    </form>
</div>
@endsection
