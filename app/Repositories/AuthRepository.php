<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Login user
     */
    public function login(array $credentials)
    {
        try {

            if (!Auth::attempt($credentials)) {
                return ResponseHelper::error(
                    'Username atau password salah',
                    'invalid_credentials',
                    401
                );
            }

            $user = Auth::user();
            $user->load('profile', 'roles.permissions');

            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseHelper::success(
                [
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => new UserResource($user),
                ],
                'Login successful',
                200
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                'Login failed',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Register user
     */
    public function register(array $data)
    {
        try {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('user');

            $user->profile()->create([
                'phone'   => null,
                'address' => null,
                'bio'     => null,
                'avatar'  => null,
            ]);

            $user->load('profile', 'roles.permissions');

            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseHelper::success(
                [
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => new UserResource($user),
                ],
                'Registration successful',
                201
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                'Registration failed',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return ResponseHelper::error(
                    'Unauthenticated',
                    null,
                    401
                );
            }

            $user->currentAccessToken()?->delete();

            return ResponseHelper::success(
                null,
                'Logout successful',
                200
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                'Logout failed',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return ResponseHelper::error(
                    'Unauthenticated',
                    null,
                    401
                );
            }

            $user->load('roles.permissions', 'profile');

            return ResponseHelper::success(
                new UserResource($user),
                'User retrieved successfully',
                200
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                'Failed to retrieve user',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Change password
     */
    public function changePassword(array $data)
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return ResponseHelper::error(
                    'Unauthenticated',
                    null,
                    401
                );
            }

            if (!Hash::check($data['current_password'], $user->password)) {
                return ResponseHelper::error(
                    'Current password is incorrect',
                    null,
                    401
                );
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            return ResponseHelper::success(
                null,
                'Password changed successfully',
                200
            );
        } catch (Throwable $e) {
            return ResponseHelper::error(
                'Failed to change password',
                $e->getMessage(),
                500
            );
        }
    }
}
