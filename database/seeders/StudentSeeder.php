<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('students')->insert([
            [
                'lrn' => '20240001',
                'name' => 'John Doe',
                'sex' => 'Male',
                'grade' => '10',
                'email' => 'john.doe@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240002',
                'name' => 'Jane Smith',
                'sex' => 'Female',
                'grade' => '9',
                'email' => 'jane.smith@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240003',
                'name' => 'Alice Johnson',
                'sex' => 'Female',
                'grade' => '11',
                'email' => 'alice.johnson@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240004',
                'name' => 'Bob Williams',
                'sex' => 'Male',
                'grade' => '12',
                'email' => 'bob.williams@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240005',
                'name' => 'Emma Davis',
                'sex' => 'Female',
                'grade' => '10',
                'email' => 'emma.davis@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240006',
                'name' => 'Liam Brown',
                'sex' => 'Male',
                'grade' => '9',
                'email' => 'liam.brown@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240007',
                'name' => 'Olivia Martinez',
                'sex' => 'Female',
                'grade' => '11',
                'email' => 'olivia.martinez@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240008',
                'name' => 'Noah Wilson',
                'sex' => 'Male',
                'grade' => '12',
                'email' => 'noah.wilson@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240009',
                'name' => 'Sophia Lee',
                'sex' => 'Female',
                'grade' => '10',
                'email' => 'sophia.lee@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'lrn' => '20240010',
                'name' => 'Jackson Taylor',
                'sex' => 'Male',
                'grade' => '9',
                'email' => 'jackson.taylor@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
