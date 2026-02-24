# 📎 File Upload Handler

Sistem upload file yang aman untuk gambar, dokumen, dan file lainnya. Menggunakan `FileUploadService` yang sudah terintegrasi untuk memudahkan pengelolaan file.

---

## 🚀 Fitur Utama

-   ✅ **Validasi Otomatis**: Memastikan file yang diupload aman dan sesuai format.
-   ✅ **Secure Filenames**: Menggunakan UUID untuk mencegah duplikasi nama file & potensi serangan.
-   ✅ **Storage Management**: Mendukung upload ke Local (public) maupun cloud storage (S3).

---

## 📖 Cara Penggunaan

### 1. Basic Upload (Upload Sederhana)

Gunakan `FileUploadService::upload()` di Controller atau Repository Anda.

```php
use App\Services\FileUploadService;
use Illuminate\Http\Request;

public function updateAvatar(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
    ]);

    // 2. Upload File
    if ($request->hasFile('avatar')) {
        // Upload ke folder 'avatars' (storage/app/public/avatars)
        $path = FileUploadService::upload($request->file('avatar'), 'avatars');

        // Output $path: avatars/uuid-filename.jpg

        // 3. Simpan path ke Database
        $user->update(['avatar' => $path]);
    }
}
```

### 2. Delete File (Hapus File)

Jangan lupa menghapus file lama saat user mengupdate avatar atau data dihapus.

```php
use App\Services\FileUploadService;

public function deleteUser($id)
{
    $user = User::find($id);

    // Hapus file avatar lama jika ada
    if ($user->avatar) {
        FileUploadService::delete($user->avatar);
    }

    $user->delete();
}
```

---

## 🔧 Konfigurasi

### 1. Setup Storage Link (Wajib)

Agar file yang diupload ke `storage/app/public` bisa diakses melalui browser, Anda harus membuat symbolic link:

```bash
php artisan storage:link
```

### 2. Konfigurasi Disk (Local vs S3)

Pengaturan penyimpanan ada di `config/filesystems.php`. Secara default menggunakan `local`.

**Untuk menggunakan AWS S3 / MinIO:**

1. Buka `.env` dan ubah `FILESYSTEM_DISK`:

    ```env
    FILESYSTEM_DISK=s3
    ```

2. Isi kredensial AWS:
    ```env
    AWS_ACCESS_KEY_ID=your-key
    AWS_SECRET_ACCESS_KEY=your-secret
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=your-bucket
    AWS_USE_PATH_STYLE_ENDPOINT=false
    ```

`FileUploadService` akan otomatis menyesuaikan lokasi upload berdasarkan konfigurasi ini.

---

## 🌐 Akses File di Frontend/API

Saat mengembalikan URL file ke API response, gunakan helper `asset()` agar menghasilkan full URL.

**Di Controller / API Resource:**

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        // Generate full URL: http://domain.com/storage/avatars/xxx.jpg
        'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
    ];
}
```

---

## 🎯 Contoh Integrasi: User Profile

Kami telah menyediakan module `Profile` sebagai referensi implementasi file upload yang lengkap.

**Endpoint:**

-   `POST /api/profile` (Update profile & upload avatar)
-   `GET /api/me` (Lihat profile & avatar URL)

Lihat kodenya di:

-   `app/Http/Controllers/ProfileController.php`
-   `app/Services/FileUploadService.php`
