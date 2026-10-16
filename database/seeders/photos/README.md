# Foto Produk Asli

Taruh foto asli di folder ini agar ikut ter-deploy ke server.

## Aturan nama file

```
{slug-produk}.{jpg|jpeg|png|webp}
```

Slug = nama file-nya produk, contoh:

```
nike-air-zoom-pegasus-40.jpg
kopi-susu-gula-aren-1-liter.png
wardah-uv-shield-sunscreen-gel-spf-50-40ml.webp
```

Daftar slug semua produk bisa dilihat dengan:

```bash
php artisan tinker --execute="echo App\Models\Product::pluck('slug')->join(PHP_EOL);"
```

atau dari halaman admin produk (kolom slug di tabel).

## Alur kerja

1. Siapkan foto (disarankan persegi ±1000×1000px, JPG, di bawah 2MB).
2. Simpan di folder ini sesuai slug.
3. Jalankan `php artisan db:seed --force` (lokal).
4. `git add`, commit, push.
5. Di server: `git pull` lalu `php artisan db:seed --force`
   (atau di Docker: `docker compose exec app php artisan db:seed --force`).

Seeder (`ProductImageSeeder`) otomatis menyalin foto ke storage dan
mencatatnya di database. Produk tanpa foto di folder ini tetap dapat
placeholder SVG — dan foto yang diupload via admin tidak akan tertimpa.
