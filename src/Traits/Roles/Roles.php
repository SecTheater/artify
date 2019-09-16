<?php

namespace Artify\Artify\Traits\Roles;

use Illuminate\Support\Collection;

trait Roles
{
    protected function transformRolesToCollection($roles): Collection
    {
        return collect((array) $roles);
    }
    public function hasAllRole(array $roles): bool
    {
        $permissions = $this->getPermissions()->only($roles);
        if (count($roles) != $permissions->count()) {
            return false;
        }
        return $permissions->contains(function ($value, $role) {
            return $this->hasRole($role);
        });
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->transformRolesToCollection($roles)->contains(function ($role) {
            return $this->hasRole($role);
        });
    }

    public function hasRole(string $role): bool
    {
        return $this->getPermissions()->contains(function ($value, $key) use ($role) {
            return ($key === $role || (str_is($role, $key) || str_is($key, $role))) && $value;
        });
    }

    public function updateOrCreatePermission($permission, $value = true): bool
    {
        $permissions = $this->transformRolesToCollection(is_array($permission) ?: [$permission => $value]);
        $permissions = $this->getUserPermissions()->merge($permissions);
        return $this->savePermissions($permissions);
    }

    public function removePermission(...$permission): bool
    {
        $permissions = $this->getUserPermissions()->except($permission);
        return (bool) $this->savePermissions($permissions);
    }

    public function savePermissions(Collection $permissions): bool
    {
        return $this->fill(['permissions' => $permissions->toArray()])->save();
    }
    public function getUserPermissions(): Collection
    {
        return $this->transformRolesToCollection($this->permissions ?? []);
    }
    public function getRolePermissions(): Collection
    {
        return $this->roles->pluck('permissions')->collapse();
    }
    public function getPermissions(): Collection
    {
        return $this->getRolePermissions()->merge($this->getUserPermissions());
    }

    public function inRole(string $slug): bool
    {
        return $this->roles()->latest()->first()->slug === $slug;
    }
}
