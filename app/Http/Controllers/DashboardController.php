<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
        $selectedYear = $request->input('selectedYear');
        $selectedQuarter = $request->input('selectedQuarter');
    
      
        
        // Fetch distinct school years and quarters
        $getMajorSchoolYears = SubmittedMajorOffense::select('student_schoolyear', 'student_quarter')->distinct();
        $getSchoolYears = SubmittedMinorOffense::select('student_schoolyear', 'student_quarter')
            ->distinct()
            ->union($getMajorSchoolYears)
            ->get();
        
        $groupedSchoolYears = [];
        foreach ($getSchoolYears as $item) {
            $year = $item->student_schoolyear;
            $quarter = $item->student_quarter;
            
            if (!isset($groupedSchoolYears[$year])) {
                $groupedSchoolYears[$year] = [
                    'student_schoolyear' => $year,
                    'quarters' => []
                ];
            }
            
            if (!in_array($quarter, $groupedSchoolYears[$year]['quarters'])) {
                $groupedSchoolYears[$year]['quarters'][] = $quarter;
            }
        }
        
        foreach ($groupedSchoolYears as &$schoolYear) {
            usort($schoolYear['quarters'], function ($a, $b) use ($quarterOrder) {
                return array_search($a, $quarterOrder) - array_search($b, $quarterOrder);
            });
        }
        
        $finalResult = array_values($groupedSchoolYears);
        
    
        return Inertia::render('Dashboard', [
            'schoolYears' => $finalResult,
            'selectedYear' => $selectedYear,  
            'selectedQuarter' => $selectedQuarter,  
        ]);
    }

    public function getTableData(Request $request, $selectedSchoolyear, $selectedQuarter = null) 
    {
        $sortColumn = $request->input('sortColumn', 'updated_at');  
        $sortOrder = $request->input('sortOrder', 'desc'); 

        $filteredStudents = Student::with(['grade', 'section'])
            ->when($selectedSchoolyear, fn($query) => $query->where('schoolyear', $selectedSchoolyear))
            ->when($selectedQuarter, fn($query) => $query->where('quarter', $selectedQuarter))
            ->withCount([
                'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
                'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
            ])
            ->orderBy($sortColumn, $sortOrder)
            ->get();
    
        // Paginate the filtered result manually
        $students = $filteredStudents->take(5)->values();

        return response()->json([
            'students' => $students
        ]);
    }

public function getGradeData(Request $request, $selectedSchoolyear, $selectedQuarter = null) 
{
    // Fetch offenses per grade with necessary filters
    $offensesPerGrade = Student::select('grade_id')
        ->where(function ($query) {
            $query->whereHas('submittedMinorOffensesWithNoSanction')
                  ->orWhereHas('submittedMajorOffensesWithNoSanction');
        })
        ->when($selectedSchoolyear, function($query) use ($selectedSchoolyear) {
            $query->where('schoolyear', $selectedSchoolyear);
        })
        ->when($selectedQuarter, function($query) use ($selectedQuarter) {
            $query->where('quarter', $selectedQuarter);
        })
        ->groupBy('grade_id')
        ->selectRaw('grade_id, COUNT(DISTINCT lrn) as offense_count')
        ->get()
        ->keyBy('grade_id');

    // Define all grades and ensure zeroes for missing grades
    $allGrades = [7, 8, 9, 10, 11, 12];
    $offensesPerGradeWithZeroes = collect($allGrades)->map(function ($grade_id) use ($offensesPerGrade) {
        return [
            'grade_id' => $grade_id,
            'offense_count' => $offensesPerGrade->get($grade_id)->offense_count ?? 0
        ];
    });

    return response()->json([
        'offensesPerGrade' => $offensesPerGradeWithZeroes,
    ]);
}

    
}
