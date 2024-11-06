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

        // Get distinct LRN of male offenders for minor offenses
        $maleOffendersMinor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->distinct('lrn')
            ->pluck('lrn');

        // Get distinct LRN of male offenders for major offenses
        $maleOffendersMajor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->distinct('lrn')
            ->pluck('lrn');

        // Combine and get unique male offenders by LRN
        $uniqueMaleOffenders = $maleOffendersMinor->merge($maleOffendersMajor)->unique()->count();

        // Get distinct LRN of female offenders for minor offenses
        $femaleOffendersMinor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->distinct('lrn')
            ->pluck('lrn');

        // Get distinct LRN of female offenders for major offenses
        $femaleOffendersMajor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->distinct('lrn')
            ->pluck('lrn');

        // Combine and get unique female offenders by LRN
        $uniqueFemaleOffenders = $femaleOffendersMinor->merge($femaleOffendersMajor)->unique()->count();

        // Combine the results for the pie chart
        $offenseData = [
            'male' => $uniqueMaleOffenders,
            'female' => $uniqueFemaleOffenders
        ];

        return response()->json($offenseData);
    }

}
