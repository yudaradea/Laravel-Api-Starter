# 📝 Activity Logging System

Fitur `Activity Logging` memungkinkan Anda untuk melacak dan mengaudit semua aktivitas pengguna dalam aplikasi secara otomatis. Fitur ini sangat berguna untuk debugging dan keamanan.

## 📍 Struktur Database

Table: `activity_logs`

| Kolom       | Tipe      | Deskripsi                                  |
| ----------- | --------- | ------------------------------------------ |
| id          | uuid      | Primary key                                |
| user_id     | uuid      | User yang melakukan aksi                   |
| action      | string    | Jenis aksi (login, create, update, delete) |
| model       | string    | Model yang diubah (User, Product, dll)     |
| model_id    | uuid      | ID dari model                              |
| description | text      | Deskripsi aktivitas                        |
| properties  | json      | Detail data (Old/new values)               |
| ip_address  | string    | IP address user                            |
| user_agent  | string    | Browser/device info                        |
| created_at  | timestamp | Waktu aktivitas                            |

---

## 🚀 Status Implementasi

✅ **Selesai:**

-   Migration & Model Tabel
-   `ActivityLogger` Helper class
-   Tangkap IP & User Agent otomatis

---

## 📖 Cara Penggunaan

Gunakan helper class `ActivityLogger` di Controller, Service, atau Observer Anda.

### 1. Simple Log (Catatan Sederhana)

Hanya mencatat aksi dan deskripsi tanpa data model.

```php
use App\Helpers\ActivityLogger;

public function login() {
    // ... logic login ...
    ActivityLogger::log('login', 'User berhasil login');
}
```

### 2. Detailed Log (Dengan Model)

Mencatat perubahan pada Model tertentu, lengkap dengan data sebelum dan sesudahnya.

```php
use App\Helpers\ActivityLogger;

public function update(Request $request, $id) {
    $product = Product::find($id);
    $oldData = $product->toArray(); // Simpan data lama

    $product->update($request->all());

    ActivityLogger::log(
        'update',                   // Action
        'Update produk: ' . $product->name, // Description
        'Product',                  // Model Name
        $product->id,               // Model ID
        [
            'old' => $oldData,
            'new' => $product->toArray()
        ]                           // Properties (Data Perubahan)
    );
}
```

### 3. Otomatisasi dengan Observer (Recommended)

Agar tidak perlu menulis kode log di setiap controller, gunakan **Observer**.

1.  Buat Observer:

    ```bash
    php artisan make:observer ProductObserver --model=Product
    ```

2.  Isi Observer:


    ```php
    public function updated(Product $product): void
    {
        ActivityLogger::log(
            'update',
            "Product {$product->name} updated",
            'Product',
            $product->id,
            ['changes' => $product->getChanges()]
        );
    }
    ```

3.  Daftarkan di `AppServiceProvider`:

    ```php
    use App\Models\Product;
    use App\Observers\ProductObserver;

    public function boot(): void
    {
        Product::observe(ProductObserver::class);
    }
    ```

---

## 🔍 Melihat Log

Anda bisa mengambil data log menggunakan Eloquent biasa.

```php
use App\Models\ActivityLog;

// Ambil log user tertentu
$logs = ActivityLog::where('user_id', $userId)->latest()->get();

// Ambil log model tertentu (misal: histori perubahan produk A)
$productLogs = ActivityLog::where('model', 'Product')
    ->where('model_id', $productId)
    ->get();
```

---

## 🎯 Kegunaan (Use Cases)

1.  **Audit Keamanan**: Melacak siapa yang login dan dari IP mana.
2.  **Debugging**: Mengetahui urutan kejadian sebelum error terjadi.
3.  **Akuntabilitas**: Mengetahui siapa yang mengubah atau menghapus data penting.

---

**Tips:**
Pastikan untuk membersihkan log yang sudah terlalu lama secara berkala agar database tidak penuh (misal: buat Scheduler untuk hapus log > 1 tahun).
