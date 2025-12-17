# 🔧 Troubleshooting Guide

## ❌ Problem: Routes API tidak muncul di `php artisan route:list`

### Gejala:
- Sudah copy file `routes/api.php`
- Sudah jalankan `php artisan install:api`
- Tapi ketika cek `php artisan route:list`, routes API tidak ada

### ✅ Solusi:

**Di Laravel 12, API routes tidak otomatis di-load!** Anda perlu register manual.

#### Langkah 1: Update `bootstrap/app.php`

Buka file `bootstrap/app.php` dan pastikan ada `api: __DIR__.'/../routes/api.php'`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ← PENTING! Tambahkan baris ini
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie Permission middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

#### Langkah 2: Clear cache

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

#### Langkah 3: Cek lagi

```bash
php artisan route:list
```

Sekarang routes API Anda akan muncul dengan prefix `/api`!

---

## ❌ Problem: RepositoryServiceProvider tidak terdaftar

### Gejala:
- Error: `Target [App\Interfaces\AuthRepositoryInterface] is not instantiable`
- Repository tidak bisa di-inject

### ✅ Solusi:

#### Langkah 1: Cek `bootstrap/providers.php`

Pastikan file ini ada dan berisi:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,  // ← Harus ada!
];
```

#### Langkah 2: Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ❌ Problem: Error "Class 'App\Traits\UUID' not found"

### Gejala:
- Error saat run migration atau create model
- UUID trait tidak ditemukan

### ✅ Solusi:

#### Langkah 1: Pastikan file ada

Cek apakah file `app/Traits/UUID.php` ada.

#### Langkah 2: Regenerate autoload

```bash
composer dump-autoload
```

---

## ❌ Problem: Error "Field 'id' doesn't have a default value" di personal_access_tokens

### Gejala:
- Error saat login: `SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value`
- Error terjadi di table `personal_access_tokens`

### ✅ Solusi:

Ini terjadi karena Sanctum belum tahu kalau kita pakai UUID untuk `personal_access_tokens`.

#### Langkah 1: Pastikan AppServiceProvider sudah benar

File `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use custom PersonalAccessToken model with UUID
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
    }
}
```

#### Langkah 2: Pastikan PersonalAccessToken model menggunakan UUID trait

File `app/Models/PersonalAccessToken.php`:

```php
<?php

namespace App\Models;

use App\Traits\UUID;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UUID;  // ← PENTING!

    protected $table = 'personal_access_tokens';
    public $incrementing = false;
    protected $keyType = 'string';
}
```

#### Langkah 3: Re-migrate

```bash
php artisan migrate:fresh --seed
```

#### Langkah 4: Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

#### Langkah 5: Test login

Sekarang login API akan berfungsi dengan UUID!

---

## ❌ Problem: Database connection refused

### Gejala:
- Error: `SQLSTATE[HY000] [2002] Connection refused`

### ✅ Solusi:

#### Untuk SQLite:

1. Pastikan file database ada:
```bash
touch database/database.sqlite
```

2. Update `.env`:
```env
DB_CONNECTION=sqlite
# Comment atau hapus yang ini:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

3. Clear config:
```bash
php artisan config:clear
```

#### Untuk MySQL:

1. Pastikan MySQL berjalan:
```bash
# Linux/Mac
sudo service mysql start

# atau
mysql.server start
```

2. Pastikan `.env` benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=password_anda
```

3. Create database:
```sql
CREATE DATABASE nama_database_anda;
```

---

## ❌ Problem: Token tidak valid / Unauthenticated

### Gejala:
- Response: `{"message": "Unauthenticated."}`
- Header authorization sudah benar

### ✅ Solusi:

#### Langkah 1: Cek format header

Pastikan format header benar:
```
Authorization: Bearer token_anda_disini
```

**BUKAN:**
- `Bearer: token_anda_disini` ❌
- `Token token_anda_disini` ❌

#### Langkah 2: Cek config Sanctum

File `config/sanctum.php`:
```php
'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
],
```

#### Langkah 3: Clear config

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ❌ Problem: CORS Error

### Gejala:
- Error di browser console: `Access to XMLHttpRequest has been blocked by CORS policy`

### ✅ Solusi:

#### Langkah 1: Install Laravel CORS (jika belum)

Laravel 12 sudah include CORS, tapi pastikan enabled.

#### Langkah 2: Update `config/cors.php`

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],  // Production: ganti dengan domain spesifik
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

#### Langkah 3: Jika masih error, tambahkan middleware

Di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);
})
```

---

## ❌ Problem: Validation Error Tidak Muncul

### Gejala:
- Form validation gagal tapi error message kosong

### ✅ Solusi:

#### Cek FormRequest

Pastikan method `authorize()` return `true`:
```php
public function authorize(): bool
{
    return true;  // ← Harus true!
}
```

---

## ❌ Problem: Role/Permission tidak bisa assign

### Gejala:
- Error: `Role does not exist`
- Role sudah di-seed

### ✅ Solusi:

#### Langkah 1: Clear permission cache

```bash
php artisan permission:cache-reset
```

#### Langkah 2: Re-seed

```bash
php artisan db:seed --class=RoleSeeder
```

#### Langkah 3: Cek guard_name

Pastikan guard_name di Role sesuai dengan User model guard (default: `web`).

---

## ❌ Problem: Soft Delete tidak bekerja

### Gejala:
- Data benar-benar terhapus dari database
- `deleted_at` tidak ter-set

### ✅ Solusi:

#### Pastikan Model menggunakan trait

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class YourModel extends Model
{
    use SoftDeletes;  // ← Harus ada!
}
```

#### Pastikan migration punya column deleted_at

```php
$table->softDeletes();
```

---

## 🔍 Debug Tips

### 1. Enable Debug Mode

Di `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### 2. Check Logs

```bash
tail -f storage/logs/laravel.log
```

### 3. Clear Everything

```bash
php artisan optimize:clear
```

Ini akan clear:
- Route cache
- Config cache
- View cache
- Event cache

### 4. Reinstall Dependencies

```bash
composer install
composer dump-autoload
```

### 5. Check PHP Version

```bash
php -v
```

Pastikan PHP >= 8.2 untuk Laravel 12.

---

## 📞 Masih Ada Masalah?

1. Cek error di `storage/logs/laravel.log`
2. Run `php artisan route:list` untuk lihat routes yang terdaftar
3. Run `php artisan config:show` untuk lihat konfigurasi
4. Pastikan semua file sudah di-copy dengan benar
5. Cek permissions folder `storage/` dan `bootstrap/cache/`

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

**Catatan:** Ini adalah masalah-masalah umum yang sering terjadi. Jika masalah Anda tidak ada di sini, cek dokumentasi Laravel resmi atau error message di log file.
