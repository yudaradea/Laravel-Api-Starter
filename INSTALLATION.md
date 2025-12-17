# 📦 Installation Guide - Step by Step

Panduan lengkap instalasi Laravel API Starter Pack dari awal sampai running.

## ✅ Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 atau SQLite
- Git (optional)

## 🚀 Method 1: Fresh Install (Recommended)

### Step 1: Create New Laravel Project

```bash
composer create-project laravel/laravel my-api-project
cd my-api-project
```

### Step 2: Install Dependencies

```bash
composer require laravel/sanctum
composer require spatie/laravel-permission
```

### Step 3: Extract Starter Pack

Extract `laravel-api-starter.zip` yang sudah didownload.

### Step 4: Copy Files

```bash
# From your extracted folder location
cd /path/to/laravel-api-starter

# Copy app files
cp -r app/* /path/to/my-api-project/app/

# Copy database files
cp -r database/migrations/* /path/to/my-api-project/database/migrations/
cp -r database/seeders/* /path/to/my-api-project/database/seeders/

# Copy routes
cp routes/api.php /path/to/my-api-project/routes/

# Copy bootstrap
cp bootstrap/app.php /path/to/my-api-project/bootstrap/
cp bootstrap/providers.php /path/to/my-api-project/bootstrap/

# Copy config
cp config/sanctum.php /path/to/my-api-project/config/
cp config/permission.php /path/to/my-api-project/config/

# Copy .env.example (optional, untuk reference)
cp .env.example /path/to/my-api-project/.env.starter-example
```

### Step 5: Configure Database

#### Option A: MySQL

1. Create database:
```sql
CREATE DATABASE my_api_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_api_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Option B: SQLite (Easier for development)

1. Create database file:
```bash
touch database/database.sqlite
```

2. Update `.env`:
```env
DB_CONNECTION=sqlite
# Comment these out:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### Step 6: Generate Application Key

```bash
php artisan key:generate
```

### Step 7: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Step 8: Run Migrations

```bash
php artisan migrate:fresh --seed
```

**Output yang benar:**
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table (XXX ms)
Migrating: 2025_01_01_000001_create_personal_access_tokens_table
Migrated:  2025_01_01_000001_create_personal_access_tokens_table (XXX ms)
Migrating: 2025_01_01_000002_create_permission_tables
Migrated:  2025_01_01_000002_create_permission_tables (XXX ms)

Seeding: Database\Seeders\RoleSeeder
Seeded:  Database\Seeders\RoleSeeder (XXX ms)
Seeding: Database\Seeders\UserSeeder
Seeded:  Database\Seeders\UserSeeder (XXX ms)
```

### Step 9: Verify Routes

```bash
php artisan route:list
```

**Harus muncul routes dengan prefix `/api`:**
```
POST   api/login
POST   api/register
GET    api/me
POST   api/logout
GET    api/user
...
```

### Step 10: Start Server

```bash
php artisan serve
```

Server akan running di: `http://localhost:8000`

### Step 11: Test API

#### Test Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "access_token": "1|xxxxx...",
    "token_type": "Bearer",
    "user": {
      "id": "xxx-xxx-xxx",
      "name": "Super Admin",
      "email": "admin@example.com",
      ...
    }
  }
}
```

#### Test Get Current User

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 🎯 Method 2: Using Installation Script

### Step 1: Extract Starter Pack

```bash
unzip laravel-api-starter.zip
cd laravel-api-starter
```

### Step 2: Make Script Executable

```bash
chmod +x install.sh
```

### Step 3: Run Installation

```bash
./install.sh my-api-project
```

The script will:
- Create new Laravel project
- Install dependencies
- Copy all files
- Setup database (SQLite)
- Run migrations & seeders
- Generate app key

### Step 4: Start Server

```bash
cd my-api-project
php artisan serve
```

## 🔍 Verification Checklist

After installation, verify:

- [ ] `php artisan route:list` shows API routes
- [ ] Can login via API
- [ ] Token authentication works
- [ ] Database tables created
- [ ] Default users seeded

## ⚠️ Common Issues

### Issue 1: Routes not showing

**Solution:** Check `bootstrap/app.php` has:
```php
api: __DIR__.'/../routes/api.php',
```

### Issue 2: "Field 'id' doesn't have a default value"

**Solution:** Check `app/Providers/AppServiceProvider.php`:
```php
public function boot(): void
{
    Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
}
```

### Issue 3: "Target [AuthRepositoryInterface] is not instantiable"

**Solution:** Check `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
];
```

### Issue 4: Permission denied on database/database.sqlite

**Solution:**
```bash
chmod 664 database/database.sqlite
chmod 775 database
```

### Issue 5: SQLSTATE connection refused

**Solution:** 
- MySQL: Make sure MySQL server is running
- SQLite: Make sure `database/database.sqlite` file exists

## 📖 Next Steps

1. Read [MIDDLEWARE.md](MIDDLEWARE.md) to learn about role & permission
2. Read [GUIDE.md](GUIDE.md) to learn how to create new modules
3. Import `postman_collection.json` to test all endpoints
4. Customize roles & permissions in `database/seeders/RoleSeeder.php`

## 🆘 Need Help?

Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for common problems and solutions.

---

**Happy Coding! 🚀**
