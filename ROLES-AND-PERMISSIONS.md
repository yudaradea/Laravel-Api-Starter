# Role & Permission System

## Overview

This application uses **Spatie Laravel Permission** package to manage roles and permissions. The system has three predefined roles with specific capabilities.

## Roles and Their Capabilities

### 1. Super Admin

**Full system access with unrestricted permissions**

#### CAN DO:

-   ✅ Manage all users (create, edit, delete)
-   ✅ **Assign roles to users**
-   ✅ Manage roles and permissions
-   ✅ View and edit any profile
-   ✅ Access all system features
-   ✅ Access dashboard

#### CANNOT DO:

-   ❌ N/A - Super Admin has all permissions

---

### 2. Admin

**User management without role/permission control**

#### CAN DO:

-   ✅ Manage users (create, edit, delete)
-   ✅ View any user profile
-   ✅ Edit user information (name, email)
-   ✅ Access dashboard

#### CANNOT DO:

-   ❌ **Assign roles to users** (only Super Admin can)
-   ❌ Manage roles and permissions
-   ❌ Change their own role

---

### 3. User

**Basic user with limited access**

#### CAN DO:

-   ✅ View and edit own profile
-   ✅ Change own password
-   ✅ Upload own avatar
-   ✅ Access dashboard

#### CANNOT DO:

-   ❌ Manage other users
-   ❌ View other user profiles
-   ❌ Assign roles
-   ❌ Delete users

---

## Available Permissions

The system defines the following permissions:

### User Management

-   `view users` - View list of users
-   `create users` - Create new users
-   `edit users` - Edit existing users
-   `delete users` - Delete users
-   `assign roles` - Assign roles to users (Super Admin only)

### Profile Management

-   `view own profile` - View own profile
-   `edit own profile` - Edit own profile
-   `view any profile` - View any user's profile

### Role & Permission Management

-   `view roles` - View available roles
-   `create roles` - Create new roles
-   `edit roles` - Edit existing roles
-   `delete roles` - Delete roles
-   `view permissions` - View available permissions
-   `assign permissions` - Assign permissions to roles

### General

-   `access dashboard` - Access the dashboard

---

## Frontend Implementation

### Role Checks in Vue Components

The auth store provides helpers for role checking:

```javascript
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();

// Check if user is admin or super-admin
if (authStore.isAdmin) {
    // Show admin features
}

// Check if user is super-admin
if (authStore.isSuperAdmin) {
    // Show super-admin only features (role assignment)
}
```

### Conditional Rendering

```vue
<!-- Show only to admins and super-admins -->
<div v-if="authStore.isAdmin">
    <button @click="openEditModal(user)">Edit</button>
</div>

<!-- Show only to super-admins -->
<div v-if="authStore.isSuperAdmin">
    <select v-model="selectedRole">
        <option v-for="role in roles" :value="role.name">
            {{ role.name }}
        </option>
    </select>
</div>

<!-- Show to regular admins (not super-admin) -->
<div v-else>
    <input :value="currentRole" disabled />
    <p>You don't have permission to change roles</p>
</div>
```

---

## Backend Implementation

### Checking Permissions in Controllers

```php
// Check if user has specific permission
if (!auth()->user()->can('assign roles')) {
    return ResponseHelper::error('Unauthorized', null, 403);
}

// Check if user has specific role
if (!auth()->user()->hasRole('super-admin')) {
    return ResponseHelper::error('Unauthorized', null, 403);
}
```

### Using Middleware

```php
// Protect route with permission
Route::middleware(['permission:assign roles'])->group(function () {
    Route::put('/user/{id}/assign-role', [UserController::class, 'assignRole']);
});

// Protect route with role
Route::middleware(['role:super-admin'])->group(function () {
    Route::resource('roles', RoleController::class);
});
```

---

## API Endpoints

### Get All Roles

```http
GET /api/roles
Authorization: Bearer {token}
```

Response:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "super-admin",
            "permissions": ["view users", "create users", ...],
            "permissions_count": 17
        },
        {
            "id": 2,
            "name": "admin",
            "permissions": ["view users", "create users", ...],
            "permissions_count": 8
        },
        {
            "id": 3,
            "name": "user",
            "permissions": ["view own profile", "edit own profile", ...],
            "permissions_count": 3
        }
    ]
}
```

### Get Role Capabilities

```http
GET /api/roles/capabilities
Authorization: Bearer {token}
```

Response:

```json
{
    "success": true,
    "data": {
        "super-admin": {
            "name": "Super Admin",
            "description": "Full system access with all permissions",
            "can": [...],
            "cannot": []
        },
        "admin": {
            "name": "Admin",
            "description": "User management without role/permission control",
            "can": [...],
            "cannot": [...]
        },
        "user": {
            "name": "User",
            "description": "Basic user with limited access",
            "can": [...],
            "cannot": [...]
        }
    }
}
```

### Update User with Role

```http
PUT /api/user/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "role": "admin"  // Only super-admin can change this
}
```

---

## Database Seeder

To setup roles and permissions:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This will:

1. Create all permissions
2. Create all roles
3. Assign permissions to roles
4. Display role capabilities

---

## Ensure All Users Have Profiles

```bash
php artisan users:ensure-profiles
```

This command creates profile records for users who don't have one yet.

---

## Role Assignment Rules

1. **Super Admin** can assign any role to any user
2. **Admin** can edit user details but cannot change roles
3. **Users** cannot manage other users at all
4. A user cannot change their own role
5. At least one Super Admin should always exist in the system

---

## Best Practices

### 1. Always Check Permissions on Both Frontend and Backend

The frontend checks are for UX, backend checks are for security.

### 2. Use Permission-Based Checks When Possible

Instead of checking roles, check for specific permissions for more granular control.

### 3. Keep Role Hierarchy Clear

```
Super Admin (Full Access)
    ├── Admin (User Management)
    └── User (Self Management Only)
```

### 4. Document Custom Permissions

If you add new permissions, update this documentation.

---

## Troubleshooting

### Permission Not Working?

1. Clear permission cache:

    ```bash
    php artisan permission:cache-reset
    ```

2. Re-seed permissions:
    ```bash
    php artisan db:seed --class=RolesAndPermissionsSeeder
    ```

### User Can't See Features?

1. Check if user has correct role assigned
2. Verify role has required permissions
3. Check frontend role checking logic
4. Verify API token is valid

---

## Example Usage

### Creating a New User as Admin

```vue
<template>
    <div v-if="authStore.isAdmin">
        <h2>Create New User</h2>
        <form @submit.prevent="createUser">
            <input v-model="newUser.name" placeholder="Name" />
            <input v-model="newUser.email" placeholder="Email" />
            <input
                v-model="newUser.password"
                type="password"
                placeholder="Password"
            />

            <!-- Only super-admin can assign roles -->
            <select v-if="authStore.isSuperAdmin" v-model="newUser.role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="super-admin">Super Admin</option>
            </select>

            <button type="submit">Create User</button>
        </form>
    </div>
</template>
```

### Editing User with Role Assignment

```vue
<template>
    <div v-if="authStore.isAdmin">
        <button @click="openEditModal(user)">Edit User</button>

        <modal v-if="showEditModal">
            <h3>Edit User</h3>
            <input v-model="editForm.name" />
            <input v-model="editForm.email" />

            <!-- Role field - different behavior based on role -->
            <div v-if="authStore.isSuperAdmin">
                <select v-model="editForm.role">
                    <option v-for="role in roles" :value="role.name">
                        {{ role.name }}
                    </option>
                </select>
                <p class="hint">Only super-admin can assign roles</p>
            </div>
            <div v-else>
                <input :value="editForm.role" disabled />
                <p class="hint">You don't have permission to change roles</p>
            </div>
        </modal>
    </div>
</template>
```

---

---

# Tutorial: Menambahkan Fitur Baru dengan Role & Permission

Tutorial ini akan memandu Anda langkah demi langkah untuk menambahkan fitur baru ke dalam aplikasi backend API starter ini. Sebagai studi kasus, kita akan membuat fitur **Manajemen Produk (Product Management)**.

Tujuannya adalah:

-   **Super Admin** & **Admin**: Bisa CRUD Products.
-   **Editor** (Role Baru): Bisa Edit Product tapi tidak bisa Delete.
-   **User**: Hanya bisa melihat list product.

---

## Langkah 1: Persiapan Database

Buat Model dan Migration untuk `Product`.

```bash
php artisan make:model Product -m
```

Edit file migration `database/migrations/xxxx_xx_xx_create_products_table.php`:

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock')->default(0);
        $table->timestamps();
    });
}
```

Migrasikan database:

```bash
php artisan migrate
```

---

## Langkah 2: Membuat Controller

Buat Controller resource untuk Product.

```bash
php artisan make:controller ProductController --api
```

Implementasikan CRUD dasar di `app/Http/Controllers/ProductController.php`. **PENTING**: Kita akan menambahkan proteksi permission di sini.

```php
<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        // Proteksi Permission
        // 'view products' bisa akses index dan show
        $this->middleware('permission:view products')->only(['index', 'show']);

        // 'create products' bisa akses store
        $this->middleware('permission:create products')->only(['store']);

        // 'edit products' bisa akses update
        $this->middleware('permission:edit products')->only(['update']);

        // 'delete products' bisa akses destroy
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index()
    {
        $products = Product::paginate(10);
        return ResponseHelper::success($products, 'Products retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($request->all());
        return ResponseHelper::success($product, 'Product created successfully', 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return ResponseHelper::success($product, 'Product details');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return ResponseHelper::success($product, 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return ResponseHelper::success(null, 'Product deleted successfully');
    }
}
```

---

## Langkah 3: Mendaftarkan Permissions Baru

Agar middleware di atas bekerja, kita harus mendaftarkan permission tersebut ke sistem.

### Cara 1: Lewat Dashboard (Frontend)

1. Login sebagai **Super Admin**.
2. Masuk ke manu **Roles & Permissions**.
3. Klik tombol **"+ Add Permission"**.
4. Masukkan nama permission (contoh: `view products`, `create products`, dll).

Buatlah permissions berikut:

-   `view products`
-   `create products`
-   `edit products`
-   `delete products`

### Cara 2: Menambahkan Lewat Seeder (Alternative)

Edit `database/seeders/RolesAndPermissionsSeeder.php` dan tambahkan ke array `$permissions`.

---

## Langkah 4: Mendaftarkan Route

Buka `routes/v1.php`:

```php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // ... route lain ...

    // Product Routes
    Route::apiResource('products', \App\Http\Controllers\ProductController::class);
});
```

---

## Langkah 5: Mengatur Role & Permission

Sekarang kita atur siapa yang boleh ngapain. Login ke **Dashboard Super Admin**.

1. Masuk menu **Roles & Permissions**.
2. Buat Role baru bernama **"Editor"**.
3. Edit Role **"Editor"**:
    - Centang `view products`
    - Centang `edit products`
    - JANGAN centang `delete products`
4. Edit Role **"Admin"**:
    - Centang semua product permissions.
5. Edit Role **"User"**:
    - Centang `view products` saja.

---

## Langkah 6: Testing

Sekarang coba login dengan user yang memiliki role **"Editor"**.

-   Coba akses API `POST /api/v1/products` -> Harusnya **Gagal (403)** karena Editor tidak punya permission `create products`.
-   Coba akses API `PUT /api/v1/products/{id}` -> Harusnya **Berhasil**.

---

## Tips Keamanan

1. **Selalu gunakan Middleware Permission**: Jangan hanya mengandalkan pengecekan role (`hasRole('admin')`). Gunakan permission (`can('edit products')`) agar lebih fleksibel. Suatu saat Anda mungkin ingin memberi akses edit ke user biasa tanpa menjadikannya admin.
2. **Setup di Constructor**: Cara paling bersih adalah define middleware permissions di `__construct` controller.
3. **Frontend Check**: Di Vue.js, sembunyikan tombol Delete jika user tidak punya permission delete.

```javascript
// Contoh di Vue Component
<button v-if="can('delete products')" @click="deleteProduct">Delete</button>
```

(Anda perlu membuat helper fungsi `can()` di frontend yang mengecek array permission user dari `authStore`).

---

Selamat! Anda telah berhasil menambahkan fitur baru yang terintegrasi penuh dengan sistem Role & Permission.
