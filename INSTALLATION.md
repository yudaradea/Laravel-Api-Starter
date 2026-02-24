# 📦 Panduan Instalasi (Installation Guide)

Panduan lengkap instalasi Laravel API Starter Pack dari awal hingga running di local environment Anda.

## ✅ Persyaratan Sistem (Requirements)

Sebelum memulai, pastikan sistem Anda memenuhi persyaratan berikut:

-   **PHP**: Versi 8.2 atau lebih baru
-   **Composer**: Dependency Manager untuk PHP
-   **Database**: MySQL >= 8.0 atau SQLite (untuk development cepat)
-   **Git**: Untuk cloning repository (opsional)
-   **Node.js & NPM**: Untuk menjalankan frontend (opsional)

---

## 🚀 Metode 1: Instalasi Fresh (Direkomendasikan)

Metode ini paling bersih karena kita membuat project Laravel baru lalu menimpa file-filenya dengan Starter Pack.

### Langkah 1: Buat Project Laravel Baru

Jalankan perintah berikut di terminal:

```bash
composer create-project laravel/laravel my-api-project
cd my-api-project
```

### Langkah 2: Install Dependencies Wajib

Kita perlu menginstall package yang digunakan oleh Starter Pack:

```bash
# Untuk Authentication
composer require laravel/sanctum

# Untuk Role & Permission Management
composer require spatie/laravel-permission
```

### Langkah 3: Extract & Copy Starter Pack

1. Extract file `laravel-api-starter.zip` yang Anda miliki.
2. Salin folder/file dari Starter Pack ke dalam folder `my-api-project`.

**Cara Copy (Manual):**
Copy dan Timpa (Overwrite) folder/file berikut:

-   Folder `app/` -> timpa ke `app/`
-   Folder `database/` -> timpa ke `database/`
-   Folder `routes/` -> timpa ke `routes/`
-   Folder `bootstrap/` -> timpa ke `bootstrap/`
-   Folder `config/` -> timpa ke `config/`

**Cara Copy (Terminal/Bash):**

```bash
# Asumsi Anda berada di folder yang berisi ekstrak starter pack
cp -r app/* /path/to/my-api-project/app/
cp -r database/migrations/* /path/to/my-api-project/database/migrations/
cp -r database/seeders/* /path/to/my-api-project/database/seeders/
cp routes/api.php /path/to/my-api-project/routes/
cp routes/v1.php /path/to/my-api-project/routes/
cp bootstrap/app.php /path/to/my-api-project/bootstrap/
cp bootstrap/providers.php /path/to/my-api-project/bootstrap/
cp config/sanctum.php /path/to/my-api-project/config/
cp config/permission.php /path/to/my-api-project/config/
```

### Langkah 4: Konfigurasi Database

Anda bisa memilih menggunakan MySQL atau SQLite.

#### Opsi A: MySQL (Standard Production)

1. Buat database baru di MySQL (misal via phpMyAdmin atau HeidiSQL): `my_api_db`.
2. Duplikasi file `.env.example` menjadi `.env`.
3. Edit file `.env` dan sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_api_db
DB_USERNAME=root
DB_PASSWORD=password_database_anda
```

#### Opsi B: SQLite (Cepat & Mudah untuk Dev)

1. Buat file kosong bernama `database.sqlite` di dalam folder `database`.

```bash
touch database/database.sqlite
# Atau di Windows Command Prompt:
# type nul > database/database.sqlite
```

2. Edit file `.env`:

```env
DB_CONNECTION=sqlite
# DB_HOST=... (Hapus atau komentari baris koneksi DB lainnya)
```

### Langkah 5: Generate Application Key

```bash
php artisan key:generate
```

### Langkah 6: Bersihkan Cache (Penting)

Karena kita baru saja mengubah konfigurasi, pastikan cache bersih:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Langkah 7: Link Storage

Agar fitur upload file berfungsi (avatar, dokumen, dll):

```bash
php artisan storage:link
```

### Langkah 8: Migrasi Database & Seeding

Perintah ini akan membuat tabel-tabel di database dan mengisi data awal (seperti Role Admin & User).

```bash
php artisan migrate:fresh --seed
```

**Indikasi Sukses:**
Anda akan melihat output seperti `Migrated: ...` dan `Seeded: Database\Seeders\RoleSeeder`.

### Langkah 9: Verifikasi Instalasi

Cek daftar route untuk memastikan API sudah terdaftar:

```bash
php artisan route:list
```

Anda harusnya melihat banyak route dengan prefix `api/`.

### Langkah 10: Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`.

---

## 🎯 Metode 2: Menggunakan Install Script (Unix/Linux/Mac)

Jika Anda menggunakan OS berbasis Unix, Anda bisa menggunakan script otomatis.

```bash
# 1. Extract zip
unzip laravel-api-starter.zip
cd laravel-api-starter

# 2. Beri izin eksekusi
chmod +x install.sh

# 3. Jalankan script
./install.sh nama-project-anda
```

Script ini akan otomatis melakukan clone, install dependency, setup database SQLite, dan seeding.

---

## 🧪 Cara Testing API

Setelah server berjalan, Anda bisa mengetesnya.

### Test Login (cURL)

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

Jika berhasil, Anda akan mendapatkan respon JSON berisi `access_token`.

---

## ⚠️ Masalah Umum (Troubleshooting)

### 1. Routes Tidak Muncul di `php artisan route:list`

**Solusi:** Cek file `bootstrap/app.php`. Pastikan baris ini ada:

```php
api: __DIR__.'/../routes/api.php',
```

### 2. Error "Field 'id' doesn't have a default value"

**Solusi:** Ini biasanya karena masalah `PersonalAccessToken`. Cek `app/Providers/AppServiceProvider.php` dan pastikan `Sanctum::usePersonalAccessTokenModel(...)` ada di method `boot()`.

### 3. Error "Target [AuthRepositoryInterface] is not instantiable"

**Solusi:** Anda belum mendaftarkan Repository di Provider. Cek `bootstrap/providers.php` dan pastikan `App\Providers\RepositoryServiceProvider::class` terdaftar.

### 4. Permission Denied pada SQLite

**Solusi:** Berikan izin tulis ke folder database:

```bash
chmod 664 database/database.sqlite
chmod 775 database
```

---

## 📚 Langkah Selanjutnya

1. Pelajari **[GUIDE.md](GUIDE.md)** untuk cara membuat modul baru.
2. Pelajari **[ROLES-AND-PERMISSIONS.md](ROLES-AND-PERMISSIONS.md)** untuk manajemen hak akses.
3. Import `postman_collection.json` ke Postman untuk testing lengkap.

---

**Selamat Coding! 🚀**
