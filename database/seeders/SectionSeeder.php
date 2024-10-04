<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current time for created_at and updated_at fields
        $timestamp = Carbon::now();

        // Insert sections data
        DB::table('section')->insert([
            [
                'section' => 'Section A',
                'grade_id' => 1, // Make sure this grade_id exists in the 'grade' table
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section B',
                'grade_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section C',
                'grade_id' => 2,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section D',
                'grade_id' => 2,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section E',
                'grade_id' => 3,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section F',
                'grade_id' => 3,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            // Add more sections as needed
        ]);
    }
}
