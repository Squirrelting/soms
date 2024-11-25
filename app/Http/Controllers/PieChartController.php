<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;

class PieChartController extends Controller
{
    public function getPieData(Request $request)
    {
        $selectedYear = $request->selectedYear;
        $selectedQuarter = $request->selectedQuarter;

        // Get distinct LRN of male offenders for minor offenses
        $maleOffendersMinor = SubmittedMinorOffense::where('student_sex', 'Male')
            ->where('student_schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('student_quarter', $selectedQuarter);
            })
            ->distinct('lrn')
            ->pluck('lrn');

        // Get distinct LRN of male offenders for major offenses
        $maleOffendersMajor = SubmittedMajorOffense::where('student_sex', 'Male')
            ->where('student_schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('student_quarter', $selectedQuarter);
            })
            ->distinct('lrn')
            ->pluck('lrn');

        // Combine and get unique male offenders by LRN
        $uniqueMaleOffenders = $maleOffendersMinor->merge($maleOffendersMajor)->unique()->count();

        // Get distinct LRN of female offenders for minor offenses
        $femaleOffendersMinor = SubmittedMinorOffense::where('student_sex', 'Female')
            ->where('student_schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('student_quarter', $selectedQuarter);
            })
            ->distinct('lrn')
            ->pluck('lrn');

        // Get distinct LRN of female offenders for major offenses
        $femaleOffendersMajor = SubmittedMajorOffense::where('student_sex', 'Female')
            ->where('student_schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query) use ($selectedQuarter) {
                return $query->where('student_quarter', $selectedQuarter);
            })
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
