# 🔐 Panduan Middleware & Permission

Sistem manajemen hak akses (Role & Permission) di aplikasi ini menggunakan library **Spatie Laravel Permission** yang telah terintegrasi dengan middleware Laravel.

---

## 🏗️ Middleware yang Tersedia

Middleware berikut sudah didaftarkan di `bootstrap/app.php` dan siap digunakan:

| Alias                | Class Middleware             | Kegunaan                                                     |
| :------------------- | :--------------------------- | :----------------------------------------------------------- |
| `role`               | `RoleMiddleware`             | Membatasi akses berdasarkan **Role** (e.g. admin)            |
| `permission`         | `PermissionMiddleware`       | Membatasi akses berdasarkan **Permission** (e.g. edit users) |
| `role_or_permission` | `RoleOrPermissionMiddleware` | Membolehkan jika punya **salah satu**: Role atau Permission  |

---

## 1️⃣ Melindungi Route berdasarkan Role

### Single Role

Hanya user dengan role tertentu yang boleh akses.

```php
// Hanya user dengan role 'admin' yang bisa akses
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

### Multiple Roles (OR)

User dengan role 'admin' ATAU 'super-admin' boleh akses. Gunakan tanda pipa `|`.

```php
Route::middleware(['auth:sanctum', 'role:admin|super-admin'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

---

## 2️⃣ Melindungi Route berdasarkan Permission

Ini lebih fleksibel daripada Role. Kita membolehkan aksi spesifik.

### Single Permission

```php
// Hanya user yang punya izin 'view users'
Route::middleware(['auth:sanctum', 'permission:view users'])->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});
```

### Multiple Permissions (AND)

User harus memiliki **SEMUA** permission yang didaftarkan.

```php
// User HARUS punya 'view users' DAN 'edit users'
Route::middleware(['auth:sanctum', 'permission:view users', 'permission:edit users'])->group(function () {
    // ...
});
```

### Multiple Permissions (OR) - dengan Pipe

User cukup punya **SALAH SATU** permission.

```php
// User punya 'view users' ATAU 'edit users'
Route::middleware(['auth:sanctum', 'permission:view users|edit users'])->group(function () {
    // ...
});
```

---

## 3️⃣ Kombinasi Role ATAU Permission

Terkadang kita ingin membolehkan Super Admin akses segalanya, atau user biasa yang punya izin khusus.

```php
// Boleh akses jika dia 'admin' ATAU punya izin 'manage users'
Route::middleware(['auth:sanctum', 'role_or_permission:admin|manage users'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

---

## 4️⃣ Menggunakan Middleware di Controller

Selain di route, Anda juga bisa define middleware di dalam constructor Controller. Ini berguna untuk kontrol lebih granular per method.

```php
class ProductController extends Controller
{
    public function __construct(ProductRepositoryInterface $repo)
    {
        // Permission beda-beda untuk setiap aksi
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only('store');
        $this->middleware('permission:edit products')->only('update');
        $this->middleware('permission:delete products')->only('destroy');
    }
}
```

---

## 5️⃣ Cek Permission di dalam Kode (Logic)

Anda bisa melakukan pengecekan manual di dalam method Controller atau Service menggunakan helper dari trait `HasRoles`.

```php
use Illuminate\Support\Facades\Auth;

public function delete($id)
{
    $user = Auth::user();

    // 1. Cek Role
    if ($user->hasRole('super-admin')) {
        return $this->forceDelete($id);
    }

    // 2. Cek Permission
    if ($user->can('delete products')) {
        return $this->softDelete($id);
    }

    abort(403, 'Unauthorized action.');
}
```

---

## 6️⃣ Assign Role & Permission ke User

Biasanya dilakukan di Admin Panel atau saat Seeding.

### Setup User Baru

```php
$user = User::create([...]);

// Beri Role
$user->assignRole('admin');

// Beri Permission Khusus (Optional)
$user->givePermissionTo('bypass payment');
```

### Sync (Reset & Set Baru)

Method `syncRoles` akan menghapus semua role lama dan menggantinya dengan yang baru.

```php
// User sekarang HANYA punya role editor
$user->syncRoles(['editor']);

// User sekarang HANYA punya permission view & edit
$user->syncPermissions(['view products', 'edit products']);
```

---

## 7️⃣ Debugging & Caching

Spatie Permission menggunakan cache untuk performa. Jika Anda mengubah permission di database secara manual (misal lewat phpMyAdmin), perubahan tidak akan langsung terlihat.

**Solusi:** Reset cache permission.

```bash
php artisan permission:cache-reset
```

### Tips Troubleshooting

Jika user merasa "seharusnya bisa akses tapi ditolak":

1.  Pastikan `guard_name` di tabel `roles` dan `permissions` adalah `web` (default Laravel Auth).
2.  Cek apakah user benar-benar punya role tersebut via `$user->getRoleNames()`.
3.  Jalankan command reset cache di atas.

---

## 📚 Referensi Lanjut

-   [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
-   [Laravel Authorization Docs](https://laravel.com/docs/authorization)
