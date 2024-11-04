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
        $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
    
    // Fetch offenses per grade
    $offensesPerGrade = Student::select('grade_id')
        ->whereHas('submittedMinorOffensesWithNoSanction')
        ->orWhereHas('submittedMajorOffensesWithNoSanction')
        ->distinct('lrn')
        ->groupBy('grade_id')
        ->selectRaw('grade_id, COUNT(DISTINCT lrn) as offense_count')
        ->get()
        ->keyBy('grade_id'); // Use keyBy to easily access grades

    // Define all grades
    $allGrades = [7, 8, 9, 10, 11, 12];
    $offensesPerGradeWithZeroes = collect($allGrades)->map(function ($grade_id) use ($offensesPerGrade) {
        return [
            'grade_id' => $grade_id,
            'offense_count' => $offensesPerGrade->get($grade_id)->offense_count ?? 0
        ];
    });
    
        // Other data you already have
        $getMajorSchoolYears = SubmittedMajorOffense::select('student_schoolyear', 'student_quarter')
            ->distinct();
    
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
        
        // Sort the quarters in each school year
        foreach ($groupedSchoolYears as &$schoolYear) {
            usort($schoolYear['quarters'], function ($a, $b) use ($quarterOrder) {
                return array_search($a, $quarterOrder) - array_search($b, $quarterOrder);
            });
        }
        
        $finalResult = array_values($groupedSchoolYears);
    
        // Fetch students with their grade, section, and count offenses
        $students = Student::with(['grade', 'section'])
            ->withCount([
                'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
                'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
            ])
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(5);
    
        return Inertia::render('Dashboard', [
            'students' => $students,
            'schoolYears' => $finalResult,
            'offensesPerGrade' => $offensesPerGradeWithZeroes
        ]);
    }
    

    
}
