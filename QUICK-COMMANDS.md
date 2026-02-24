# Referensi Perintah Cepat (Quick Commands)

Berikut adalah kumpulan perintah _artisan_ yang sering digunakan untuk mempermudah development.

---

## 🔑 Manajemen Role & User

### Jadikan Super Admin

Memberikan role `super-admin` ke user tertentu (akses penuh).

```bash
php artisan user:make-superadmin {email}
```

**Contoh:**

```bash
php artisan user:make-superadmin admin@example.com
```

**Efek:**

-   Menghapus semua role yang ada pada user tersebut.
-   Memberikan role `super-admin`.
-   Menampilkan role terbaru user.

> **Penting:** User harus **logout dan login ulang** agar token baru memuat permissions super-admin.

### Buat Profil untuk Semua User

Gunakan jika ada user yang belum memiliki profil (misal data dari seeder lama).

```bash
php artisan users:ensure-profiles
```

---

## 🛡️ Setup Roles & Permissions

### Reset & Seed Permissions

Perintah ini akan membuat ulang semua roles dan permissions.

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

**Efek:**

-   Membuat permission baru (total +- 17 permissions).
-   Membuat 3 role utama: `super-admin`, `admin`, `user`.
-   Assign permission ke role yang sesuai.

### Clear Permission Cache

Jalankan ini jika Anda mengubah permission tapi tidak berefek.

```bash
php artisan permission:cache-reset
```

---

## 💻 Development Server

### Frontend (Vue.js)

```bash
cd frontend/frontend-starter
npm run dev
```

### Backend (Laravel)

```bash
php artisan serve
```

---

## 🔍 Testing API (Manual)

Contoh testing endpoint dengan cURL.

```bash
# Cek info user saat ini (termasuk roles)
curl http://localhost:8000/api/me \
  -H "Authorization: Bearer TOKEN_ANDA"

# Cek semua roles yang tersedia
curl http://localhost:8000/api/roles \
  -H "Authorization: Bearer TOKEN_ANDA"

# Cek capabilities (permissions) per role
curl http://localhost:8000/api/roles/capabilities \
  -H "Authorization: Bearer TOKEN_ANDA"
```

---

## 👤 Daftar User Default (Seed)

Jika Anda menjalankan `php artisan migrate --seed`:

| Email               | Role          | Password Default |
| :------------------ | :------------ | :--------------- |
| `admin@example.com` | `super-admin` | `password`       |
| `user@example.com`  | `user`        | `password`       |

> **Catatan:** Pastikan `admin@example.com` memiliki role `super-admin`. Jika ragu, jalankan perintah `user:make-superadmin`.

---

## ❓ Troubleshooting

### User role tidak berubah di frontend?

**Solusi:** User wajib **Logout** dan **Login** lagi. Token JWT/Sanctum menyimpan klaim role saat login, jadi perubahan role di tengah sesi tidak akan terbaca sampai token diperbarui.

### Profil tidak muncul?

**Solusi:** Jalankan `php artisan users:ensure-profiles`.

### Permission Error (403 Forbidden)?

**Solusi:**

1.  Jalankan `php artisan permission:cache-reset`.
2.  Pastikan user punya role yang benar: cek di database atau via `php artisan tinker`.
3.  Pastikan route menggunakan middleware yang benar (misal `can:manage-users`).

### Cara cek role user via terminal?

```bash
php artisan tinker
> User::where('email', 'admin@example.com')->first()->roles->pluck('name');
```
