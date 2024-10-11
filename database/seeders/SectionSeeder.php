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
            [
                'section' => 'Section G',
                'grade_id' => 4,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section H',
                'grade_id' => 4,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section I',
                'grade_id' => 5,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section J',
                'grade_id' => 5,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section K',
                'grade_id' => 6,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section L',
                'grade_id' => 6,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section M',
                'grade_id' => 7,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section N',
                'grade_id' => 7,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section O',
                'grade_id' => 8,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section P',
                'grade_id' => 8,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section Q',
                'grade_id' => 9,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section R',
                'grade_id' => 9,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section S',
                'grade_id' => 10,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section T',
                'grade_id' => 10,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section U',
                'grade_id' => 11,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section V',
                'grade_id' =>11,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section W',
                'grade_id' => 12,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'section' => 'Section X',
                'grade_id' => 12,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}