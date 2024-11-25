<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;

class LineChartController extends Controller
{
    public function getLineData(Request $request)
    {
        $selectedYear = $request->selectedYear;
        $selectedQuarter = $request->selectedQuarter;

        // Fetch minor offenses data
        $minorOffenses = SubmittedMinorOffense::query()
            ->when($selectedYear, fn($query) => $query->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
            ->selectRaw('committed_date, COUNT(*) as count')
            ->groupBy('committed_date')
            ->orderBy('committed_date')
            ->get()
            ->map(function ($offense) {
                return [
                    'date' => $offense->committed_date,
                    'count' => $offense->count,
                ];
            });

        // Fetch major offenses data
        $majorOffenses = SubmittedMajorOffense::query()
            ->when($selectedYear, fn($query) => $query->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
            ->selectRaw('committed_date, COUNT(*) as count')
            ->groupBy('committed_date')
            ->orderBy('committed_date')
            ->get()
            ->map(function ($offense) {
                return [
                    'date' => $offense->committed_date,
                    'count' => $offense->count,
                ];
            });

        // Prepare data for frontend
        $data = [
            'minor' => $minorOffenses,
            'major' => $majorOffenses,
        ];

        return response()->json($data);
    }
}
