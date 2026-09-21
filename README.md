<p align="center">
  <h1 align="center">⚡ Kyro PHP Framework</h1>
  <p align="center">
    <strong>Framework PHP modern, ringan (lightweight), cepat, dan elegan dengan pola arsitektur MVC.</strong>
  </p>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Architecture-MVC-6366F1?style=flat-square" alt="MVC Architecture">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue?style=flat-square" alt="Version">
</p>

---

## 📖 Tentang Kyro

**Kyro** adalah framework PHP yang dirancang untuk pengembang yang menginginkan kesederhanaan, performa tinggi, dan struktur kode yang bersih tanpa *overhead* dependensi yang berat. Kyro menghadirkan pengalaman pengembangan modern seperti Laravel namun dengan bobot yang jauh lebih ringan dan instan untuk dijalankan.

---

## ✨ Fitur Utama

- 🚀 **Routing Dinamis & Ekspresif**: Mendukung seluruh method HTTP (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `ANY`), parameter regex dinamis (`/users/{id}`), route grouping (prefix & middleware), serta named routes.
- 🗄️ **Active Record ORM & Query Builder**: Query builder fluent yang aman dari SQL Injection via PDO Prepared Statements, mendukung MySQL, SQLite, dan PostgreSQL.
- 🛡️ **Keamanan Terintegrasi**: Proteksi CSRF otomatis pada form web, enkripsi/hashing password Bcrypt, dan sanitasi input.
- 📦 **HTTP Request & Response Modern**: Deteksi otomatis JSON payload (`php://input`), validasi form terintegrasi, penanganan upload file, dan fluent response builder (`json`, `view`, `redirect`, `download`).
- 🔄 **Pipeline Middleware**: Eksekusi middleware sebelum dan sesudah controller terpanggil (termasuk bawaan `CorsMiddleware` dan `CsrfMiddleware`).
- 🛠️ **Kyro CLI Power Tool**: Alat bantu CLI mandiri untuk generator controller, model, migration, middleware, migrasi database, inspeksi rute, dan development server lokal.
- 🐞 **Modern Developer Debugger**: Halaman error debug modern bertema gelap saat mode pengembangan aktif, menampilkan cuplikan kode tempat error terjadi secara interaktif.
- 🎨 **Minimalist & Clean Welcome Page**: Halaman beranda bawaan yang bersih, rapi, dan otomatis mendukung mode terang & gelap (*Light/Dark mode*).

---

## 📋 Persyaratan Sistem

- PHP >= 8.2
- Ekstensi PHP PDO (MySQL, SQLite, atau PgSQL)
- Composer

---

## 🚀 Panduan Instalasi Cepat

### 1. Clone Repositori
```bash
git clone https://github.com/username/kyro-framework.git
cd kyro-framework
```

### 2. Pasang Dependensi
```bash
composer install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database Anda di dalam file `.env`:
```env
APP_NAME=Kyro
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=kyro
DB_USER=root
DB_PASS=
```

### 4. Buat Kunci Aplikasi (*Application Key*)
```bash
php kyro key:generate
```

### 5. Jalankan Migrasi Database
```bash
php kyro migrate
```

### 6. Jalankan Server Lokal
```bash
php kyro serve
```
Buka browser Anda dan kunjungi **`http://localhost:8000`** 🎉

---

## 📂 Struktur Direktori

```text
kyro-framework/
├── app/
│   ├── Controllers/       # Controller logika aplikasi
│   ├── Middlewares/       # Middleware kustom aplikasi
│   ├── Models/            # Model Active Record
│   └── Views/             # Template tampilan HTML / View
├── config/
│   ├── app.php            # Konfigurasi aplikasi & timezone
│   ├── database.php       # Konfigurasi koneksi database
│   └── session.php        # Konfigurasi sesi & cookie
├── core/                  # Core engine Kyro Framework
├── database/
│   └── migrations/        # Berkas migrasi skema tabel
├── public/                # Web root publik (index.php, aset)
├── routes/
│   ├── api.php            # Rute REST API (prefix otomatis: /api)
│   └── web.php            # Rute Web aplikasi
├── storage/               # Penyimpanan log & berkas cache
├── .env                   # Pengaturan variabel environment
├── composer.json          # Autoloading & metadata paket
└── kyro                   # Entrypoint Kyro CLI
```

---

## 💻 Contoh Penggunaan

### 1. Routing (`routes/web.php` & `routes/api.php`)

```php
use Core\Router;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

// Rute Dasar
Router::get('/', [HomeController::class, 'index'])->name('home');

// Rute dengan Parameter Dinamis
Router::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

// Route Group dengan Prefix dan Middleware
Router::group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class]], function () {
    Router::get('/dashboard', [AdminController::class, 'dashboard']);
    Router::post('/settings', [AdminController::class, 'saveSettings']);
});
```

### 2. Controller (`app/Controllers/UserController.php`)

```php
namespace App\Controllers;

use Core\Controller;
use Core\Response;
use App\Models\User;

class UserController extends Controller {
    public function show($id): Response {
        $user = User::find($id);

        if (!$user) {
            return $this->json(['error' => 'User tidak ditemukan'], 404);
        }

        return $this->view('users.profile', ['user' => $user]);
    }

    public function store(): Response {
        // Validasi form otomatis
        $data = $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
        ]);

        $user = User::create($data);

        return $this->json(['message' => 'User berhasil dibuat', 'data' => $user], 201);
    }
}
```

### 3. Active Record Model & Query Builder (`app/Models/User.php`)

```php
namespace App\Models;

use Core\Model;

class User extends Model {
    protected ?string $table = 'users';

    protected array $fillable = [
        'name', 'email', 'password'
    ];
}
```

Penggunaan di Controller atau Rute:
```php
use App\Models\User;

// Mengambil semua baris
$users = User::all();

// Mencari berdasarkan Primary Key
$user = User::find(1);

// Query Builder aman dari SQL Injection
$activeUsers = User::where('status', '=', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Membuat data baru
$newUser = User::create([
    'name' => 'Budi Santoso',
    'email' => 'budi@example.com',
    'password' => password_hash('secret', PASSWORD_BCRYPT),
]);

// Update data
$user->name = 'Nama Baru';
$user->save();

// Hapus data
$user->delete();
```

---

## 🛠️ Panduan Perintah Kyro CLI

Kyro dilengkapi dengan perintah CLI yang lengkap:

| Perintah | Keterangan |
|---|---|
| `php kyro serve` | Menjalankan local development server (`http://localhost:8000`). Opsi: `--port=8080` `--host=0.0.0.0` |
| `php kyro route:list` | Menampilkan tabel rapi dari semua rute terdaftar beserta method, URI, action, dan middleware |
| `php kyro key:generate` | Membuat kunci acak aman `APP_KEY` dan memperbarui file `.env` |
| `php kyro migrate` | Menjalankan seluruh berkas migrasi pada folder `database/migrations` |
| `php kyro make:controller <Name>` | Membuat controller baru. Tambahkan opsi `--resource` untuk metode CRUD lengkap |
| `php kyro make:model <Name> [-m]` | Membuat model Active Record. Tambahkan opsi `-m` untuk membuat migrasi sekaligus |
| `php kyro make:middleware <Name>` | Membuat middleware pipeline baru di `app/Middlewares` |
| `php kyro make:migration <Name>` | Membuat berkas skema migrasi baru dengan timestamp |
| `php kyro make:view <Name>` | Membuat template view baru di `app/Views` |

---

## 🔒 Keamanan

Jika Anda menemukan celah keamanan di Kyro Framework, silakan buat issue atau kirimkan pull request.

---

## 📄 Lisensi

Kyro Framework adalah perangkat lunak sumber terbuka di bawah lisensi [MIT License](LICENSE).
