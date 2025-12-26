# 🔧 Panduan Troubleshooting (Masalah Umum)

Dokumen ini berisi kumpulan masalah yang sering ditemui saat instalasi atau pengembangan, beserta solusinya.

---

## ❌ Masalah 1: Routes API tidak muncul di `php artisan route:list`

### Gejala:

-   Sudah copy file `routes/api.php`
-   Sudah jalankan `php artisan install:api`
-   Tapi ketika cek `php artisan route:list`, routes API tidak ada.

### ✅ Solusi:

**Di Laravel 12 (atau konfigurasi custom), API routes tidak otomatis di-load.** Anda perlu mendaftarkannya secara manual.

#### Langkah 1: Update `bootstrap/app.php`

Buka file `bootstrap/app.php` dan pastikan ada baris `api: __DIR__.'/../routes/api.php'`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ← PENTING! Tambahkan baris ini
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ...
```

#### Langkah 2: Clear cache

```bash
php artisan route:clear
```

---

## ❌ Masalah 2: RepositoryServiceProvider Error

### Gejala:

-   Error: `Target [App\Interfaces\AuthRepositoryInterface] is not instantiable`
-   Repository tidak bisa di-inject ke Controller.

### ✅ Solusi:

Anda belum mendaftarkan Service Provider untuk Repository.

#### Langkah 1: Cek `bootstrap/providers.php`

Pastikan file ini memuat `RepositoryServiceProvider`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,  // ← Harus ada!
];
```

#### Langkah 2: Clear cache

```bash
php artisan config:clear
```

---

## ❌ Masalah 3: Error "Class 'App\Traits\UUID' not found"

### Gejala:

-   Error saat menjalankan migration atau membuat model.
-   Pesan error menyebutkan trait UUID tidak ditemukan.

### ✅ Solusi:

Ini biasanya masalah autoloading composer.

#### Langkah 1: Regenerate autoload

```bash
composer dump-autoload
```

---

## ❌ Masalah 4: Halaman Dokumentasi 404 Not Found

### Gejala:

-   Akses ke `/dokumentasi` atau `/dokumentasi/{slug}` mengembalikan 404.

### ✅ Solusi:

#### Langkah 1: Clear Cache

```bash
php artisan route:clear
php artisan view:clear
```

#### Langkah 2: Cek keberadaan file

Pastikan file markdown (misal `README.md`) ada di root folder project.

---

## ❌ Masalah 5: Error "Field 'id' doesn't have a default value"

### Gejala:

-   Error saat login: `SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value`
-   Terjadi di tabel `personal_access_tokens`.

### ✅ Solusi:

Ini karena Sanctum default-nya menggunakan Auto Increment ID, sedangkan kita menggunakan UUID.

#### Langkah 1: Update `AppServiceProvider.php`

Pastikan Anda memberitahu Sanctum untuk menggunakan model custom kita:

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
}
```

#### Langkah 2: Cek Model `PersonalAccessToken`

Pastikan file `app/Models/PersonalAccessToken.php` menggunakan trait `UUID`.

---

## ❌ Masalah 6: Database Connection Refused

### Gejala:

-   Error: `SQLSTATE[HY000] [2002] Connection refused`

### ✅ Solusi:

**Jika pakai SQLite:**

1. Pastikan file database ada: `touch database/database.sqlite`
2. Pastikan config `.env`: `DB_CONNECTION=sqlite` (dan hapus DB_HOST dll).

**Jika pakai MySQL:**

1. Pastikan MySQL server running.
2. Cek username/password di `.env`.
3. Pastikan port (3306) tidak terblokir.

---

## ❌ Masalah 7: Unauthenticated (Token Tidak Valid)

### Gejala:

-   Akses endpoint API selalu return 401 Unauthenticated.

### ✅ Solusi:

1.  **Cek Header**: Pastikan formatnya `Authorization: Bearer <token>`.
2.  **Cek Environment**: Pastikan `SANCTUM_STATEFUL_DOMAINS` di `.env` sudah benar (biasanya biarkan default untuk API usage).
3.  **Clear Config**: `php artisan config:clear`.

---

## ❌ Masalah 8: CORS Error (Browser)

### Gejala:

-   Browser console merah: `Access to XMLHttpRequest has been blocked by CORS policy`.

### ✅ Solusi:

1.  Buka `config/cors.php`.
2.  Pastikan `allowed_origins` berisi domain frontend Anda (atau `['*']` untuk development).
3.  Pastikan `paths` mencakup `api/*`.

---

## 🔍 Tips Debugging Umum

Jika Anda mengalami error yang tidak ada di daftar ini:

1.  **Nyalakan Debug Mode**: Set `APP_DEBUG=true` di `.env`.
2.  **Cek Log**: Buka file `storage/logs/laravel.log`. Biasanya error detail ada di baris paling bawah.
3.  **Gunakan `dd()`**: Dump and Die variables di controller untuk memeriksa data.

```php
dd($request->all()); // Cek data input
```

---

**Masih buntu?**
Coba cari pesan error-nya di Google atau StackOverflow. Komunitas Laravel sangat besar dan kemungkinan besar orang lain pernah mengalami hal yang sama.
