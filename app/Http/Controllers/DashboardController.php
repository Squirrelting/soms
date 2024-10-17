<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $getMajorSchoolYears = SubmittedMajorOffense::select('student_schoolyear', 'student_quarter')
            ->distinct();

        // Get student_schoolyear and student_quarter from SubmittedMinorOffense and merge using union
        $getSchoolYears = SubmittedMinorOffense::select('student_schoolyear', 'student_quarter')
            ->distinct()
            ->union($getMajorSchoolYears)
            ->get();

        // Process the results to group by student_schoolyear and list the quarters
        $groupedSchoolYears = [];

        foreach ($getSchoolYears as $item) {
            $year = $item->student_schoolyear;
            $quarter = $item->student_quarter;

            // Check if the school year is already in the array
            if (!isset($groupedSchoolYears[$year])) {
                // Initialize with the school year and an empty quarters array
                $groupedSchoolYears[$year] = [
                    'student_schoolyear' => $year,
                    'quarters' => []
                ];
            }

            // Avoid duplicate quarters
            if (!in_array($quarter, $groupedSchoolYears[$year]['quarters'])) {
                $groupedSchoolYears[$year]['quarters'][] = $quarter;
            }
        }

        // Re-index the array to ensure proper numeric indexing
        $finalResult = array_values($groupedSchoolYears);
        return Inertia::render('Dashboard', [
            'schoolYears' => $finalResult
        ]);
    }

}
