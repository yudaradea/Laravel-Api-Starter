# 📝 Activity Logging System

Track dan audit semua aktivitas user dalam aplikasi Anda.

## 📍 Database Schema

Table: `activity_logs`

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key |
| user_id | uuid | User yang melakukan aksi |
| action | string | Jenis aksi (login, create, update, delete) |
| model | string | Model yang diubah (User, Product, dll) |
| model_id | uuid | ID dari model |
| description | text | Deskripsi aktivitas |
| properties | json | Old/new values |
| ip_address | string | IP address user |
| user_agent | string | Browser/device info |
| created_at | timestamp | Waktu aktivitas |

## 🚀 Implementation Status

✅ **Completed:**
- Activity logs table migration
- ActivityLog model with UUID
- Relationships (user)
- Scopes for filtering

🚧 **Coming Soon:**
- ActivityLogService for easy logging
- Automatic logging middleware
- API endpoints untuk view logs
- Dashboard untuk monitoring
- Export logs functionality

## 📖 Planned Usage

### Log User Activity
```php
use App\Services\ActivityLogService;

// After user login
ActivityLogService::log([
    'action' => 'login',
    'description' => 'User logged in',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

// After CRUD operation
ActivityLogService::log([
    'action' => 'created',
    'model' => 'Product',
    'model_id' => $product->id,
    'description' => 'Product created: ' . $product->name,
    'properties' => ['data' => $product->toArray()],
]);
```

### Retrieve Logs
```php
// Get user's activity logs
$logs = ActivityLog::byUser(auth()->id())
    ->latest()
    ->paginate(20);

// Get all login activities
$logins = ActivityLog::byAction('login')
    ->with('user')
    ->latest()
    ->get();

// Get activities for specific model
$productLogs = ActivityLog::byModel('Product')
    ->where('model_id', $productId)
    ->get();
```

## 🎯 Use Cases

1. **Security Audit** - Track who accessed what and when
2. **Compliance** - Maintain audit trail for regulations
3. **Debugging** - See what happened before an error
4. **User Analytics** - Understand user behavior
5. **Accountability** - Know who made changes

## 🔧 To Implement in Your Project

1. Run migration:
```bash
php artisan migrate
```

2. Use the ActivityLog model in your code:
```php
use App\Models\ActivityLog;

ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'created',
    'model' => 'User',
    'model_id' => $user->id,
    'description' => 'New user registered',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

---

**Note:** Fitur ini sudah include model dan migration. Implementation lengkap akan ditambahkan di update berikutnya.
