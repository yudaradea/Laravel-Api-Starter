# 🔑 Reset Password (Lupa Password)

Sistem pemulihan akun yang aman menggunakan email notification, built-in dari Laravel.

---

## 🚀 Status Fitur

Fitur ini menggunakan mekanisme bawaan Laravel `Illuminate\Foundation\Auth\ResetsPasswords`.

Fitur standar meliputi:

-   Endpoint `forgot-password` untuk mengirim link reset.
-   Endpoint `reset-password` untuk mengubah password dengan token.
-   Rate limiting untuk mencegah spam.

---

## 📖 Cara Penggunaan

### 1. Request Reset Link (Lupa Password)

User mengirim email mereka untuk mendapatkan link reset.

**Endpoint:** `POST /api/forgot-password`

**Request Body:**

```json
{
    "email": "user@example.com"
}
```

**Response:**

```json
{
    "status": "We have emailed your password reset link."
}
```

### 2. Reset Password (Set Password Baru)

Setelah user mengklik link di email (yang mengarah ke frontend), frontend harus mengambil token dari URL dan mengirimkannya kembali ke backend.

**Endpoint:** `POST /api/reset-password`

**Request Body:**

```json
{
    "token": "token-dari-url-email",
    "email": "user@example.com",
    "password": "password-baru",
    "password_confirmation": "password-baru"
}
```

**Response:**

```json
{
    "status": "Your password has been reset."
}
```

---

## 🔧 Konfigurasi

### 1. Database

Pastikan tabel `password_reset_tokens` sudah dibuat (biasanya sudah ada di migrasi default Laravel).

```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

### 2. Expiry Time

Anda bisa mengatur berapa lama token valid di `config/auth.php`.

```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60, // menit
        'throttle' => 60,
    ],
],
```

---

## 🛡️ Fitur Keamanan

1.  **Token Expiration**: Token otomatis kadaluarsa setelah 60 menit (configurable).
2.  **Rate Limiting**: Mencegah spam request reset link.
3.  **Single Use**: Token tidak bisa digunakan kembali setelah sukses reset.
4.  **Email Verification**: Sistem memverifikasi kecocokan email dan token.

---

## 📝 Catatan Implementasi Frontend

1.  Buat halaman "Lupa Password" -> Form input email.
2.  Buat halaman "Reset Password" (misal: `/reset-password/{token}?email=...`) -> Form input password baru.
3.  Pastikan URL di email mengarah ke halaman frontend yang benar. Anda mungkin perlu menyesuaikan `App\Notifications\ResetPassword` jika URL frontend berbeda dari default Laravel.
