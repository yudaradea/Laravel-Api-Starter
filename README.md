# 🚀 Laravel API Starter Pack

Selamat datang di **Laravel API Starter Pack**!
Ini adalah starter pack production-ready yang dirancang untuk mempercepat pengembangan API Laravel Anda dengan fitur-fitur modern dan best practices.

Dilengkapi dengan **Authentication** (Sanctum), **Authorization** (Spatie), **Repository Pattern**, dan sudah terintegrasi dengan **Frontend Vue.js + Tailwind**.

---

## 📚 Dokumentasi Lengkap

Kami telah menyediakan dokumentasi detail untuk setiap aspek aplikasi ini:

### 🎓 Memulai (Getting Started)

-   **[Panduan Instalasi](INSTALLATION.md)** - Mulai dari sini! Cara install step-by-step.
-   **[Panduan Pengembangan (Guide)](GUIDE.md)** - Tutorial cara membuat module/fitur baru (CRUD) dengan cepat.
-   **[Quick Commands](QUICK-COMMANDS.md)** - Daftar perintah artisan penting yang sering dipakai.

### 🔑 Fitur Utama (Core Features)

-   **[Roles & Permissions](ROLES-AND-PERMISSIONS.md)** - Penjelasan lengkap sistem role (Super Admin, Admin, User).
-   **[Authentication Flow](REFRESH-TOKEN.md)** - Penjelasan login, refresh token, dan keamanan.
-   **[Middleware](MIDDLEWARE.md)** - Daftar middleware yang tersedia untuk proteksi route.
-   **[API Versioning](API-VERSIONING.md)** - Cara mengelola versi API (v1, v2).

### 🛠️ Fitur Tambahan (Utilities)

-   **[File Upload](FILE-UPLOAD.md)** - Cara upload file dengan aman menggunakan Service.
-   **[Activity Log](ACTIVITY-LOG.md)** - Cara mencatat aktivitas user.
-   **[Rate Limiting](RATE-LIMITING.md)** - Proteksi API dari spam/brute-force.
-   **[CORS](CORS.md)** - Konfigurasi Cross-Origin Resource Sharing.

### 🆘 Bantuan (Support)

-   **[Troubleshooting](TROUBLESHOOTING.md)** - Solusi untuk masalah umum yang sering ditemui.
-   **[Struktur Project](STRUCTURE.md)** - Penjelasan struktur folder dan architecture.

---

## 📦 Tech Stack

Aplikasi ini dibangun di atas teknologi modern yang stabil:

-   **Backend**: Laravel 12.x, PHP 8.2+
-   **Database**: MySQL 8.0+ / SQLite
-   **Architecture**: Repository Pattern
-   **Authentication**: Laravel Sanctum (Token based)
-   **Authorization**: Spatie Laravel Permission
-   **Frontend**: Vue.js 3, Pinia, TailwindCSS

---

## ✨ Fitur Sekilas

### Core Features

-   ✅ **Secure Auth**: Login, Register, Logout, Profile Management.
-   ✅ **Role Management**: Super Admin, Admin, User (Extensible).
-   ✅ **Repository Pattern**: Kode lebih rapi dan maintainable.
-   ✅ **UUID**: Menggunakan UUID sebagai Primary Key untuk keamanan lebih baik.
-   ✅ **Standardized Response**: Format JSON response yang konsisten (`success`, `message`, `data`).

### Advanced Features

-   ✅ **Rate Limiting**: 8 jenis limiter untuk berbagai use-case.
-   ✅ **Activity Logger**: Helper untuk mencatat audit trail.
-   ✅ **File Upload Service**: Standardisasi upload file ke storage.
-   ✅ **Email Queue**: Konfigurasi job queue yang siap production.

---

## 🚀 Instalasi Cepat

Untuk panduan detail, lihat **[INSTALLATION.md](INSTALLATION.md)**.

```bash
# 1. Clone & Install
git clone https://github.com/username/project.git
composer install

# 2. Setup Env
cp .env.example .env
php artisan key:generate

# 3. Setup Database (update .env first)
php artisan migrate:fresh --seed

# 4. Run!
php artisan serve
```

---

## 🔑 Default Credentials

Gunakan akun ini untuk login pertama kali:

| Role            | Email               | Password   |
| --------------- | ------------------- | ---------- |
| **Super Admin** | `admin@example.com` | `password` |
| **User Biasa**  | `user@example.com`  | `password` |

---

## 🏗️ Struktur Project

```
app/
├── Helpers/            # Helper function (e.g. ActivityLogger)
├── Http/
│   ├── Controllers/    # Handle request
│   ├── Requests/       # Form Validations
│   └── Resources/      # API Transformers (JSON Response)
├── Interfaces/         # Contracts untuk Repository
├── Models/             # Eloquent Models
├── Repositories/       # Logic database query
└── Services/           # Logic bisnis yang kompleks
```

Lihat detail di **[STRUCTURE.md](STRUCTURE.md)**.

---

## 🤝 Kontribusi

Silakan fork repository ini dan buat Pull Request jika ingin berkontribusi. Pastikan coding style mengikuti standar PSR-12.

## 📄 Lisensi

MIT License. Bebas digunakan untuk project personal maupun komersial.

---

**Selamat Berkarya! 🚀**
