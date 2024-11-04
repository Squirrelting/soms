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
            ->groupBy('lrn') 
            ->count(); 

        $femaleOffendersMinor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMinorOffenses')
            ->groupBy('lrn') 
            ->count(); 

        $maleOffendersMajor = Student::where('sex', 'Male')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->groupBy('lrn') 
            ->count(); 

        $femaleOffendersMajor = Student::where('sex', 'Female')
            ->where('schoolyear', $selectedYear)
            ->when($selectedQuarter, function ($query, $selectedQuarter) {
                return $query->where('quarter', $selectedQuarter);
            })
            ->whereHas('submittedMajorOffenses')
            ->groupBy('lrn') 
            ->count(); 




        $offenseData = [
            'male' => $maleOffendersMinor + $maleOffendersMajor,
            'female' => $femaleOffendersMinor + $femaleOffendersMajor
        ];

        return response()->json($offenseData);
    }
}
