# 📎 File Upload Handler

Secure file upload system untuk images, documents, dan files lainnya. Menggunakan `FileUploadService` yang sudah terintegrasi.

## 🚀 Fitur

-   ✅ **Validasi Otomatis**: Memastikan file yang diupload aman.
-   ✅ **Secure Filenames**: Menggunakan UUID untuk mencegah nama file duplikat.
-   ✅ **Storage Management**: Mudah mengatur disk (public/s3/local).

## 📖 Cara Penggunaan (Usage)

### 1. Basic Upload

Gunakan `FileUploadService::upload()` di Controller atau Repository Anda.

```php
use App\Services\FileUploadService;

public function updateAvatar(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'avatar' => 'required|image|max:2048', // Max 2MB
    ]);

    // 2. Upload File
    if ($request->hasFile('avatar')) {
        // Upload ke folder 'avatars' di storage public
        $path = FileUploadService::upload($request->file('avatar'), 'avatars');

        // Output: avatars/uuid-filename.jpg

        // 3. Simpan ke Database
        $user->update(['avatar' => $path]);
    }
}
```

### 2. Delete File

Jangan lupa hapus file lama saat update/delete data untuk menghemat storage.

```php
use App\Services\FileUploadService;

public function deleteUser($id)
{
    $user = User::find($id);

    // Hapus file avatar jika ada
    if ($user->avatar) {
        FileUploadService::delete($user->avatar);
    }

    $user->delete();
}
```

## 🔧 Konfigurasi

### 1. Storage Link

Wajib dijalankan agar file bisa diakses via URL:

```bash
php artisan storage:link
```

### 2. Akses File di Frontend

Di Laravel, Anda bisa mengakses file public dengan helper `asset()` atau `Storage::url()`.

**Di Controller/Resource:**

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        // Pastikan generate full URL
        'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
    ];
}
```

## 🎯 Contoh Integrasi: User Profile

Kami telah menyediakan module `Profile` sebagai referensi implementasi file upload yang lengkap.

**Endpoint:**

-   `POST /api/profile` (Update profile & upload avatar)
-   `GET /api/profile` (Lihat profile & avatar URL)

Lihat kodenya di:

-   `app/Http/Controllers/ProfileController.php`
-   `app/Repositories/ProfileRepository.php`
