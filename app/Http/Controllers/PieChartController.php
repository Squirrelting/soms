<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class PieChartController extends Controller
{
    public function getPieData(Request $request)
    {
        $selectedYear = $request->selectedYear;
        $selectedQuarter = $request->selectedQuarter;

        // Get the unique counts of male offenders for minor offenses
        $maleOffendersMinor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->distinct('lrn') // Count unique lrn values
            ->count('lrn'); // Count unique lrn directly

        // Get the unique counts of female offenders for minor offenses
        $femaleOffendersMinor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->distinct('lrn')
            ->count('lrn');

        // Get the unique counts of male offenders for major offenses
        $maleOffendersMajor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->distinct('lrn')
            ->count('lrn');

        // Get the unique counts of female offenders for major offenses
        $femaleOffendersMajor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->distinct('lrn')
            ->count('lrn');

        // Combine the results
        $offenseData = [
            'male' => $maleOffendersMinor + $maleOffendersMajor,
            'female' => $femaleOffendersMinor + $femaleOffendersMajor
        ];

        return response()->json($offenseData);
    }
}
