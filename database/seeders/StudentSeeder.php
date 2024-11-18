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
                'lrn' => '202400011111',
                'firstname' => 'John',
                'middlename' => 'Li',
                'lastname' => 'Doe',
                'sex' => 'Male',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 7,  
                'section_id' => 1, 
                'email' => 'john.doe@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400022222',
                'firstname' => 'Jane',
                'middlename' => 'Dy',
                'lastname' => 'Smith',
                'sex' => 'Female',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 7,
                'section_id' => 2,
                'email' => 'jane.smith@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400033333',
                'firstname' => 'Alice',
                'middlename' => 'Go',
                'lastname' => 'Johnson',
                'sex' => 'Female',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 8,  // Assuming 'Section C' corresponds to grade_id 2
                'section_id' => 3, // Assuming 'Section C' corresponds to section_id 3
                'email' => 'alice.johnson@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400044444',
                'firstname' => 'Bob',
                'middlename' => 'So',
                'lastname' => 'Williams',
                'sex' => 'Male',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 8,
                'section_id' => 4, // Assuming 'Section D' corresponds to section_id 4
                'email' => 'bob.williams@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400055555',
                'firstname' => 'Emma',
                'middlename' => 'Vi',
                'lastname' => 'Davis',
                'sex' => 'Female',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 9,
                'section_id' => 5, // Assuming 'Section B' corresponds to section_id 2
                'email' => 'emma.davis@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400066666',
                'firstname' => 'Liam',
                'middlename' => 'Si',
                'lastname' => 'Brown',
                'sex' => 'Male',
                'schoolyear' => '2024-2025',
                'quarter' => '1st Quarter',
                'grade_id' => 9,
                'section_id' => 6,
                'email' => 'liam.brown@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400077777',
                'firstname' => 'Olivia',
                'middlename' => 'Sy',
                'lastname' => 'Martinez',
                'sex' => 'Female',
                'schoolyear' => '2024-2025',
                'quarter' => '2nd Quarter',
                'grade_id' => 10,
                'section_id' => 7,
                'email' => 'olivia.martinez@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400088888',
                'firstname' => 'Noah',
                'middlename' => 'Lo',
                'lastname' => 'Wilson',
                'sex' => 'Male',
                'schoolyear' => '2024-2025',
                'quarter' => '2nd Quarter',
                'grade_id' => 10,
                'section_id' => 8,
                'email' => 'noah.wilson@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400099999',
                'firstname' => 'Sophia',
                'middlename' => 'Li',
                'lastname' => 'Lee',
                'sex' => 'Female',
                'schoolyear' => '2024-2025',
                'quarter' => '2nd Quarter',
                'grade_id' => 11,
                'section_id' => 9,
                'email' => 'sophia.lee@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'lrn' => '202400100000',
                'firstname' => 'Jackson',
                'middlename' => 'So',
                'lastname' => 'Taylor',
                'sex' => 'Male',
                'schoolyear' => '2024-2025',
                'quarter' => '2nd Quarter',
                'grade_id' => 11,
                'section_id' => 10,
                'email' => 'jackson.taylor@example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
