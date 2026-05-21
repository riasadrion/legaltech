<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all permissions
        foreach (Permissions::cases() as $case) {
            Permission::firstOrCreate(
                ['name' => $case->value],
                ['group' => $case->group()],
            );
        }

        // Seed default roles and assign permissions
        foreach (['super_admin', 'admin', 'staff'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName], ['guard' => 'admin']);

            $permissionNames = Permissions::forRole($roleName);

            if ($permissionNames) {
                $ids = Permission::whereIn('name', $permissionNames)->pluck('id');
                $role->permissions()->sync($ids);
            }
        }

        // Seed super admin
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
        );

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
    }
}
