# 🚀 Kyro PHP Framework - Local Project Guide

Selamat datang di proyek **Kyro Framework** Anda! Dokumen ini adalah panduan cepat untuk menjalankan dan mengembangkan aplikasi di lingkungan lokal Anda.

---

## ⚡ Memulai Cepat (Quickstart)

Ikuti langkah mudah berikut untuk menjalankan proyek ini di komputer lokal:

### 1. Konfigurasi Environment
Pastikan file `.env` sudah tersedia. Jika belum, salin dari template:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan pengaturan database Anda (MySQL, SQLite, atau PgSQL):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=kyro
DB_USER=root
DB_PASS=
```

### 2. Pasang Dependensi
Jika folder `vendor` belum tersedia atau baru di-*clone*:
```bash
composer install
```

### 3. Buat Kunci Aplikasi
```bash
php kyro key:generate
```

### 4. Jalankan Migrasi Database
Kyro akan secara otomatis membuat database di MySQL jika belum ada:
```bash
php kyro migrate
```

### 5. Jalankan Server Pengembangan
```bash
php kyro serve
```
Akses aplikasi melalui browser Anda di **`http://localhost:8000`**.

---

## 🛠️ Perintah CLI Cepat (`php kyro`)

| Perintah | Fungsi |
|---|---|
| `php kyro serve` | Menjalankan web server lokal di port 8000 |
| `php kyro route:list` | Menampilkan seluruh daftar rute dan middleware yang aktif |
| `php kyro migrate` | Menjalankan seluruh migrasi database yang ada |
| `php kyro make:controller <Name> [--resource]` | Membuat Controller baru |
| `php kyro make:model <Name> [-m]` | Membuat Model Active Record (opsi `-m` untuk sekalian migrasi) |
| `php kyro make:middleware <Name>` | Membuat Middleware baru di `app/Middlewares` |
| `php kyro make:migration <Name>` | Membuat file migrasi baru di `database/migrations` |
| `php kyro make:view <Name>` | Membuat template view baru di `app/Views` |

---

## 📂 Di Mana Saya Menulis Kode?

- **Rute Web & API**: Tambahkan di [routes/web.php](routes/web.php) atau [routes/api.php](routes/api.php).
- **Controller**: Simpan di dalam folder [app/Controllers/](app/Controllers/).
- **Model Database**: Simpan di dalam folder [app/Models/](app/Models/).
- **Tampilan HTML (Views)**: Simpan di dalam folder [app/Views/](app/Views/).
- **Konfigurasi Tambahan**: Cek folder [config/](config/).

---

## 🌐 Dokumentasi Lengkap
Untuk dokumentasi arsitektur, panduan lengkap, dan pembaruan framework, kunjungi repositori resmi di GitHub.
