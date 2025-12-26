<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Get all roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();

        $rolesData = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
                'permissions_count' => $role->permissions->count(),
                'guards' => 'web', // Spatie uses web guard by default
            ];
        });

        return ResponseHelper::success(
            $rolesData,
            'Roles retrieved successfully'
        );
    }

    /**
     * Create new role
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        // Cache reset
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return ResponseHelper::success(
            $role->load('permissions'),
            'Role created successfully',
            201
        );
    }

    /**
     * Update role
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Jangan edit role super-admin untuk keamanan
        if ($role->name === 'super-admin') {
            return ResponseHelper::error('Cannot edit super-admin role', null, 403);
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        // Cache reset
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return ResponseHelper::success(
            $role->load('permissions'),
            'Role updated successfully'
        );
    }

    /**
     * Delete role
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting critical roles
        if (in_array($role->name, ['super-admin', 'admin', 'user'])) {
            return ResponseHelper::error('Cannot delete system roles', null, 403);
        }

        $role->delete();

        // Cache reset
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return ResponseHelper::success(
            null,
            'Role deleted successfully'
        );
    }

    /**
     * Get role capabilities description
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function capabilities()
    {
        $capabilities = [
            'super-admin' => [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'can' => [
                    'Manage all users (create, edit, delete)',
                    'Assign roles to users',
                    'Manage roles and permissions',
                    'View and edit any profile',
                    'Access all system features',
                ],
                'cannot' => [],
            ],
            'admin' => [
                'name' => 'Admin',
                'description' => 'User management without role/permission control',
                'can' => [
                    'Manage users (create, edit, delete)',
                    'View any user profile',
                    'Access dashboard',
                ],
                'cannot' => [
                    'Assign roles to users',
                    'Manage roles and permissions',
                ],
            ],
            'user' => [
                'name' => 'User',
                'description' => 'Basic user with limited access',
                'can' => [
                    'View and edit own profile',
                    'Access dashboard',
                ],
                'cannot' => [
                    'Manage other users',
                    'View other user profiles',
                    'Assign roles',
                ],
            ],
        ];

        return ResponseHelper::success(
            $capabilities,
            'Role capabilities retrieved successfully'
        );
    }
}
