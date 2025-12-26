# ✉️ Verifikasi Email (Email Verification)

Sistem verifikasi email standar Laravel untuk memastikan validitas alamat email pengguna.

---

## 🚀 Status Fitur

Sistem ini menggunakan fitur bawaan Laravel `MustVerifyEmail`.

Fitur yang tersedia:

-   Link verifikasi otomatis saat registrasi.
-   Middleware `verified` untuk memproteksi route.
-   Endpoint untuk kirim ulang email verifikasi.

---

## 📖 Cara Implementasi

Untuk mengaktifkan fitur ini, ikuti langkah berikut:

### 1. Update Model User

Implementasikan interface `MustVerifyEmail` di model `User`.

```php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    // ...
}
```

### 2. Proteksi Route yang Membutuhkan Verifikasi

Gunakan middleware `verified` pada route yang ingin Anda lindungi.

```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // User hanya bisa akses ini jika email sudah diverifikasi
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/transaction', [TransactionController::class, 'store']);
});
```

### 3. Register & Pengiriman Email

Saat user registrasi, Laravel akan otomatis mengirim notifikasi jika Anda menggunakan `event(new Registered($user));`, atau panggil manual:

```php
// Di dalam AuthController/RegisterController
$user = User::create([ ... ]);

// Kirim notifikasi verifikasi
$user->sendEmailVerificationNotification();

return response()->json(['message' => 'Registrasi berhasil. Silakan cek email Anda.']);
```

---

## 🔧 Endpoint API

Kami telah menyediakan endpoint untuk memverifikasi email (jika menggunakan API-based verification).

| Method | Endpoint                               | Deskripsi                                           |
| :----- | :------------------------------------- | :-------------------------------------------------- |
| `POST` | `/api/email/verification-notification` | Kirim ulang email verifikasi. Route ini butuh auth. |
| `GET`  | `/api/email/verify/{id}/{hash}`        | Link verifikasi yang diklik user dari email.        |

> **Catatan:** Untuk API, Anda perlu menangani verifikasi di frontend atau membiarkan link mengarah ke endpoint backend yang kemudian me-redirect ke halaman sukses di frontend.

---

## ⚙️ Konfigurasi SMTP

Agar email terkirim, pastikan `.env` sudah diatur:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_google_anda
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🎯 Kegunaan

1.  **Cegah Akun Palsu**: Memastikan user menggunakan email asli.
2.  **Keamanan**: Memastikan user memiliki akses ke email tersebut.
3.  **Komunikasi**: Menjamin email user valid untuk pengiriman notifikasi penting.
