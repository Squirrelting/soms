<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;

class PieChartController extends Controller
{
    public function getPieData(Request $request)
    {
        $selectedYear = $request->selectedYear;
        $selectedQuarter = $request->selectedQuarter;

        $maleOffendersMinor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->groupBy('firstname', 'middlename', 'lastname') // Group by full name
            ->count(); // Count the number of unique groups

        $femaleOffendersMinor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->groupBy('firstname', 'middlename', 'lastname') // Group by full name
            ->count(); // Count the number of unique groups

        $maleOffendersMajor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->groupBy('firstname', 'middlename', 'lastname') // Group by full name
            ->count(); // Count the number of unique groups

        $femaleOffendersMajor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->groupBy('firstname', 'middlename', 'lastname') // Group by full name
            ->count(); // Count the number of unique groups



        // Prepare data for the frontend
        $offenseData = [
            'male' => $maleOffendersMinor + $maleOffendersMajor,
            'female' => $femaleOffendersMinor + $femaleOffendersMajor
        ];

        return response()->json($offenseData);
    }
}
