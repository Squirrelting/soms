<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MinorPenaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $minorPenalties = [
            '1st Offense-Oral warning by the class adviser or subject teacher.',
            '2nd Offense-Violation report, conference with parents/guardians (last warning).',
            '3rd Offense-Imposition of Disciplinary action.',
        ];

        foreach ($minorPenalties as $penalty) {
            DB::table('minor_penalties')->insert([
                'minor_penalties' => $penalty,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
