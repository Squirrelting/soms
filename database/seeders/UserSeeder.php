<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email'=> 'admin@gmail.com',
                'password'=> 'admin1234',
                'role'=> 'admin'
            ],
            [
                'name' => 'Standard',
                'email'=> 'standard@gmail.com',
                'password'=> 'standard1234',
                'role'=> 'standard'
            ]

        ];

        foreach ($users as $user) {
            $created_user = User::create([
                'name' => $user['name'],
                'email'=> $user['email'],
                'password'=> Hash::make($user['password']),
            ]);
            
            $created_user->assignRole($user['role']);
        }
    }
}
