<?php

namespace App\Http\Controllers;

use App\Models\SubmittedMinorOffense;
use App\Models\SubmittedMajorOffense;
use Illuminate\Http\Request;

class BarGraphController extends Controller
{
    public function getBarData(Request $request) 
{
    $selectedYear = $request->selectedYear;
    $selectedQuarter = $request->selectedQuarter;

    // Fetch top minor offenses based on their frequency
    $minorOffenses = SubmittedMinorOffense::selectRaw('minor_offense, COUNT(*) as count')
        ->when($selectedQuarter, function ($query) use ($selectedYear, $selectedQuarter) {
            return $query->where('student_schoolyear', $selectedYear)
                         ->where('student_quarter', $selectedQuarter);
        }, function ($query) use ($selectedYear) {
            return $query->where('student_schoolyear', $selectedYear);
        })
        ->groupBy('minor_offense')
        ->orderByDesc('count')
        ->get();

    // Fetch top major offenses based on their frequency
    $majorOffenses = SubmittedMajorOffense::selectRaw('major_offense, COUNT(*) as count')
        ->when($selectedQuarter, function ($query) use ($selectedYear, $selectedQuarter) {
            return $query->where('student_schoolyear', $selectedYear)
                         ->where('student_quarter', $selectedQuarter);
        }, function ($query) use ($selectedYear) {
            return $query->where('student_schoolyear', $selectedYear);
        })
        ->groupBy('major_offense')
        ->orderByDesc('count')
        ->get();

    // Combine both minor and major offenses into a single collection
    $combinedOffenses = $minorOffenses->map(function ($offense) {
        return [
            'offense_name' => $offense->minor_offense,
            'count' => $offense->count,
        ];
    })->concat($majorOffenses->map(function ($offense) {
        return [
            'offense_name' => $offense->major_offense,
            'count' => $offense->count,
        ];
    }));

    // Sort the combined offenses by count and take the top 5
    $topOffenses = $combinedOffenses->sortByDesc('count')->take(5);

    // Prepare the data for the frontend
    $data = $topOffenses->values()->all(); // Reset keys and convert to array

    return response()->json($data);
}

}
