<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Login user
     *
     * @param array $credentials
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', null, 401);
        }

        $user = Auth::user();
        $user->load('profile', 'roles.permissions');

        $token = $user->createToken('auth_token')->plainTextToken;

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login successful');
    }

    /**
     * Register user
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role
        $user->assignRole('user');

        // Create empty profile
        $user->profile()->create([
            'phone' => null,
            'address' => null,
            'bio' => null,
            'avatar' => null,
        ]);

        // Load profile for response
        $user->load('profile', 'roles.permissions');

        $token = $user->createToken('auth_token')->plainTextToken;

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Registration successful', 201);
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return ResponseHelper::success(null, 'Logout successful');
    }

    /**
     * Get authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();
        $user->load('roles.permissions', 'profile');

        return ResponseHelper::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Change user password
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(array $data)
    {
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            return ResponseHelper::error('Current password is incorrect', null, 401);
        }

        // Update password
        $user->password = Hash::make($data['password']);
        $user->save();

        return ResponseHelper::success(null, 'Password changed successfully');
    }
}
