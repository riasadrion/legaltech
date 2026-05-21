<?php

use App\Constants\Permissions;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesPermissionsSeeder::class);
});

test('super_admin role has all permissions', function () {
    $role = Role::where('name', 'super_admin')->with('permissions')->first();

    $allPermissions = array_column(Permissions::cases(), 'value');

    foreach ($allPermissions as $permission) {
        expect($role->hasPermission($permission))->toBeTrue("super_admin missing: {$permission}");
    }
});

test('admin can be assigned a role and gains its permissions', function () {
    $admin = Admin::factory()->create();
    $admin->assignRole('admin');

    expect($admin->hasRole('admin'))->toBeTrue();
    expect($admin->hasPermission(Permissions::USERS_VIEW->value))->toBeTrue();
    expect($admin->hasPermission(Permissions::ROLES_MANAGE->value))->toBeFalse();
});

test('staff role only has limited permissions', function () {
    $admin = Admin::factory()->create();
    $admin->assignRole('staff');

    expect($admin->hasPermission(Permissions::SUPPORT_VIEW->value))->toBeTrue();
    expect($admin->hasPermission(Permissions::USERS_DELETE->value))->toBeFalse();
    expect($admin->hasPermission(Permissions::SETTINGS_MANAGE->value))->toBeFalse();
});

test('super_admin can() always returns true', function () {
    $admin = Admin::factory()->create();
    $admin->assignRole('super_admin');

    expect($admin->can('anything.at.all'))->toBeTrue();
    expect($admin->can(Permissions::USERS_DELETE->value))->toBeTrue();
});

test('staff cannot escalate to super_admin', function () {
    $staff = Admin::factory()->create();
    $staff->assignRole('staff');

    expect($staff->hasRole('super_admin'))->toBeFalse();
    expect($staff->can(Permissions::ROLES_MANAGE->value))->toBeFalse();
});

test('user model can have a role assigned', function () {
    $user = User::factory()->create();
    $userRole = Role::where('name', 'user')->first();
    $user->roles()->attach($userRole->id);

    expect($user->hasRole('user'))->toBeTrue();
});

test('role can be removed from admin', function () {
    $admin = Admin::factory()->create();
    $admin->assignRole('admin');
    $admin->removeRole('admin');

    expect($admin->hasRole('admin'))->toBeFalse();
    expect($admin->hasPermission(Permissions::USERS_VIEW->value))->toBeFalse();
});

test('activity log records an entry', function () {
    $admin = Admin::factory()->create();
    $admin->logActivity('admin.created', ['note' => 'test']);

    expect(ActivityLog::where('action', 'admin.created')->exists())->toBeTrue();
});
