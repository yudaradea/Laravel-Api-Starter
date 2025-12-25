# 📦 API Versioning

API ini menggunakan versioning untuk memastikan backward compatibility. Saat ini versi aktif adalah `v1`.

## 📍 URL Structure

Semua request ke API harus menggunakan prefix version:

```
http://domain.com/api/v1/{endpoint}
```

Contoh:

-   `GET /api/v1/user`
-   `POST /api/v1/login`

## 🚀 Menambah Versi Baru (e.g. v2)

1.  Buat file routes baru `routes/v2.php`.
2.  Update `bootstrap/app.php`:

```php
->withRouting(
    // ...
    then: function () {
       Route::prefix('api/v1')
           ->middleware('api')
           ->group(base_path('routes/v1.php'));

       Route::prefix('api/v2')
           ->middleware('api')
           ->group(base_path('routes/v2.php'));
    },
)
```

3.  Buat Namespace Controller baru `App\Http\Controllers\Api\V2`.
