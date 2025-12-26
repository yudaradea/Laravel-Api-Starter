# 📦 Manajemen Versi API (API Versioning)

Aplikasi ini menggunakan strategi versioning berbasis URL untuk memastikan stabilitas dan _backward compatibility_ bagi klien yang menggunakan API.

Versi saat ini adalah: **v1**

---

## 📍 Struktur URL

Semua request ke API **WAJIB** menggunakan prefix versi:

```
http://domain.com/api/v1/{endpoint}
```

### Contoh Request Valid:

-   `GET /api/v1/user`
-   `POST /api/v1/login`
-   `GET /api/v1/products`

### Mengapa Versioning Penting?

1.  **Stabilitas**: Jika Anda ingin merubah struktur response secara drastis (Breaking Changes), Anda bisa membuat `v2` tanpa merusak aplikasi klien yang masih menggunakan `v1`.
2.  **Masa Transisi**: Memberikan waktu bagi developer frontend/mobile untuk migrasi ke versi baru secara bertahap.

---

## 🚀 Cara Menambah Versi Baru (Misal: v2)

Jika suatu saat Anda perlu membuat versi 2 (v2), ikuti langkah-langkah berikut:

### 1. Buat File Routes Baru

Duplikasi `routes/v1.php` menjadi `routes/v2.php`.

```bash
cp routes/v1.php routes/v2.php
```

### 2. Update Bootstrap App

Buka `bootstrap/app.php` dan daftarkan route baru tersebut:

```php
->withRouting(
    // ...
    then: function () {
        // Route V1 (Existing)
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(base_path('routes/v1.php'));

        // Route V2 (New)
        Route::prefix('api/v2')
            ->middleware('api')
            ->group(base_path('routes/v2.php'));
    },
)
```

### 3. Buat Namespace Controller Baru

Sebaiknya pisahkan logika Controller v1 dan v2.

1.  Buat folder `app/Http/Controllers/Api/V2`
2.  Salin controller yang berubah dari V1 ke V2.
3.  Update namespace di controller V2 menjadi `App\Http\Controllers\Api\V2`.

### 4. Update Route v2.php

Di dalam `routes/v2.php`, arahkan endpoint ke Controller V2 yang baru.

```php
use App\Http\Controllers\Api\V2\ProductController;

Route::apiResource('products', ProductController::class);
```

---

## 💡 Tips Versioning

-   **Jangan Ubah v1**: Setelah rilis ke production, **JANGAN** pernah mengubah response structure di `v1`. Jika butuh perubahan, buat `v2`.
-   **Minor Changes**: Jika hanya menambah field baru (non-breaking), Anda tidak perlu membuat versi baru. Cukup tambahkan di `v1`.
-   **Deprecation**: Jika `v1` akan dimatikan, beri peringatan lewat header `Warning: 299 - "This API version is deprecated"` atau umumkan ke pengguna API Anda.
