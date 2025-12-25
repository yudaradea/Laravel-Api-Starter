# 📝 Activity Logging System

Track dan audit semua aktivitas user dalam aplikasi Anda secara otomatis dan mudah.

## 📍 Database Schema

Table: `activity_logs`

| Column      | Type      | Description                                |
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

## 🚀 Implementation Status

✅ **Completed:**

-   Table migration & Model
-   `ActivityLogger` Helper class
-   Automatic IP & User Agent capture

## 📖 Cara Penggunaan (Usage)

Gunakan helper class `ActivityLogger` di Controller, Service, atau Observer Anda.

### 1. Simple Log

Hanya mencatat aksi dan deskripsi.

```php
use App\Helpers\ActivityLogger;

public function login() {
    // ... logic login ...
    ActivityLogger::log('login', 'User logged in successfully');
}
```

### 2. Detailed Log with Model

Mencatat perubahan pada Model tertentu.

```php
use App\Helpers\ActivityLogger;

public function update(Request $request, $id) {
    $product = Product::find($id);
    $product->update($request->all());

    ActivityLogger::log(
        'update', // Action
        'Product updated: ' . $product->name, // Description
        'Product', // Model Name
        $product->id, // Model ID
        ['old_price' => 5000, 'new_price' => 6000] // Properties (Optional)
    );
}
```

## 🔍 Retrieve Logs

Anda bisa mengambil data log menggunakan Eloquent biasa.

```php
use App\Models\ActivityLog;

// Get logs for specific user
$logs = ActivityLog::where('user_id', $userId)->latest()->get();

// Get logs for specific model
$productLogs = ActivityLog::where('model', 'Product')
    ->where('model_id', $productId)
    ->get();
```

## 🎯 Use Cases

1. **Security Audit** - Melacak siapa yang login dan dari IP mana.
2. **Debugging** - Mengetahui urutan kejadian sebelum error.
3. **Accountability** - Mengetahui siapa yang mengubah/menghapus data penting.
