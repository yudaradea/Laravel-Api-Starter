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
        $user->load('roles.permissions');

        return ResponseHelper::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }
}
