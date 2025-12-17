# 🔐 Middleware & Permission Guide

## Middleware yang Tersedia

Starter pack ini sudah include middleware dari Spatie Permission yang sudah terdaftar di `bootstrap/app.php`:

```php
$middleware->alias([
    'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
]);
```

## 1️⃣ Protect Routes by Role

### Single Role
```php
// Hanya user dengan role 'admin' yang bisa akses
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

### Multiple Roles (OR)
```php
// User dengan role 'admin' ATAU 'super-admin' bisa akses
Route::middleware(['auth:sanctum', 'role:admin|super-admin'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

## 2️⃣ Protect Routes by Permission

### Single Permission
```php
// Hanya user dengan permission 'view users' yang bisa akses
Route::middleware(['auth:sanctum', 'permission:view users'])->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});
```

### Multiple Permissions (AND)
```php
// User harus punya SEMUA permission ini
Route::middleware(['auth:sanctum', 'permission:view users|edit users'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

## 3️⃣ Protect Routes by Role OR Permission

```php
// User dengan role 'admin' ATAU permission 'manage users' bisa akses
Route::middleware(['auth:sanctum', 'role_or_permission:admin|manage users'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

## 4️⃣ Apply Middleware di Controller

### Constructor Middleware
```php
<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
        
        // Apply middleware ke method tertentu
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only('store');
        $this->middleware('permission:edit products')->only('update');
        $this->middleware('permission:delete products')->only('destroy');
    }
    
    // ... methods
}
```

### Granular Permission per Method
```php
public function __construct(ProductRepositoryInterface $productRepository)
{
    $this->productRepository = $productRepository;
    
    // Berbeda-beda permission untuk setiap method
    $this->middleware('permission:view products')->only('index');
    $this->middleware('permission:view product detail')->only('show');
    $this->middleware('permission:create products')->only('store');
    $this->middleware('permission:update products')->only('update');
    $this->middleware('permission:delete products')->only('destroy');
}
```

## 5️⃣ Check Permission di Code

### Dalam Controller/Repository
```php
use Illuminate\Support\Facades\Auth;

public function someMethod()
{
    $user = Auth::user();
    
    // Check role
    if ($user->hasRole('admin')) {
        // User is admin
    }
    
    // Check permission
    if ($user->hasPermissionTo('edit products')) {
        // User can edit products
    }
    
    // Check multiple roles (OR)
    if ($user->hasAnyRole(['admin', 'super-admin'])) {
        // User is admin OR super-admin
    }
    
    // Check multiple permissions (AND)
    if ($user->hasAllPermissions(['view products', 'edit products'])) {
        // User has ALL these permissions
    }
    
    // Check role dan permission
    if ($user->hasRole('admin') && $user->hasPermissionTo('delete products')) {
        // User is admin AND can delete products
    }
}
```

### Dalam Blade (jika pakai views)
```php
@role('admin')
    // This is only visible to users with admin role
@endrole

@hasrole('admin|super-admin')
    // This is visible to admin OR super-admin
@endhasrole

@can('edit products')
    // This is only visible to users with 'edit products' permission
@endcan
```

## 6️⃣ Assign Role & Permission

### Saat Register/Create User
```php
// In AuthRepository atau UserRepository
$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
]);

// Assign role
$user->assignRole('user'); // Single role
$user->assignRole(['user', 'admin']); // Multiple roles

// Assign permission
$user->givePermissionTo('view products'); // Single permission
$user->givePermissionTo(['view products', 'edit products']); // Multiple
```

### Update User Role
```php
// Replace semua roles dengan role baru
$user->syncRoles(['admin']); 

// Add role (tidak menghapus role lama)
$user->assignRole('editor');

// Remove role
$user->removeRole('admin');
```

### Update User Permission
```php
// Replace semua permissions dengan permission baru
$user->syncPermissions(['view products', 'edit products']); 

// Add permission
$user->givePermissionTo('delete products');

// Remove permission
$user->revokePermissionTo('delete products');
```

## 7️⃣ Get User Roles & Permissions

```php
$user = Auth::user();

// Get all roles
$roles = $user->roles; // Collection

// Get role names
$roleNames = $user->getRoleNames(); // Collection of role names

// Get all permissions (direct + via roles)
$permissions = $user->getAllPermissions(); // Collection

// Get permission names
$permissionNames = $user->getPermissionNames(); // Collection

// Get direct permissions only (not via roles)
$directPermissions = $user->permissions; // Collection
```

## 8️⃣ API Response dengan Roles & Permissions

Di `UserResource.php` sudah include roles & permissions:

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'roles' => $this->whenLoaded('roles', function () {
            return $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    }),
                ];
            });
        }),
        'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
    ];
}
```

## 9️⃣ Create Custom Permission

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create permission
Permission::create(['name' => 'publish articles']);

// Create role
$role = Role::create(['name' => 'editor']);

// Assign permission to role
$role->givePermissionTo('publish articles');

// Or create role with permissions
$role = Role::create(['name' => 'editor']);
$role->givePermissionTo(['view articles', 'edit articles', 'publish articles']);
```

## 🔟 Real World Examples

### Example 1: E-commerce Admin
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    
    // Super Admin only
    Route::middleware('role:super-admin')->group(function () {
        Route::apiResource('user', UserController::class);
        Route::apiResource('role', RoleController::class);
    });
    
    // Admin or Super Admin
    Route::middleware('role:admin|super-admin')->group(function () {
        Route::apiResource('product', ProductController::class);
        Route::apiResource('category', CategoryController::class);
    });
    
    // Anyone with view orders permission
    Route::middleware('permission:view orders')->group(function () {
        Route::get('/order', [OrderController::class, 'index']);
        Route::get('/order/{id}', [OrderController::class, 'show']);
    });
    
    // Only users with process orders permission
    Route::middleware('permission:process orders')->group(function () {
        Route::put('/order/{id}/process', [OrderController::class, 'process']);
    });
});
```

### Example 2: Blog CMS
```php
Route::middleware('auth:sanctum')->group(function () {
    
    // Writers can create and edit their own posts
    Route::middleware('permission:write articles')->group(function () {
        Route::post('/article', [ArticleController::class, 'store']);
        Route::put('/article/{id}', [ArticleController::class, 'update']);
    });
    
    // Editors can publish articles
    Route::middleware('permission:publish articles')->group(function () {
        Route::put('/article/{id}/publish', [ArticleController::class, 'publish']);
    });
    
    // Admin can delete articles
    Route::middleware('role:admin')->group(function () {
        Route::delete('/article/{id}', [ArticleController::class, 'destroy']);
    });
});
```

### Example 3: Check Permission in Repository
```php
<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleRepository
{
    public function update(array $data, $id)
    {
        $article = Article::findOrFail($id);
        $user = Auth::user();
        
        // Check if user owns the article OR has admin role
        if ($article->user_id !== $user->id && !$user->hasRole('admin')) {
            return ResponseHelper::error(
                'Unauthorized to update this article', 
                null, 
                403
            );
        }
        
        $article->update($data);
        
        return ResponseHelper::success(
            new ArticleResource($article),
            'Article updated successfully'
        );
    }
}
```

## 📝 Default Roles & Permissions

Starter pack ini sudah include 3 roles default:

### Super Admin
- Has ALL permissions
- Can do anything

### Admin
- view users
- create users
- edit users
- view roles
- view permissions

### User
- view users (read only)

Anda bisa modify di `database/seeders/RoleSeeder.php`

## 🔄 Clear Permission Cache

Jika role/permission tidak update, clear cache:

```bash
php artisan permission:cache-reset
```

## 📚 Learn More

- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)
- [Laravel Authorization](https://laravel.com/docs/authorization)

---

**Happy Coding! 🚀**
