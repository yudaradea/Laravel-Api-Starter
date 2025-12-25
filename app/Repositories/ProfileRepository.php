<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

use App\Interfaces\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function getProfile($userId)
    {
        $user = User::with('profile')->findOrFail($userId);

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : null,
            'bio' => $user->profile?->bio,
            'phone' => $user->profile?->phone,
            'address' => $user->profile?->address,
        ];

        return ResponseHelper::success($data, 'Profile retrieved successfully');
    }

    public function updateProfile($userId, array $data, $file = null)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);

            // Update User Table
            $userData = [];
            if (isset($data['name'])) $userData['name'] = $data['name'];
            if (isset($data['email'])) $userData['email'] = $data['email'];

            if (!empty($userData)) {
                $user->update($userData);
            }

            // Handle Avatar Upload
            $avatarPath = $user->profile?->avatar;
            if ($file) {
                // Delete old avatar if exists
                if ($avatarPath) {
                    FileUploadService::delete($avatarPath);
                }
                $avatarPath = FileUploadService::upload($file, 'avatars');
            }

            // Update or Create Profile
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar' => $avatarPath,
                    'bio' => $data['bio'] ?? $user->profile?->bio,
                    'phone' => $data['phone'] ?? $user->profile?->phone,
                    'address' => $data['address'] ?? $user->profile?->address,
                ]
            );

            DB::commit();

            return $this->getProfile($userId);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::error('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }
}
