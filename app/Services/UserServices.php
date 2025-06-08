<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserServices
{
    /**
     * Get all users
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return User::with('roles')->get();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role if not specified
        $defaultRole = Role::where('name', 'user')->first();
        if ($defaultRole) {
            $user->roles()->attach($defaultRole->id);
        }

        return $user->load('roles');
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
        ];

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        // Update roles if provided
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user->load('roles');
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    /**
     * Assign role to user
     *
     * @param int $userId
     * @param int $roleId
     * @return User
     */
    public function assignRole(int $userId, int $roleId): User
    {
        $user = User::findOrFail($userId);
        $user->roles()->attach($roleId);
        return $user->load('roles');
    }

    /**
     * Remove role from user
     *
     * @param int $userId
     * @param int $roleId
     * @return User
     */
    public function removeRole(int $userId, int $roleId): User
    {
        $user = User::findOrFail($userId);
        $user->roles()->detach($roleId);
        return $user->load('roles');
    }
}