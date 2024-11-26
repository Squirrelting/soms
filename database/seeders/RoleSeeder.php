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
        Permission::create(['name' => 'Manage Sections']);  
        Permission::create(['name' => 'Student Offenses']);  
        Permission::create(['name' => 'Manage Signatory']);  
        Permission::create(['name' => 'Manage Users']);
        Permission::create(['name' => 'Manage Offenses']);
        Permission::create(['name' => 'Manage Roles']);


        // Create roles and assign permissions
        Role::create(['name' => 'SUPER-ADMIN'])->givePermissionTo(['Manage Sections', 'Student Offenses', 'Manage Signatory', 'Manage Users', 'Manage Offenses', 'Manage Roles']);
        Role::create(['name' => 'ADMIN'])->givePermissionTo(['Manage Sections', 'Student Offenses', 'Manage Signatory', 'Manage Users', 'Manage Offenses', 'Manage Roles']);
        Role::create(['name' => 'POD'])->givePermissionTo(['Student Offenses']);
        Role::create(['name' => 'ADVISER'])->givePermissionTo(['Student Offenses']);

    }
}
