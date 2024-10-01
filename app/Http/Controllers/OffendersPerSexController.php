<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MajorOffense;
use App\Models\MinorOffense;

class OffendersPerSexController extends Controller
{
    public function index()   
    {
        // Fetch minor offenses with male and female counts
        $submittedminorOffenses = MinorOffense::withCount([
            'submittedMinorOffenses as male_count' => function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
            },
            'submittedMinorOffenses as female_count' => function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)') // Grouped condition to check male and female counts
        ->get()
        ->map(function($offense) {
            $offense->type = 'Minor';  // Add offense type
            return $offense;
        });

        // Fetch major offenses with male and female counts
        $submittedmajorOffenses = MajorOffense::withCount([
            'submittedMajorOffenses as male_count' => function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
            },
            'submittedMajorOffenses as female_count' => function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)') // Grouped condition to check male and female counts
        ->get()
        ->map(function($offense) {
            $offense->type = 'Major';  // Add offense type
            return $offense;
        });

        return Inertia::render('Report/OffendersPerSex', [
            'submittedmajorOffenses' => $submittedmajorOffenses,
            'submittedminorOffenses' => $submittedminorOffenses,
        ]);
    }
}





