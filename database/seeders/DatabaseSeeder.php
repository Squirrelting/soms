<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MinorOffenseSeeder::class,
            GradeSeeder::class,
            SectionSeeder::class,
            // StudentSeeder::class,
            MinorPenaltySeeder::class,
            MajorOffenseSeeder::class,
            MajorPenaltySeeder::class,
        ]);
    }
}
