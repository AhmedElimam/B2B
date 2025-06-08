<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'user',
            'permissions' => json_encode(['read'])
        ]);

        Role::create([
            'name' => 'admin',
            'permissions' => json_encode(['read', 'write', 'delete'])
        ]);
    }
} 