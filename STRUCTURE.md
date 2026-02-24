# 📂 Laravel API Starter Pack - Struktur File

## Struktur Lengkap

```plaintext
laravel-api-starter/
├── 📄 README.md                          # Dokumentasi utama
├── 📄 GUIDE.md                           # Panduan cepat dan template
├── 📄 CHANGELOG.md                       # Catatan perubahan versi
├── 📄 LICENSE                            # MIT License
├── 📄 .env.example                       # Template environment
├── 🔧 install.sh                         # Script instalasi otomatis
├── 📦 postman_collection.json            # Koleksi Postman untuk testing
│
├── 📁 app/
│   ├── 📁 Traits/
│   │   └── UUID.php                      # Trait untuk UUID primary key
│   │
│   ├── 📁 Helpers/
│   │   └── ResponseHelper.php            # Helper untuk response API standar
│   │
│   ├── 📁 Interfaces/
│   │   ├── AuthRepositoryInterface.php   # Interface untuk Auth Repository
│   │   └── UserRepositoryInterface.php   # Interface untuk User Repository
│   │
│   ├── 📁 Repositories/
│   │   ├── AuthRepository.php            # Implementasi Auth logic
│   │   └── UserRepository.php            # Implementasi User CRUD logic
│   │
│   ├── 📁 Models/
│   │   ├── User.php                      # Model User dengan UUID & Roles
│   │   └── PersonalAccessToken.php       # Custom Sanctum Token Model
│   │
│   ├── 📁 Providers/
│   │   └── RepositoryServiceProvider.php # Service Provider untuk DI
│   │
│   └── 📁 Http/
│       ├── 📁 Controllers/
│       │   ├── Controller.php            # Base Controller
│       │   ├── AuthController.php        # Auth endpoints
│       │   └── UserController.php        # User CRUD endpoints
│       │
│       ├── 📁 Requests/
│       │   ├── LoginStoreRequest.php     # Validasi login
│       │   ├── RegisterStoreRequest.php  # Validasi register
│       │   └── 📁 User/
│       │       ├── UserStoreRequest.php  # Validasi create user
│       │       ├── UserUpdateRequest.php # Validasi update user
│       │       └── UserUpdatePasswordRequest.php # Validasi update password
│       │
│       └── 📁 Resources/
│           ├── UserResource.php          # Format response User
│           └── PaginateResource.php      # Format response pagination
│
├── 📁 database/
│   ├── 📁 migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_01_01_000001_create_personal_access_tokens_table.php
│   │   └── 2025_01_01_000002_create_permission_tables.php
│   │
│   └── 📁 seeders/
│       ├── DatabaseSeeder.php            # Main seeder
│       ├── RoleSeeder.php                # Seed roles & permissions
│       ├── UserSeeder.php                # Seed default users
│
├── 📁 routes/
│   └── v1.php                            # API routes definition (Version 1)
│
└── 📁 config/
    ├── sanctum.php                       # Sanctum configuration
    └── permission.php                    # Spatie Permission config
```

## 🎯 File Descriptions

### Core Files

| File                      | Deskripsi                                    |
| ------------------------- | -------------------------------------------- |
| `README.md`               | Dokumentasi lengkap instalasi dan penggunaan |
| `GUIDE.md`                | Panduan cepat dengan template module         |
| `CHANGELOG.md`            | Riwayat perubahan dan fitur                  |
| `install.sh`              | Script untuk instalasi otomatis              |
| `postman_collection.json` | Koleksi API untuk testing di Postman         |

### Application Layer

#### Traits

-   **UUID.php**: Trait untuk menggunakan UUID sebagai primary key

#### Helpers

-   **ResponseHelper.php**: Helper untuk membuat response API yang konsisten
    -   `success()`: Response sukses
    -   `error()`: Response error

#### Interfaces

-   **AuthRepositoryInterface.php**: Contract untuk authentication
    -   `login()`, `register()`, `logout()`, `me()`
-   **UserRepositoryInterface.php**: Contract untuk user management
    -   `index()`, `store()`, `show()`, `update()`, `destroy()`, `updatePassword()`

#### Repositories

-   **AuthRepository.php**: Business logic untuk authentication
-   **UserRepository.php**: Business logic untuk user CRUD operations

#### Models

-   **User.php**: Model dengan UUID, Soft Deletes, Roles, dan Search scope
-   **PersonalAccessToken.php**: Custom Sanctum token model dengan UUID

#### Controllers

-   **AuthController.php**: Endpoint untuk login, register, logout, me
-   **UserController.php**: CRUD endpoints untuk user management

#### Requests (Form Validation)

-   **LoginStoreRequest.php**: Validasi email & password
-   **RegisterStoreRequest.php**: Validasi registrasi user baru
-   **UserStoreRequest.php**: Validasi create user
-   **UserUpdateRequest.php**: Validasi update user
-   **UserUpdatePasswordRequest.php**: Validasi update password

#### Resources (Response Formatting)

-   **UserResource.php**: Format response user dengan roles & permissions
-   **PaginateResource.php**: Format response pagination yang reusable

### Database Layer

#### Migrations

1. **create_users_table**: Tabel users dengan UUID
2. **create_personal_access_tokens_table**: Tabel untuk Sanctum tokens
3. **create_permission_tables**: Tabel untuk Spatie Permission (roles, permissions, pivot tables)

#### Seeders

-   **RoleSeeder**: Seed 3 roles (super-admin, admin, user) dengan permissions
-   **UserSeeder**: Seed 3 default users dengan berbagai roles
-   **DatabaseSeeder**: Orchestrator untuk menjalankan semua seeders

### Routes

-   **v1.php**: Definisi semua API endpoints dengan authentication middleware (Version 1)

### Configuration

-   **sanctum.php**: Konfigurasi Laravel Sanctum untuk API authentication
-   **permission.php**: Konfigurasi Spatie Permission untuk role & permission management

## 🔄 Data Flow

```
Request → Route → Controller → Repository → Model → Database
                                    ↓
                              Response ← Resource ← Helper
```

### Example Flow: Get All Users

1. **Request**: `GET /api/user/all/paginated?per_page=10&search=admin`
2. **Route**: `routes/v1.php` → `UserController@getAllPaginated`
3. **Controller**: `UserController` → calls `UserRepository`
4. **Repository**: `UserRepository@getAllPaginated`
    - Query database dengan pagination & search
    - Return data menggunakan `ResponseHelper`
5. **Resource**: `PaginateResource` → format data
6. **Response**: JSON dengan format standar

## 📊 Database Schema

### Users Table

```sql
- id (uuid, primary)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- remember_token (string, nullable)
- deleted_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Personal Access Tokens Table

```sql
- id (uuid, primary)
- tokenable_type (string)
- tokenable_id (uuid)
- name (string)
- token (string, unique)
- abilities (text, nullable)
- last_used_at (timestamp, nullable)
- expires_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Roles & Permissions Tables

-   `roles`: id, name, guard_name
-   `permissions`: id, name, guard_name
-   `model_has_roles`: pivot table
-   `model_has_permissions`: pivot table
-   `role_has_permissions`: pivot table

## 🔑 Default Credentials

| Role        | Email             | Password |
| ----------- | ----------------- | -------- |
| Super Admin | admin@example.com | password |
| Admin       | user@example.com  | password |
| User        | test@example.com  | password |

## 📝 API Endpoints

### Authentication

-   `POST /api/login` - Login
-   `POST /api/register` - Register
-   `POST /api/logout` - Logout (Auth Required)
-   `GET /api/me` - Get current user (Auth Required)

### User Management (Auth Required)

-   `GET /api/user` - Get all users
-   `GET /api/user/all/paginated` - Get paginated users with search
-   `GET /api/user/{id}` - Get user by ID
-   `POST /api/user` - Create new user
-   `PUT /api/user/{id}` - Update user
-   `PUT /api/user/{id}/update-password` - Update user password
-   `DELETE /api/user/{id}` - Delete user (soft delete)

## 🎨 Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

### Paginated Response

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "data": [...],
    "current_page": 1,
    "last_page": 10,
    "per_page": 10,
    "total": 100,
    "from": 1,
    "to": 10
  }
}
```

---

📚 Untuk panduan lengkap, lihat **README.md** dan **GUIDE.md**
