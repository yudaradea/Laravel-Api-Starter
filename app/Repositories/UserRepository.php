<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @param string $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($perPage, $search)
    {
        $users = User::with('roles')
            ->search($search)
            ->paginate($perPage);

        return ResponseHelper::success(
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }

    /**
     * Get all users with pagination (different format)
     *
     * @param int $perPage
     * @param string $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPaginated($perPage, $search)
    {
        $users = User::with('roles')
            ->search($search)
            ->paginate($perPage);

        return ResponseHelper::success(
            new PaginateResource($users, UserResource::class),
            'Users retrieved successfully'
        );
    }

    /**
     * Store new user
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        
        // Assign role if provided
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        } else {
            $user->assignRole('user');
        }

        return ResponseHelper::success(
            new UserResource($user->load('roles')),
            'User created successfully',
            201
        );
    }

    /**
     * Show user detail
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return ResponseHelper::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update user
     *
     * @param array $data
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(array $data, $id)
    {
        $user = User::findOrFail($id);
        
        // Remove password if empty
        if (isset($data['password']) && empty($data['password'])) {
            unset($data['password']);
        }
        
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        
        // Update role if provided
        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return ResponseHelper::success(
            new UserResource($user->load('roles')),
            'User updated successfully'
        );
    }

    /**
     * Delete user
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return ResponseHelper::success(
            null,
            'User deleted successfully'
        );
    }

    /**
     * Update user password
     *
     * @param array $data
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(array $data, $id)
    {
        $user = User::findOrFail($id);

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            return ResponseHelper::error('Current password is incorrect', null, 422);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return ResponseHelper::success(
            null,
            'Password updated successfully'
        );
    }
}
