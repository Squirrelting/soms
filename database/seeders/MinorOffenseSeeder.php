<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MinorOffenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $minorOffenses = [
            'Habitual tardiness, absenteeism, and cutting class',
            'Loitering',
            'Littering',
            'Going over the fences',
            'Wearing of unauthorized school uniform and I.D.',
            'Improper haircut and hair dyeing (boys)',
            'Wearing of earrings, ring with spikes, and belt with big buckles (boys)',
            'Wearing of make-up, using lipstick, and hair dyeing (girls)',
            'Wearing indecent attire',
            'Frequent staying in places considered as off-limit areas by the school',
            'Quarrels among classmates, like shouting, taunting, teasing',
            'Doing activities that disturb classes',
            'Not following the minimum health standard protocol such as physical distancing, wearing mask and frequent washing of hands',
        ];

        foreach ($minorOffenses as $offense) {
            DB::table('minor_offenses')->insert([
                'minor_offenses' => $offense,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
