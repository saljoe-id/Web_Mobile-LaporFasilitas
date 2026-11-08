# FasilitasKita

Sistem Pengaduan Kerusakan Fasilitas Umum.

## Teknologi
- Laravel 13
- Filament 5
- PHP 8.5
- MariaDB
- Blade
- JavaScript

## Fitur
- Pengaduan tanpa login.
- Upload foto kerusakan.
- Tracking pengaduan.
- Dashboard petugas.
- Manajemen kategori.
- Penugasan petugas.
- Riwayat penanganan.

## CREDIT
saljoe-id
yaf886
hilgamay-cpu

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
