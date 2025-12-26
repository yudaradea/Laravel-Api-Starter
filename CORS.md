# 🔒 Panduan Konfigurasi CORS

**Cross-Origin Resource Sharing (CORS)** adalah mekanisme keamanan browser yang membatasi akses resource dari domain berbeda. Document ini menjelaskan cara konfigurasi CORS di aplikasi ini.

---

## 📍 Lokasi File

File konfigurasi CORS ada di: **`config/cors.php`**

---

## 🎯 Contoh Konfigurasi

### 1️⃣ Development (Allow Localhost)

Konfigurasi ini membolehkan akses dari localhost (React, Vue, Vite, dll) saat development.

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',      // React default
        'http://localhost:5173',      // Vite default
        'http://localhost:8080',      // Vue default
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 2️⃣ Production (Specific Domains Only)

Di production, akses **HARUS** dibatasi hanya untuk domain frontend yang sah.

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://yourdomain.com',
        'https://www.yourdomain.com',
        'https://app.yourdomain.com',
        'https://admin.yourdomain.com',
    ],

    // ... sisa konfigurasi
```

### 3️⃣ Menggunakan Environment Variable (Recommended)

Agar fleksibel, sebaiknya simpan URL frontend di `.env`.

**File: `.env`**

```env
# Development
FRONTEND_URL=http://localhost:3000

# Production
# FRONTEND_URL=https://yourdomain.com
```

**File: `config/cors.php`**

```php
'allowed_origins' => [
    env('FRONTEND_URL', 'http://localhost:3000'),
],
```

---

## 📋 Penjelasan Opsi

| Opsi                   | Deskripsi                                                                       |
| :--------------------- | :------------------------------------------------------------------------------ |
| `paths`                | Array path yang akan dikenakan aturan CORS. Biasanya `api/*`.                   |
| `allowed_methods`      | HTTP Methods yang dibolehkan (e.g. `GET`, `POST`, `*`).                         |
| `allowed_origins`      | Daftar domain yang diizinkan mengakses API.                                     |
| `allowed_headers`      | Header request yang diizinkan (e.g. `Content-Type`, `Authorization`).           |
| `exposed_headers`      | Header response yang boleh dibaca oleh browser/frontend.                        |
| `max_age`              | Cache preflight request (OPTIONS) dalam detik.                                  |
| `supports_credentials` | Wajib `true` jika menggunakan authentication cookies/session (seperti Sanctum). |

> ⚠️ **PERINGATAN:** Jangan gunakan `allowed_origins => ['*']` jika `supports_credentials => true`. Ini tidak valid menurut spesifikasi CORS.

---

## 🔧 Cara Apply Perubahan

Setiap kali mengubah `config/cors.php` atau `.env`, Anda **WAJIB** membersihkan cache konfigurasi:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ❌ Troubleshooting Error CORS

### Error: "Access to fetch has been blocked by CORS policy"

Solusi:

1.  Periksa apakah URL Frontend Anda (misal `http://localhost:3000`) sudah ada di `allowed_origins`. **Ingat:** `http` dan `https` itu beda!
2.  Jalankan `php artisan config:clear`.
3.  Pastikan `supports_credentials` diset ke `true` jika frontend mengirim kredensial (cookies/auth header).

### Error: "Wildcard '\*' cannot be used in combination with 'credentials'"

Penyebab: Anda mengeset `supports_credentials => true` tapi `allowed_origins` menggunakan `*`.

Solusi: Ganti `*` dengan url spesifik frontend Anda.

❌ **SALAH:**

```php
'allowed_origins' => ['*'],
'supports_credentials' => true,
```

✅ **BENAR:**

```php
'allowed_origins' => ['http://localhost:3000'],
'supports_credentials' => true,
```

---

## 📝 Tes Manual dengan cURL

Anda bisa mengecek apakah server merespon dengan header CORS yang benar menggunakan terminal:

```bash
curl -H "Origin: http://localhost:3000" \
     -H "Access-Control-Request-Method: POST" \
     -X OPTIONS \
     --verbose \
     http://localhost:8000/api/login
```

**Respon yang Benar:** Harusnya ada header seperti `Access-Control-Allow-Origin: http://localhost:3000`.
