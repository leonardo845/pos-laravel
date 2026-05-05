<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'superadmin'],
            ['name' => 'Owner',       'slug' => 'owner'],
            ['name' => 'Admin',       'slug' => 'admin'],
            ['name' => 'Cashier',     'slug' => 'cashier'],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Users
        $users = [
            ['role' => 'superadmin', 'name' => 'Super Admin', 'username' => 'superadmin'],
            ['role' => 'owner',      'name' => 'Owner',        'username' => 'owner'],
            ['role' => 'admin',      'name' => 'Admin',        'username' => 'admin'],
            ['role' => 'cashier',    'name' => 'Cashier',      'username' => 'cashier'],
        ];

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role'])->first();
            User::create([
                'role_id'  => $role->id,
                'name'     => $userData['name'],
                'username' => $userData['username'],
                'password' => Hash::make('admin123'),
            ]);
        }

        // Outlet
        Outlet::create([
            'name'      => 'Main Outlet',
            'address'   => 'Jl. Contoh No. 1',
            'phone'     => '08100000001',
            'email'     => 'main@outlet.com',
            'is_active' => true,
        ]);
    }
}
