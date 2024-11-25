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

        // Fetch top minor offenses with committed_date for sorting
        $minorOffenses = SubmittedMinorOffense::selectRaw('minor_offense, COUNT(*) as count, MAX(committed_date) as latest_date')
            ->when($selectedQuarter, function ($query) use ($selectedYear, $selectedQuarter) {
                return $query->where('student_schoolyear', $selectedYear)
                             ->where('student_quarter', $selectedQuarter);
            }, function ($query) use ($selectedYear) {
                return $query->where('student_schoolyear', $selectedYear);
            })
            ->groupBy('minor_offense')
            ->orderByDesc('count')
            ->orderByDesc('latest_date') // Secondary sort by latest_date
            ->get();

        // Fetch top major offenses with committed_date for sorting
        $majorOffenses = SubmittedMajorOffense::selectRaw('major_offense, COUNT(*) as count, MAX(committed_date) as latest_date')
            ->when($selectedQuarter, function ($query) use ($selectedYear, $selectedQuarter) {
                return $query->where('student_schoolyear', $selectedYear)
                             ->where('student_quarter', $selectedQuarter);
            }, function ($query) use ($selectedYear) {
                return $query->where('student_schoolyear', $selectedYear);
            })
            ->groupBy('major_offense')
            ->orderByDesc('count')
            ->orderByDesc('latest_date') // Secondary sort by latest_date
            ->get();

        // Combine both minor and major offenses into a single collection
        $combinedOffenses = $minorOffenses->map(function ($offense) {
            return [
                'offense_name' => $offense->minor_offense,
                'count' => $offense->count,
                'latest_date' => $offense->latest_date,
            ];
        })->concat($majorOffenses->map(function ($offense) {
            return [
                'offense_name' => $offense->major_offense,
                'count' => $offense->count,
                'latest_date' => $offense->latest_date,
            ];
        }));

        // Sort the combined offenses by count and latest_date
        $topOffenses = $combinedOffenses->sort(function ($a, $b) {
            if ($a['count'] === $b['count']) {
                return strcmp($b['latest_date'], $a['latest_date']); // Compare dates (latest first)
            }
            return $b['count'] <=> $a['count']; // Compare counts
        })->take(5);

        // Prepare the data for the frontend
        $data = $topOffenses->values()->all(); // Reset keys and convert to array

        return response()->json($data);
    }
}
