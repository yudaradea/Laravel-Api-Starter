<?php

namespace App\Interfaces;

interface ProfileRepositoryInterface
{
    /**
     * Get user profile
     * 
     * @param string|int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile($userId);

    /**
     * Update user profile
     * 
     * @param string|int $userId
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile($userId, array $data, $file = null);
}
