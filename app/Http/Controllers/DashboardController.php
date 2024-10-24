<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $sortColumn = $request->input('sortColumn', 'updated_at');  
        $sortOrder = $request->input('sortOrder', 'desc');  

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
        };
        $finalResult = array_values($groupedSchoolYears);

        
            // Fetch students with their grade, section, and count offenses
    $students = Student::with(['grade', 'section'])
    ->withCount([
        'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
        'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'])
    ->orderBy($sortColumn, $sortOrder)
    ->paginate(5);


        return Inertia::render('Dashboard', [
            'students' => $students,
            'schoolYears' => $finalResult
        ]);
    }

    
}
