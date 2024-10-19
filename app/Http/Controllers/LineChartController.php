<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class LineChartController extends Controller
{
    public function getLineData(Request $request)
    {
        $selectedYear = $request->selectedYear;
        $selectedQuarter = $request->selectedQuarter;

        // Fetch minor offenses data
        $minorOffensesQuery = Student::whereHas('submittedMinorOffenses')
            ->join('submitted_minor_offenses', 'students.lrn', '=', 'submitted_minor_offenses.lrn')
            ->where('submitted_minor_offenses.student_schoolyear', $selectedYear);

        // If a quarter is selected, filter by it; otherwise, merge data for all quarters
        if ($selectedQuarter) {
            $minorOffensesQuery->where('submitted_minor_offenses.student_quarter', $selectedQuarter);
        }

        $minorOffenses = $minorOffensesQuery
            ->selectRaw('submitted_minor_offenses.committed_date, COUNT(*) as count')
            ->groupBy('submitted_minor_offenses.committed_date')
            ->orderBy('submitted_minor_offenses.committed_date') // Optional: order by committed date
            ->get()
            ->map(function ($offense) {
                return [
                    'date' => $offense->committed_date,
                    'count' => $offense->count,
                ];
            });

        // Fetch major offenses data
        $majorOffensesQuery = Student::whereHas('submittedMajorOffenses')
            ->join('submitted_major_offenses', 'students.lrn', '=', 'submitted_major_offenses.lrn')
            ->where('submitted_major_offenses.student_schoolyear', $selectedYear);

        // If a quarter is selected, filter by it; otherwise, merge data for all quarters
        if ($selectedQuarter) {
            $majorOffensesQuery->where('submitted_major_offenses.student_quarter', $selectedQuarter);
        }

        $majorOffenses = $majorOffensesQuery
            ->selectRaw('submitted_major_offenses.committed_date, COUNT(*) as count')
            ->groupBy('submitted_major_offenses.committed_date')
            ->orderBy('submitted_major_offenses.committed_date') // Optional: order by committed date
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
