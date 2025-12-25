# 🚀 Laravel API Starter Pack

Starter pack Laravel API dengan Repository Pattern, Sanctum Authentication, Spatie Permission, dan fitur-fitur production-ready lainnya.

## 📦 Tech Stack

-   **Laravel**: 12.x
-   **PHP**: 8.2+
-   **Authentication**: Laravel Sanctum
-   **Authorization**: Spatie Laravel Permission
-   **Architecture**: Repository Pattern
-   **UUID**: Primary Key

## ✨ Features

### 🖥️ Frontend (New)

-   ✅ **Vue.js 3 SPA** - Located in `frontend/frontend-starter`
-   ✅ **Pinia** - State Management
-   ✅ **TailwindCSS** - Styling framework
-   ✅ **Authentication** - Integrated with API v1
-   ✅ **Admin Panel** - User management interface

### 🔐 Core Features

-   ✅ Authentication (Login, Register, Logout, Me)
-   ✅ Role & Permission Management (Spatie)
-   ✅ Repository Pattern
-   ✅ UUID as Primary Key
-   ✅ Soft Deletes
-   ✅ API Resources & Pagination
-   ✅ Form Request Validation
-   ✅ Standardized API Response
-   ✅ Search Functionality
-   ✅ CORS Configuration

### 🚀 Advanced Features

-   ✅ **Rate Limiting** - 8 pre-configured limiters (anti brute-force, anti-spam)
-   ✅ **API Versioning** - Support `/api/v1`, `/api/v2`, etc
-   ✅ **Activity Logging** - Helper siap pakai `ActivityLogger`
-   ✅ **File Upload Handler** - Service `FileUploadService` aman dan mudah
-   ✅ **Email Queue** - Konfigurasi production-ready untuk email
-   ✅ **Email Verification** - Verify user email
-   ✅ **Password Reset** - Forgot password functionality
-   ✅ **Refresh Token** - JWT-style token refresh

---

## 📥 Panduan Instalasi (Installation)

### 1. Clone & Install

```bash
# Clone project
git clone https://github.com/username/laravel-api-starter.git
cd laravel-api-starter

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Setup Database

Pastikan sudah membuat database, lalu update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Setup Storage & Queue

Penting untuk fitur upload file dan pengiriman email.

```bash
# Link storage folder (Wajib untuk File Upload)
php artisan storage:link

# Setup Queue (Untuk email performa tinggi)
# Di .env ubah:
QUEUE_CONNECTION=database
```

Jika menggunakan `QUEUE_CONNECTION=database`, jalankan:

```bash
php artisan queue:table
php artisan migrate
```

### 4. Run Migrations & Seeders

Jalankan perintah ini untuk membuat tabel dan data dummy awal (User Admin & Super Admin).

```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Aplikasi

```bash
php artisan serve
# Aplikasi akan jalan di: http://localhost:8000
# Base API URL: http://localhost:8000/api/v1
```

Jangan lupa jalankan queue worker di terminal terpisah jika menggunakan email/jobs:

```bash
php artisan queue:work
```

---

## 🔑 Default Credentials

-   **Super Admin**: `admin@example.com` / `password`
-   **Admin**: `user@example.com` / `password`

---

## 📖 Panduan Penggunaan (Usage Guide)

### 1. File Upload (New!)

Gunakan `FileUploadService` untuk handle upload dengan aman.

```php
use App\Services\FileUploadService;

public function updateAvatar(Request $request)
{
    if ($request->hasFile('avatar')) {
        // Upload file ke folder 'avatars' di storage public
        $path = FileUploadService::upload($request->file('avatar'), 'avatars');

        // Simpan path ke database
        $user->update(['avatar' => $path]);
    }
}
```

### 2. Activity Logging (New!)

Catat aktivitas user dengan mudah menggunakan helper `ActivityLogger`.

```php
use App\Helpers\ActivityLogger;

// Log aktivitas sederhana
ActivityLogger::log('create', 'Membuat product baru');

// Log dengan detail
ActivityLogger::log(
    'update',
    'Update harga product',
    'Product', // Model name
    $product->id, // Model ID
    ['old_price' => 5000, 'new_price' => 6000] // Properties tambahan
);
```

### 3. Mengirim Email dengan Queue

Gunakan class `App\Mail\WelcomeMail` yang sudah implement `ShouldQueue` agar aplikasi tidak loading lama saat kirim email.

```php
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

// Email akan dikirim di background (via Queue)
Mail::to($user->email)->send(new WelcomeMail($user));
```

Pastikan worker jalan: `php artisan queue:work`

### 4. Menambah Module Baru (Contoh: Product)

Ikuti langkah standar Repository Pattern:

1.  **Model**: `php artisan make:model Product -m`
2.  **Repository Interface**: Buat `App/Interfaces/ProductRepositoryInterface.php`
3.  **Repository Implementation**: Buat `App/Repositories/ProductRepository.php`
4.  **Service Provider**: Daftarkan di `RepositoryServiceProvider` binding-nya.
5.  **Controller**: Inject Interface ke Constructor Controller.
6.  **Route**: Daftarkan di `routes/v1.php`

---

## 🏗️ Project Structure

```
app/
├── Helpers/
│   └── ActivityLogger.php   # Helper logging
├── Services/
│   └── FileUploadService.php # Service upload
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Interfaces/             # Contracts
├── Repositories/           # Business Logic
├── Models/
├── Traits/
└── Providers/
```

## 🎯 Best Practices

1.  **Production Readiness**:
    -   Selalu gunakan `QUEUE_CONNECTION=redis` atau `database` di production.
    -   Jangan lupa `php artisan config:cache` dan `route:cache` saat deploy.
2.  **Security**:
    -   Gunakan `Sanctum` untuk token API.
    -   Validasikan input user dengan `FormRequest`.

## 📄 License

MIT License

---

**Happy Coding! 🚀**
