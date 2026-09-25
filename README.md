# Nexus Play — Rental PlayStation

Aplikasi rental PlayStation berbasis Laravel 12 dengan landing page publik, booking pelanggan, jadwal unit, autentikasi admin, dashboard operasional, pembayaran, dan laporan.

## Stack

- Laravel 12 / PHP 8.2
- MariaDB / MySQL
- Blade + Tailwind CSS 4 + Alpine.js
- ApexCharts
- Dompdf
- Vite

## Menjalankan lokal

Pastikan MariaDB aktif, lalu:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Atur kredensial database dan admin pada `.env`, kemudian:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Buka `http://localhost:8000`. Login admin tersedia di `http://localhost:8000/login` menggunakan nilai `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dari `.env`.

## Pengujian

```bash
php artisan test
vendor\bin\pint --test
npm run build
php artisan route:list
```

Test menggunakan SQLite in-memory secara isolated, sedangkan konfigurasi runtime lokal menggunakan MariaDB.

## Booking

- Harga selalu dihitung ulang dari tabel `packages` di server.
- Unit dikunci dengan `lockForUpdate` saat booking dibuat.
- Konflik dicek menggunakan interval overlap pada tabel `schedules`.
- Unit maintenance otomatis ditolak.
- Jam operasional secara default `10:00-02:00` dan dapat diubah dari Pengaturan.
- Status booking dan pembayaran hanya dapat berubah melalui service transaksional.

## Sinkronisasi Unit

Scheduler menjalankan command berikut setiap menit:

```bash
php artisan rental:sync-unit-statuses
```

Jalankan `php artisan schedule:work` saat scheduler aktif.
