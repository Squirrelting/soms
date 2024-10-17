<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class LineChartController extends Controller
{
    public function getLineData(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'school_year' => 'required|string',
            'quarter' => 'required|string',
        ]);

        // Fetch and filter data based on school year and quarter
        $minorOffenses = Student::whereHas('submittedMinorOffenses')
            ->join('submitted_minor_offenses', 'students.lrn', '=', 'submitted_minor_offenses.lrn')
            ->where('submitted_minor_offenses.student_school_year', $request->school_year) 
            ->where('submitted_minor_offenses.student_quarter', $request->quarter)
            ->selectRaw('date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        $majorOffenses = Student::whereHas('submittedMajorOffenses')
            ->join('submitted_major_offenses', 'students.lrn', '=', 'submitted_major_offenses.lrn')
            ->where('submitted_major_offenses.student_school_year', $request->school_year) 
            ->where('submitted_major_offenses.student_quarter', $request->quarter)
            ->selectRaw('date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        // Prepare data for frontend
        $data = [
            'minor' => $minorOffenses,
            'major' => $majorOffenses,
        ];

        return response()->json($data);
    }
}
