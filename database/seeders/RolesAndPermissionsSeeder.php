<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',

            // Profile Management
            'view own profile',
            'edit own profile',
            'view any profile',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'assign permissions',

            // General
            'access dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Super Admin - Can do everything
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Can manage users but not roles/permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view own profile',
            'edit own profile',
            'view any profile',
            'access dashboard',
        ]);

        // User - Basic permissions
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'view own profile',
            'edit own profile',
            'access dashboard',
        ]);

        $this->command->info('Roles and Permissions created successfully!');
        $this->command->info('');
        $this->command->info('=== Role Capabilities ===');
        $this->command->info('');
        $this->command->info('SUPER ADMIN:');
        $this->command->info('  ✓ Full system access');
        $this->command->info('  ✓ Manage users (create, edit, delete)');
        $this->command->info('  ✓ Assign roles to users');
        $this->command->info('  ✓ Manage roles and permissions');
        $this->command->info('  ✓ View and edit any profile');
        $this->command->info('');
        $this->command->info('ADMIN:');
        $this->command->info('  ✓ Manage users (create, edit, delete)');
        $this->command->info('  ✓ View any user profile');
        $this->command->info('  ✓ Access dashboard');
        $this->command->info('  ✗ Cannot assign roles');
        $this->command->info('  ✗ Cannot manage roles/permissions');
        $this->command->info('');
        $this->command->info('USER:');
        $this->command->info('  ✓ View and edit own profile');
        $this->command->info('  ✓ Access dashboard');
        $this->command->info('  ✗ Cannot manage other users');
        $this->command->info('  ✗ Cannot view other profiles');
    }
}
