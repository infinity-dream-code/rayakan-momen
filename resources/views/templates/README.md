# Cara edit template undangan

Folder ini berisi 3 desain undangan. Edit file sesuai tema:

- `elegan.blade.php` → tema Elegant
- `classic.blade.php` → tema Classic Wedding
- `langit_malam.blade.php` → tema Langit Malam

## Yang biasanya diubah

Di masing-masing file, ubah array `$theme`:

- `bg` = warna background
- `accent` = warna emas/aksen
- `text` / `muted` = warna teks
- `cover` = foto default jika cover kosong

Struktur HTML lengkap ada di `_layout.blade.php`.
Kalau mau ubah layout section (urutan, animasi cover, dll), edit `_layout.blade.php`.

## Flow admin

1. Tambah Undangan → pilih template (tersimpan di cookie)
2. Isi form data
3. Undangan tampil di `/{slug}` pakai template yang dipilih

Demo tetap tanpa database (data di `storage/app/invitations.json`, pilihan template di cookie).
