<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Assign super_admin role if it exists
        if (method_exists($superAdmin, 'assignRole')) {
            if (! $superAdmin->hasRole('super_admin')) {
                $superAdmin->assignRole('super_admin');
            }
        }
    }
}
