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
        // Get current time for created_at and updated_at fields
        $timestamp = Carbon::now();

        // Insert students data
        DB::table('students')->insert([
            [
                'lrn' => '20240001',
                'firstname' => 'John',
                'lastname' => 'Doe',
                'sex' => 'Male',
                'grade_id' => 1,  // Assuming 'Section A' corresponds to grade_id 1
                'section_id' => 1, // Assuming 'Section A' corresponds to section_id 1
                'email' => 'john.doe@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240002',
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'sex' => 'Female',
                'grade_id' => 1,
                'section_id' => 1,
                'email' => 'jane.smith@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240003',
                'firstname' => 'Alice',
                'lastname' => 'Johnson',
                'sex' => 'Female',
                'grade_id' => 2,  // Assuming 'Section C' corresponds to grade_id 2
                'section_id' => 3, // Assuming 'Section C' corresponds to section_id 3
                'email' => 'alice.johnson@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240004',
                'firstname' => 'Bob',
                'lastname' => 'Williams',
                'sex' => 'Male',
                'grade_id' => 2,
                'section_id' => 4, // Assuming 'Section D' corresponds to section_id 4
                'email' => 'bob.williams@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240005',
                'firstname' => 'Emma',
                'lastname' => 'Davis',
                'sex' => 'Female',
                'grade_id' => 1,
                'section_id' => 2, // Assuming 'Section B' corresponds to section_id 2
                'email' => 'emma.davis@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240006',
                'firstname' => 'Liam',
                'lastname' => 'Brown',
                'sex' => 'Male',
                'grade_id' => 1,
                'section_id' => 2,
                'email' => 'liam.brown@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240007',
                'firstname' => 'Olivia',
                'lastname' => 'Martinez',
                'sex' => 'Female',
                'grade_id' => 2,
                'section_id' => 4,
                'email' => 'olivia.martinez@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240008',
                'firstname' => 'Noah',
                'lastname' => 'Wilson',
                'sex' => 'Male',
                'grade_id' => 2,
                'section_id' => 3,
                'email' => 'noah.wilson@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240009',
                'firstname' => 'Sophia',
                'lastname' => 'Lee',
                'sex' => 'Female',
                'grade_id' => 1,
                'section_id' => 1,
                'email' => 'sophia.lee@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '20240010',
                'firstname' => 'Jackson',
                'lastname' => 'Taylor',
                'sex' => 'Male',
                'grade_id' => 1,
                'section_id' => 2,
                'email' => 'jackson.taylor@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
