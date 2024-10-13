<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current timestamp for created_at and updated_at fields
        $timestamp = Carbon::now();

        // Insert grades from 7 to 12
        foreach (range(7, 12) as $grade) {
            DB::table('grade')->insert([
                'id' => $grade, // Assuming id is the same as grade number
                'grade' => (string)$grade, // Converting to string if the grade column is a string type
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
