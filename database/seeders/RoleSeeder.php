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
    Permission::create(['name' => 'manage_students']);
    Permission::create(['name' => 'manage_standard']);
    Permission::create(['name' => 'Manage Roles']);
    Permission::create(['name' => 'Manage Permissions']);

    // Create roles and assign permissions
    Role::create(['name' => 'admin'])->givePermissionTo(['manage_students', 'manage_standard', 'Manage Roles', 'Manage Permissions']);
    Role::create(['name' => 'standard'])->givePermissionTo(['manage_students']);

      
    }
}
