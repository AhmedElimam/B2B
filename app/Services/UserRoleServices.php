<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;

class UserRoleServices
{
    public function assignRole(User $user, string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(User $user, string $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->detach($role->id);
        }
    }

    public function hasRole(User $user, string $roleName): bool
    {
        return $user->roles()->where('name', $roleName)->exists();
    }
}
