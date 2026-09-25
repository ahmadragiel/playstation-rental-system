# 🎮 PlayStation Rental System

**PlayStation Rental System** adalah aplikasi berbasis web yang dibuat untuk membantu proses pengelolaan usaha penyewaan PlayStation secara lebih mudah, terorganisir, dan efisien.

Sistem ini memiliki dua bagian utama, yaitu **Landing Page untuk pelanggan** dan **Dashboard Admin untuk pengelola rental**.

Pelanggan dapat melihat informasi rental, pilihan paket, unit PlayStation, game, jadwal, serta melakukan booking. Admin dapat mengelola seluruh aktivitas rental melalui dashboard.

---

## 📌 Tentang Project

Aplikasi ini dibuat untuk membantu usaha rental PlayStation dalam mengelola proses yang sebelumnya dilakukan secara manual.

Melalui sistem ini, pelanggan dapat melakukan pemesanan berdasarkan tanggal, waktu, durasi, paket, dan unit PlayStation yang tersedia.

Admin dapat memantau dan mengelola:

* Data booking
* Jadwal rental
* Data pelanggan
* Unit PlayStation
* Paket sewa
* Game
* Pembayaran
* Pendapatan
* Laporan

Sistem juga dirancang untuk mencegah terjadinya **double booking** dan pemesanan unit yang sedang dalam status maintenance.

---

## ✨ Fitur Utama

### 👤 Pelanggan

* Landing page yang modern dan responsif
* Melihat informasi rental
* Melihat paket sewa
* Melihat unit PlayStation
* Melihat game yang tersedia
* Melihat fasilitas rental
* Mengecek jadwal rental
* Melakukan booking
* Perhitungan harga otomatis
* Melihat informasi booking
* Melihat status booking
* Informasi pembayaran
* Kontak WhatsApp

### 🛠️ Admin

* Login admin
* Dashboard utama
* Manajemen booking
* Manajemen jadwal
* Manajemen pelanggan
* Manajemen unit PlayStation
* Manajemen paket sewa
* Manajemen game
* Manajemen pembayaran
* Laporan transaksi
* Pengaturan sistem

---

## 📊 Dashboard Admin

Dashboard admin menampilkan berbagai informasi penting mengenai kondisi rental, seperti:

* Jumlah booking hari ini
* Booking yang sedang aktif
* Total pendapatan hari ini
* Total pendapatan bulan ini
* Jumlah unit tersedia
* Jumlah unit sedang digunakan
* Jumlah unit maintenance
* Grafik pendapatan
* Grafik jumlah booking
* Booking terbaru

Dengan dashboard ini, admin dapat memantau kondisi rental tanpa harus mengecek data satu per satu.

---

## 🎮 Manajemen Unit PlayStation

Setiap unit PlayStation dapat dikelola melalui dashboard.

Contoh unit:

```text
PS4-01
PS4-02
PS5-01
PS5-02
```

Data unit meliputi:

* Kode unit
* Nama unit
* Jenis PlayStation
* Kondisi
* Status
* Lokasi
* Catatan
* Foto

Status unit:

* 🟢 Available
* 🟡 Booked
* 🔵 In Use
* 🔴 Maintenance

---

## 💰 Paket Sewa

Admin dapat menambahkan dan mengelola paket sewa.

Contoh:

* PS4 Reguler
* PS5 Reguler
* PS5 VIP
* Paket 3 Jam
* Paket 5 Jam
* Paket Malam
* Paket Weekend

Setiap paket memiliki informasi:

* Nama paket
* Jenis PlayStation
* Durasi
* Harga
* Deskripsi
* Fasilitas
* Status aktif

---

## 🕹️ Game

Sistem menyediakan daftar game yang tersedia di rental.

Contoh:

* EA Sports FC
* GTA V
* Tekken
* Mortal Kombat
* Spider-Man
* NBA 2K
* Call of Duty
* eFootball

Admin dapat menambah, mengedit, dan menghapus data game.

---

## 📅 Sistem Booking

Pelanggan dapat melakukan booking dengan mengisi:

* Nama
* Nomor WhatsApp
* Paket sewa
* Unit PlayStation
* Tanggal
* Jam mulai
* Durasi
* Catatan

Sistem akan menghitung total biaya secara otomatis.

### Alur Booking

```text
Pelanggan
    ↓
Pilih Paket
    ↓
Pilih Tanggal & Jam
    ↓
Cek Ketersediaan Unit
    ↓
Isi Data Pelanggan
    ↓
Perhitungan Harga
    ↓
Booking
    ↓
Pembayaran
    ↓
Konfirmasi Admin
    ↓
Mulai Bermain
    ↓
Booking Selesai
```

### Status Booking

* Pending
* Confirmed
* Paid
* Ongoing
* Completed
* Cancelled

Sistem harus mencegah:

* Double booking
* Booking unit maintenance
* Booking di luar jam operasional
* Data booking yang tidak lengkap
* Perhitungan harga yang tidak sesuai

---

## 💳 Pembayaran

Data pembayaran dapat dikelola melalui dashboard admin.

Metode pembayaran:

* Cash
* Transfer
* QRIS

Status pembayaran:

* Pending
* Paid
* Failed
* Refunded

Informasi pembayaran meliputi:

* Nomor transaksi
* Nomor booking
* Pelanggan
* Total pembayaran
* Metode pembayaran
* Status pembayaran
* Tanggal pembayaran

---

## 📈 Laporan

Admin dapat melihat berbagai laporan rental, seperti:

* Laporan pendapatan
* Laporan booking
* Laporan penggunaan unit
* Paket yang paling banyak dipesan
* Data pelanggan

Filter laporan:

* Hari ini
* Minggu ini
* Bulan ini
* Tahun ini
* Rentang tanggal tertentu

---

## 🗄️ Struktur Database

Database menggunakan sistem relasional dengan beberapa tabel utama:

```text
users
customers
playstation_types
playstation_units
packages
games
bookings
booking_details
payments
schedules
settings
```

Setiap tabel memiliki relasi yang disesuaikan dengan kebutuhan sistem.

Database menggunakan:

* Primary Key
* Foreign Key
* Relationship
* Index
* Timestamps

---

## 🛠️ Teknologi yang Digunakan

### Backend

* Laravel
* PHP
* Laravel Eloquent

### Frontend

* Blade
* Tailwind CSS
* JavaScript
* HTML5
* CSS3

### Database

* MySQL

### Tools

* Git
* GitHub
* Laragon
* OpenCode
* Visual Studio Code

---

## 🎨 Tampilan dan UI/UX

Website menggunakan konsep desain:

* Dark Gaming Theme
* Modern
* Minimalis
* Responsif
* Clean
* User Friendly

Tampilan dibuat agar tetap memiliki nuansa gaming tetapi tidak terlalu ramai dan tetap mudah digunakan.

---

## 📱 Responsive Design

Sistem dapat digunakan pada berbagai perangkat:

* 💻 Desktop
* 💻 Laptop
* 📱 Smartphone
* 📱 Tablet

Landing page maupun dashboard admin dirancang agar tetap nyaman digunakan pada ukuran layar yang berbeda.

---

## 🔐 Keamanan

Sistem menerapkan beberapa aspek keamanan dasar, seperti:

* Authentication
* Password Hashing
* CSRF Protection
* Validasi Input
* Route Protection
* Authorization
* Secure Session
* Proteksi Query Database
* Mass Assignment Protection

---

## ⚙️ Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/ahmadragiel/playstation-rental-system.git
```

### 2. Masuk ke Folder Project

```bash
cd playstation-rental-system
```

### 3. Install Dependency Laravel

```bash
composer install
```

### 4. Install Dependency Frontend

```bash
npm install
```

### 5. Buat File `.env`

Windows:

```bash
copy .env.example .env
```

Linux / macOS:

```bash
cp .env.example .env
```

### 6. Generate Application Key

```bash
php artisan key:generate
```

### 7. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=playstation_rental
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```text
playstation_rental
```

### 8. Jalankan Migration

```bash
php artisan migrate
```

Jika tersedia seeder:

```bash
php artisan db:seed
```

atau:

```bash
php artisan migrate --seed
```

### 9. Buat Storage Link

```bash
php artisan storage:link
```

### 10. Jalankan Laravel

```bash
php artisan serve
```

Akses melalui:

```text
http://127.0.0.1:8000
```

### 11. Jalankan Vite

Buka terminal baru:

```bash
npm run dev
```

---

## 📂 Struktur Project

Struktur utama project:

```text
playstation-rental-system/
│
├── app/
│   ├── Http/
│   ├── Models/
│   └── ...
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── customer/
│   │   └── admin/
│   ├── css/
│   └── js/
│
├── routes/
│   └── web.php
│
├── public/
├── storage/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 🚀 Pengembangan Selanjutnya

Beberapa fitur yang dapat dikembangkan pada versi berikutnya:

* Integrasi payment gateway
* Integrasi QRIS otomatis
* Notifikasi WhatsApp
* Reminder booking otomatis
* Akun pelanggan
* Sistem membership
* Voucher dan diskon
* Invoice otomatis
* Analitik rental yang lebih lengkap
* Multi-cabang rental

---

## 🎓 Informasi Project

**Nama Project:** PlayStation Rental System
**Jenis:** Aplikasi Manajemen Rental
**Platform:** Web
**Kategori:** Sistem Informasi
**Tujuan:** Membantu pengelolaan usaha penyewaan PlayStation dan proses booking pelanggan

Project ini dibuat sebagai bagian dari pengembangan aplikasi berbasis web dan pembelajaran full-stack development.

---

## 👨‍💻 Pengembang

**Ahmad Ragiel Zaini**

GitHub: [@ahmadragiel](https://github.com/ahmadragiel)

---

## 📄 Lisensi

Project ini dibuat untuk keperluan **pembelajaran, pengembangan project, dan portfolio**.
