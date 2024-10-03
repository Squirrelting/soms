<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Permissions
        Permission::create(['name' => 'Manage Students']);
        Permission::create(['name' => 'Manage POD Users']);
        Permission::create(['name' => 'Manage Roles']);  // Add Manage Roles permission

        // Create roles and assign permissions
        Role::create(['name' => 'super-admin'])->givePermissionTo(['Manage Students', 'Manage POD Users', 'Manage Roles']);
        Role::create(['name' => 'admin'])->givePermissionTo(['Manage Students', 'Manage POD Users', 'Manage Roles']);
        Role::create(['name' => 'pod'])->givePermissionTo(['Manage Students']);
    }
}
