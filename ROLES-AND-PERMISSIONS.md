# Role & Permission System Documentation

## Table of Contents

-   [Overview](#overview)
-   [Roles and Capabilities](#roles-and-capabilities)
-   [Available Permissions](#available-permissions)
-   [Backend Implementation](#backend-implementation)
-   [Frontend Implementation](#frontend-implementation)
-   [API Endpoints](#api-endpoints)
-   [Database Setup](#database-setup)
-   [Best Practices](#best-practices)
-   [Troubleshooting](#troubleshooting)
-   [Tutorial: Adding New Features](#tutorial-adding-new-features)

---

## Overview

This application uses **Spatie Laravel Permission** package to manage roles and permissions. The system provides a flexible and granular access control mechanism with predefined roles and customizable permissions.

### Key Features

-   Role-based access control (RBAC)
-   Permission-based authorization
-   Easy role and permission management
-   Support for multiple roles per user
-   Dynamic permission assignment

---

## Roles and Capabilities

### 1. Super Admin

**Full system access with unrestricted permissions**

**CAN DO:**

-   ✅ Manage all users (create, read, update, delete)
-   ✅ Assign roles to users
-   ✅ Manage roles and permissions
-   ✅ View and edit any profile
-   ✅ Access all system features
-   ✅ Access dashboard

**CANNOT DO:**

-   ❌ N/A - Super Admin has all permissions

---

### 2. Admin

**User management without role/permission control**

**CAN DO:**

-   ✅ Manage users (create, read, update, delete)
-   ✅ View any user profile
-   ✅ Edit user information (name, email)
-   ✅ Access dashboard

**CANNOT DO:**

-   ❌ Assign roles to users (only Super Admin can)
-   ❌ Manage roles and permissions
-   ❌ Change their own role

---

### 3. User

**Basic user with limited access**

**CAN DO:**

-   ✅ View and edit own profile
-   ✅ Change own password
-   ✅ Upload own avatar
-   ✅ Access dashboard

**CANNOT DO:**

-   ❌ Manage other users
-   ❌ View other user profiles
-   ❌ Assign roles
-   ❌ Delete users

---

## Available Permissions

### User Management

| Permission     | Description                              |
| -------------- | ---------------------------------------- |
| `view users`   | View list of users                       |
| `create users` | Create new users                         |
| `edit users`   | Edit existing users                      |
| `delete users` | Delete users                             |
| `assign roles` | Assign roles to users (Super Admin only) |

### Profile Management

| Permission         | Description             |
| ------------------ | ----------------------- |
| `view own profile` | View own profile        |
| `edit own profile` | Edit own profile        |
| `view any profile` | View any user's profile |

### Role & Permission Management

| Permission           | Description                 |
| -------------------- | --------------------------- |
| `view roles`         | View available roles        |
| `create roles`       | Create new roles            |
| `edit roles`         | Edit existing roles         |
| `delete roles`       | Delete roles                |
| `view permissions`   | View available permissions  |
| `assign permissions` | Assign permissions to roles |

### General

| Permission         | Description          |
| ------------------ | -------------------- |
| `access dashboard` | Access the dashboard |

---

## Backend Implementation

### Protecting Controller with Permissions (Laravel 11+)

Use `HasMiddleware` interface with the `middleware()` method:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Define middleware for this controller
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index', 'getAllPaginated', 'show']),
            new Middleware('permission:create users', only: ['store']),
            new Middleware('permission:edit users', only: ['update', 'updatePassword']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        return $this->userRepository->index($perPage, $search);
    }

    public function store(UserStoreRequest $request)
    {
        return $this->userRepository->store($request->validated());
    }

    public function show($id)
    {
        return $this->userRepository->show($id);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        return $this->userRepository->update($request->validated(), $id);
    }

    public function destroy($id)
    {
        return $this->userRepository->destroy($id);
    }

    public function updatePassword(UserUpdatePasswordRequest $request, $id)
    {
        return $this->userRepository->updatePassword($request->validated(), $id);
    }
}
```

### Checking Permissions Manually

```php
// Check if user has specific permission
if (!auth()->user()->can('assign roles')) {
    return ResponseHelper::error('Unauthorized', null, 403);
}

// Check if user has specific role
if (!auth()->user()->hasRole('super-admin')) {
    return ResponseHelper::error('Unauthorized', null, 403);
}

// Check multiple permissions (user must have ALL)
if (!auth()->user()->hasAllPermissions(['edit users', 'delete users'])) {
    return ResponseHelper::error('Unauthorized', null, 403);
}

// Check multiple permissions (user must have ANY)
if (!auth()->user()->hasAnyPermission(['edit users', 'view users'])) {
    return ResponseHelper::error('Unauthorized', null, 403);
}
```

### Using Route Middleware

```php
// Protect route with permission
Route::middleware(['permission:assign roles'])->group(function () {
    Route::put('/user/{id}/assign-role', [UserController::class, 'assignRole']);
});

// Protect route with role
Route::middleware(['role:super-admin'])->group(function () {
    Route::resource('roles', RoleController::class);
});

// Multiple permissions (user must have ALL)
Route::middleware(['permission:edit users,delete users'])->group(function () {
    Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete']);
});
```

---

## Frontend Implementation

### Role Checks in Vue Components

The auth store provides helpers for role checking:

```javascript
import { useAuthStore } from "@/stores/auth";

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
<template>
    <!-- Show only to admins and super-admins -->
    <div v-if="authStore.isAdmin">
        <button @click="openEditModal(user)">Edit User</button>
        <button @click="deleteUser(user)">Delete User</button>
    </div>

    <!-- Show only to super-admins -->
    <div v-if="authStore.isSuperAdmin">
        <label>Role:</label>
        <select v-model="selectedRole">
            <option v-for="role in roles" :key="role.id" :value="role.name">
                {{ role.name }}
            </option>
        </select>
        <p class="text-xs text-gray-500">Only super-admin can assign roles</p>
    </div>

    <!-- Show to regular admins (not super-admin) -->
    <div v-else-if="authStore.isAdmin">
        <label>Role:</label>
        <input :value="currentRole" disabled class="bg-gray-100" />
        <p class="text-xs text-red-500">
            You don't have permission to change roles
        </p>
    </div>
</template>
```

### Permission-Based Rendering (Recommended)

Create a composable for permission checking:

```javascript
// composables/usePermission.js
import { useAuthStore } from "@/stores/auth";

export function usePermission() {
    const authStore = useAuthStore();

    const can = (permission) => {
        return (
            authStore.user?.permissions?.some((p) => p.name === permission) ||
            false
        );
    };

    const hasRole = (role) => {
        return authStore.user?.roles?.some((r) => r.name === role) || false;
    };

    return { can, hasRole };
}
```

Usage in component:

```vue
<template>
    <button v-if="can('edit users')" @click="editUser">Edit</button>
    <button v-if="can('delete users')" @click="deleteUser">Delete</button>
</template>

<script setup>
import { usePermission } from "@/composables/usePermission";

const { can, hasRole } = usePermission();
</script>
```

---

## API Endpoints

### Get All Roles

**Request:**

```http
GET /api/roles
Authorization: Bearer {token}
```

**Response:**

```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "super-admin",
            "permissions": [
                { "id": 1, "name": "view users" },
                { "id": 2, "name": "create users" }
            ],
            "permissions_count": 17
        },
        {
            "id": 2,
            "name": "admin",
            "permissions": [
                { "id": 1, "name": "view users" },
                { "id": 2, "name": "create users" }
            ],
            "permissions_count": 8
        }
    ]
}
```

---

### Get Role Capabilities

**Request:**

```http
GET /api/roles/capabilities
Authorization: Bearer {token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "super-admin": {
            "name": "Super Admin",
            "description": "Full system access with all permissions",
            "can": [
                "Manage all users",
                "Assign roles to users",
                "Manage roles and permissions"
            ],
            "cannot": []
        },
        "admin": {
            "name": "Admin",
            "description": "User management without role/permission control",
            "can": ["Manage users", "View any user profile"],
            "cannot": ["Assign roles to users", "Manage roles and permissions"]
        }
    }
}
```

---

### Update User with Role

**Request:**

```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "role": "admin"
}
```

> **Note:** Only super-admin can change user roles

**Response:**

```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": [{ "id": 2, "name": "admin" }]
    }
}
```

---

## Database Setup

### Run Seeder

To setup roles and permissions:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This will:

1. Create all permissions
2. Create all roles
3. Assign permissions to roles
4. Display role capabilities

### Ensure All Users Have Profiles

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

-   **Frontend checks** are for UX (hiding buttons, disabling forms)
-   **Backend checks** are for security (enforcing access control)

> ⚠️ **Warning:** Never rely on frontend checks alone!

---

### 2. Use Permission-Based Checks When Possible

**❌ Bad - Rigid, hard to change:**

```php
if (auth()->user()->hasRole('admin')) {
    // do something
}
```

**✅ Good - Flexible, granular control:**

```php
if (auth()->user()->can('edit users')) {
    // do something
}
```

---

### 3. Keep Role Hierarchy Clear

```
Super Admin (Full Access)
    ├── Admin (User Management)
    └── User (Self Management Only)
```

---

### 4. Use Descriptive Permission Names

**❌ Bad:**

```php
'manage_stuff'
'do_things'
```

**✅ Good:**

```php
'edit users'
'delete products'
'view reports'
```

---

### 5. Group Related Permissions

```php
// User Management
'view users'
'create users'
'edit users'
'delete users'

// Product Management
'view products'
'create products'
'edit products'
'delete products'
```

---

## Troubleshooting

### Permission Not Working?

**1. Clear permission cache:**

```bash
php artisan permission:cache-reset
```

**2. Re-seed permissions:**

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

**3. Check if permission exists:**

```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::all()->pluck('name');
```

---

### User Can't See Features?

**Check the following:**

-   ✅ User has correct role assigned
-   ✅ Role has required permissions
-   ✅ Frontend permission checking logic is correct
-   ✅ API token is valid and not expired
-   ✅ Middleware is properly configured

**Debug user permissions:**

```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->getAllPermissions()->pluck('name');
>>> $user->getRoleNames();
```

---

### 403 Forbidden Error?

**Common causes:**

1. User doesn't have required permission
2. Permission cache needs to be reset
3. Middleware is not properly configured
4. User's token doesn't include permission data

---

## Tutorial: Adding New Features

This tutorial will guide you step-by-step to add a new feature to the application. As a case study, we'll create a **Product Management** feature.

### Requirements

-   **Super Admin** & **Admin**: Full CRUD access to Products
-   **Editor** (New Role): Can edit Products but cannot delete
-   **User**: Can only view product list

---

### Step 1: Database Preparation

Create Model and Migration for `Product`:

```bash
php artisan make:model Product -m
```

Edit migration file `database/migrations/xxxx_xx_xx_create_products_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

### Step 2: Create Controller

Create API Resource Controller:

```bash
php artisan make:controller ProductController --api
```

Implement CRUD with permission protection in `app/Http/Controllers/ProductController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    /**
     * Define middleware for this controller
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view products', only: ['index', 'show']),
            new Middleware('permission:create products', only: ['store']),
            new Middleware('permission:edit products', only: ['update']),
            new Middleware('permission:delete products', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');

        $products = Product::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        })->paginate($perPage);

        return ResponseHelper::success($products, 'Products retrieved successfully');
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);

        return ResponseHelper::success($product, 'Product created successfully', 201);
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return ResponseHelper::success($product, 'Product retrieved successfully');
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return ResponseHelper::success($product, 'Product updated successfully');
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return ResponseHelper::success(null, 'Product deleted successfully');
    }
}
```

---

### Step 3: Register New Permissions

You need to register these permissions in the system.

#### Option 1: Via Dashboard (Frontend)

1. Login as **Super Admin**
2. Go to **Roles & Permissions** menu
3. Click **"+ Add Permission"** button
4. Add the following permissions:
    - `view products`
    - `create products`
    - `edit products`
    - `delete products`

#### Option 2: Via Seeder (Recommended)

Edit `database/seeders/RolesAndPermissionsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',

            // Profile Management
            'view own profile',
            'edit own profile',
            'view any profile',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'assign permissions',

            // Product Management (NEW)
            'view products',
            'create products',
            'edit products',
            'delete products',

            // General
            'access dashboard',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createSuperAdminRole();
        $this->createAdminRole();
        $this->createEditorRole(); // NEW ROLE
        $this->createUserRole();
    }

    private function createSuperAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }

    private function createAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view any profile',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'access dashboard',
        ]);
    }

    private function createEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'editor']);
        $role->givePermissionTo([
            'view products',
            'edit products', // Can edit
            // Note: NO 'delete products' permission
            'access dashboard',
        ]);
    }

    private function createUserRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'user']);
        $role->givePermissionTo([
            'view own profile',
            'edit own profile',
            'view products', // Can only view
            'access dashboard',
        ]);
    }
}
```

Run the seeder:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

### Step 4: Register Routes

Open `routes/v1.php`:

```php
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // ... existing routes ...

    // Product Routes
    Route::apiResource('products', ProductController::class);
});
```

**Available endpoints:**

-   `GET /api/products` - List all products
-   `POST /api/products` - Create product
-   `GET /api/products/{id}` - Show product detail
-   `PUT /api/products/{id}` - Update product
-   `DELETE /api/products/{id}` - Delete product

---

### Step 5: Testing Permissions

Test with different roles:

#### Test as Super Admin

```bash
# Should succeed - Super Admin has all permissions
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer {super_admin_token}"

curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {super_admin_token}"
```

#### Test as Editor

```bash
# Should succeed - Editor can view products
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer {editor_token}"

# Should succeed - Editor can edit products
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {editor_token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Product"}'

# Should FAIL (403) - Editor cannot delete products
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {editor_token}"
```

#### Test as User

```bash
# Should succeed - User can view products
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer {user_token}"

# Should FAIL (403) - User cannot create products
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer {user_token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "New Product", "price": 1000}'
```

---

### Step 6: Frontend Implementation

Create Vue component with permission checks:

```vue
<template>
    <div class="product-management">
        <h1>Product Management</h1>

        <!-- Create Button - Only for users with 'create products' permission -->
        <button
            v-if="can('create products')"
            @click="openCreateModal"
            class="btn-primary"
        >
            + Add New Product
        </button>

        <!-- Product List -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th v-if="can('edit products') || can('delete products')">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.id">
                    <td>{{ product.name }}</td>
                    <td>{{ formatPrice(product.price) }}</td>
                    <td>{{ product.stock }}</td>
                    <td v-if="can('edit products') || can('delete products')">
                        <!-- Edit Button -->
                        <button
                            v-if="can('edit products')"
                            @click="editProduct(product)"
                            class="btn-edit"
                        >
                            Edit
                        </button>

                        <!-- Delete Button -->
                        <button
                            v-if="can('delete products')"
                            @click="deleteProduct(product)"
                            class="btn-delete"
                        >
                            Delete
                        </button>

                        <!-- Show message if user can only view -->
                        <span
                            v-else-if="!can('edit products')"
                            class="text-gray-400"
                        >
                            View Only
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { usePermission } from "@/composables/usePermission";
import axios from "axios";

const { can } = usePermission();
const products = ref([]);

const fetchProducts = async () => {
    try {
        const response = await axios.get("/api/products");
        products.value = response.data.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const editProduct = (product) => {
    // Open edit modal
};

const deleteProduct = async (product) => {
    if (confirm(`Delete ${product.name}?`)) {
        try {
            await axios.delete(`/api/products/${product.id}`);
            fetchProducts(); // Refresh list
        } catch (error) {
            console.error("Error deleting product:", error);
        }
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(price);
};

onMounted(() => {
    fetchProducts();
});
</script>
```

---

## Summary

### Key Points When Adding New Features

1. **Create Model & Migration** - Define database structure
2. **Create Controller with HasMiddleware** - Implement CRUD with permission checks
3. **Register Permissions** - Add new permissions to seeder
4. **Assign Permissions to Roles** - Define who can do what
5. **Register Routes** - Make endpoints accessible
6. **Test Thoroughly** - Verify permissions work correctly
7. **Implement Frontend** - Add permission checks in UI

### Security Checklist

-   ✅ Backend has permission middleware
-   ✅ Frontend hides unauthorized buttons
-   ✅ API returns 403 for unauthorized access
-   ✅ Permissions are granular and specific
-   ✅ All CRUD operations are protected

---

## Additional Resources

-   [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
-   [Laravel Authorization Documentation](https://laravel.com/docs/authorization)
-   [Laravel API Resources](https://laravel.com/docs/api-resources)
-   [Laravel Middleware Documentation](https://laravel.com/docs/middleware)

---

## License

This documentation is part of the Laravel API Starter project.

---

**Last Updated:** December 2024  
**Version:** 1.0.0
