<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)   
    {
        $year = $request->input('year', date('Y')); // Get year from request, default to current year

        // Query offenses by sex for the given year
        $maleOffendersMinor = Student::where('sex', 'Male')
            ->whereHas('submittedMinorOffenses', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })->count();

        $femaleOffendersMinor = Student::where('sex', 'Female')
            ->whereHas('submittedMinorOffenses', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })->count();

        $maleOffendersMajor = Student::where('sex', 'Male')
            ->whereHas('submittedMajorOffenses', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })->count();

        $femaleOffendersMajor = Student::where('sex', 'Female')
            ->whereHas('submittedMajorOffenses', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })->count();

        // Prepare data for the frontend
        $offenseData = [
            'male' => $maleOffendersMinor + $maleOffendersMajor,
            'female' => $femaleOffendersMinor + $femaleOffendersMajor
        ];

       return Inertia::render('Dashboard', [
            'offenseData' => $offenseData,
            'year' => $year
       ]);
   }

   public function getChartData($year) 
   { 
    // Query offenses by sex for the given year
    $maleOffendersMinor = Student::where('sex', 'Male')
        ->whereHas('submittedMinorOffenses', function($query) use ($year) {
            $query->whereYear('created_at', $year);
        })->count();

    $femaleOffendersMinor = Student::where('sex', 'Female')
        ->whereHas('submittedMinorOffenses', function($query) use ($year) {
            $query->whereYear('created_at', $year);
        })->count();

    $maleOffendersMajor = Student::where('sex', 'Male')
        ->whereHas('submittedMajorOffenses', function($query) use ($year) {
            $query->whereYear('created_at', $year);
        })->count();

    $femaleOffendersMajor = Student::where('sex', 'Female')
        ->whereHas('submittedMajorOffenses', function($query) use ($year) {
            $query->whereYear('created_at', $year);
        })->count();

    // Prepare data for the frontend
    $offenseData = [
        'male' => $maleOffendersMinor + $maleOffendersMajor,
        'female' => $femaleOffendersMinor + $femaleOffendersMajor
    ];

    return response()->json($offenseData);
   }
}

