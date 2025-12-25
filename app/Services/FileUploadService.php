<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the specified path.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $disk
     * @return string The path of the uploaded file
     */
    public static function upload(UploadedFile $file, string $path = 'uploads', ?string $disk = 'public'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $filename, $disk);
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public static function delete(string $path, ?string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }
}
