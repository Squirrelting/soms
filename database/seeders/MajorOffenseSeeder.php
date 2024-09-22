<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MajorOffenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $majorOffenses = [
            'Tampering of school documents',
            'Falsifying signature of school authorities and/or teachers',
            'Drinking any form of liquor within the campus',
            'Smoking/vaping within the campus',
            'Coming to school under the influence of intoxicating drinks',
            'Possessing, using, and selling prohibited drugs',
            'Showing disrespect towards the teachers, any member of the staff, school authorities, and other students',
            'Assaulting, verbal or physical, teachers, students, school authorities and personnel',
            'Possessing of deadly weapons',
            'Committing acts that cause public scandal',
            'Inflicting injury/ies to others',
            'Belonging to or participating in the activities of fraternities and sororities and any organizations not recognized by the school',
            'Involvement in rambles and fights',
            'Prostitution',
            'Harassing and molesting students',
            'Extortion',
            'Stealing and theft within the school campus',
            'Bullying others',
            'Grave threats to others',
            'Vandalism - Graffiti - Causing damage to school properties and properties of others',
            'Cheating and other forms of dishonesty',
            'Reading, watching, writing, and circulating pornographic materials',
            'Committing acts that lead to immorality',
            'Live-in relationships',
            'Impersonation',
            'Gambling',
        ];

        foreach ($majorOffenses as $offense) {
            DB::table('major_offenses')->insert([
                'major_offenses' => $offense,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
