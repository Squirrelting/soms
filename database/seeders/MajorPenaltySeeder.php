<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MajorPenaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $majorPenalties = [
            '1st Offense-Oral warning by the class adviser or subject teacher.',
            '2nd Offense-Undergo counseling with the guidance counselor.',
            '3rd Offense-Violation report, conference with parents/guardians. Submission of Memorandum of Understanding of parents or guardians.',
        ];

        foreach ($majorPenalties as $penalty) {
            DB::table('major_penalties')->insert([
                'major_penalties' => $penalty,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
