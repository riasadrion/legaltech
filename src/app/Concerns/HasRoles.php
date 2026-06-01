<?php

namespace App\Concerns;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

trait HasRoles
{
    public function roles()
    {
        $pivot = $this instanceof Admin ? 'admin_role' : 'user_role';

        return $this->belongsToMany(Role::class, $pivot);
    }

    public function hasRole(string $role): bool
    {
        return $this->getRoles()->contains('name', $role);
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->getRoles()->whereIn('name', $roles)->isNotEmpty();
    }

    public function can($abilities, $arguments = []): bool
    {
        if ($this instanceof Admin && $this->hasRole('super_admin')) {
            return true;
        }

        if (is_string($abilities)) {
            return $this->hasPermission($abilities);
        }

        foreach ((array) $abilities as $ability) {
            if (! $this->hasPermission($ability)) {
                return false;
            }
        }

        return true;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this instanceof Admin && $this->hasRole('super_admin')) {
            return true;
        }

        return $this->getRoles()
            ->flatMap(fn (Role $role) => $role->permissions)
            ->contains('name', $permission);
    }

    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
        $this->unsetRelation('roles');
    }

    public function removeRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
        $this->unsetRelation('roles');
    }

    public function syncRoles(array $roles): void
    {
        $ids = Role::whereIn('name', $roles)->pluck('id');
        $this->roles()->sync($ids);
        $this->unsetRelation('roles');
    }

    protected function getRoles(): Collection
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        return $this->roles;
    }
}
