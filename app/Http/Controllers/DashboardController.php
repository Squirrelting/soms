<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
        $selectedYear = $request->input('selectedYear');
        $selectedQuarter = $request->input('selectedQuarter');
        $sortColumn = $request->input('sortColumn', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $search = $request->input('search');


        $perPage = $request->input('perPage', 10);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $studentsQuery = Student::with(['grade', 'section'])
        ->withCount([
            'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
            'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
        ])
        ->when($selectedYear, fn($query) => $query->where('schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($query) => $query->where('quarter', $selectedQuarter))
        ->when($search, fn($query) => $query->where(function ($query) use ($search) {
            $query->where('firstname', 'like', "%{$search}%")
                  ->orWhere('middlename', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('grade_id', 'like', "%{$search}%")
                  ->orWhereHas('section', fn($q) => 
                  $q->where('section', 'like', "%{$search}%")
              )                  ->orWhere('sex', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
        }))
        ->orderBy($sortColumn, $sortOrder);


    // Apply limit and offset for manual pagination
    $students = $studentsQuery
        ->get();

    // Create the LengthAwarePaginator instance
    $students = new LengthAwarePaginator(
        $students->forPage($currentPage, $perPage),
        $students->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );
      
        
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
        
        $user = $request->user();
    
        return Inertia::render('Dashboard', [
            'user' => $user,
            'students' => $students,
            'perPage' => $perPage,
            'schoolYears' => $finalResult,
            'selectedYear' => $selectedYear,  
            'selectedQuarter' => $selectedQuarter,  
        ]);
    }
    private function getOffenseCounts($type, $sanction, $selectedSchoolyear, $selectedQuarter) {
        $model = $type === 'major' ? SubmittedMajorOffense::class : SubmittedMinorOffense::class;
        return $model::select('student_grade as grade_id')
            ->where('sanction', $sanction)
            ->when($selectedSchoolyear, fn($query) => $query->where('student_schoolyear', $selectedSchoolyear))
            ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
            ->groupBy('student_grade')
            ->selectRaw('student_grade as grade_id, COUNT(*) as offense_count')
            ->pluck('offense_count', 'grade_id');
    }

    
    public function getGradeData(Request $request, $selectedSchoolyear, $selectedQuarter = null)
    {
        // Define all grades for normalization
        $allGrades = [7, 8, 9, 10, 11, 12];
    
// Get distinct LRNs for minor offenders
$minorOffendersLRNs = SubmittedMinorOffense::select('lrn', 'student_grade as grade_id')
    ->when($selectedSchoolyear, fn($query) => $query->where('student_schoolyear', $selectedSchoolyear))
    ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
    ->get();

// Get distinct LRNs for major offenders
$majorOffendersLRNs = SubmittedMajorOffense::select('lrn', 'student_grade as grade_id')
    ->when($selectedSchoolyear, fn($query) => $query->where('student_schoolyear', $selectedSchoolyear))
    ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
    ->get();

// Combine minor and major LRNs
$combinedOffenders = $minorOffendersLRNs->concat($majorOffendersLRNs);

// Group by grade and count distinct LRNs
$offenders = $combinedOffenders
    ->groupBy('grade_id')
    ->map(function ($groupedOffenders) {
        // Count distinct LRNs in each grade
        return $groupedOffenders->pluck('lrn')->unique()->count();
    });

// Normalize offenders data with all grades
$offendersByGrade = collect($allGrades)->mapWithKeys(function ($gradeId) use ($offenders) {
    return [$gradeId => $offenders[$gradeId] ?? 0];
});

// You can now use $offendersByGrade as needed

    
        $minorUnresolved = $this->getOffenseCounts('minor', 0, $selectedSchoolyear, $selectedQuarter);
        $majorUnresolved = $this->getOffenseCounts('major', 0, $selectedSchoolyear, $selectedQuarter);
        $totalUnresolved = collect($allGrades)->mapWithKeys(function ($gradeId) use ($minorUnresolved, $majorUnresolved) {
            $minorCount = $minorUnresolved[$gradeId] ?? 0;
            $majorCount = $majorUnresolved[$gradeId] ?? 0;
            
            // Sum the minor and major unresolved offenses
            return [
                $gradeId => $minorCount + $majorCount
            ];
        })->toArray();
        
    
        // Resolved offenses by grade
        $minorResolved = $this->getOffenseCounts('minor', 1, $selectedSchoolyear, $selectedQuarter);
        $majorResolved = $this->getOffenseCounts('major', 1, $selectedSchoolyear, $selectedQuarter);
        $totalResolved = collect($allGrades)->mapWithKeys(function ($gradeId) use ($minorResolved, $majorResolved) {
            $minorCount = $minorResolved[$gradeId] ?? 0;
            $majorCount = $majorResolved[$gradeId] ?? 0;
            
            // Sum the minor and major unresolved offenses
            return [
                $gradeId => $minorCount + $majorCount
            ];
        })->toArray();
        

// Aggregate major and minor offenses
$majorOffenses = SubmittedMajorOffense::select('student_grade as grade_id')
    ->when($selectedSchoolyear, fn($query) => $query->where('student_schoolyear', $selectedSchoolyear))
    ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
    ->groupBy('student_grade')
    ->selectRaw('student_grade as grade_id, COUNT(*) as major_offenses')
    ->pluck('major_offenses', 'grade_id');

$minorOffenses = SubmittedMinorOffense::select('student_grade as grade_id')
    ->when($selectedSchoolyear, fn($query) => $query->where('student_schoolyear', $selectedSchoolyear))
    ->when($selectedQuarter, fn($query) => $query->where('student_quarter', $selectedQuarter))
    ->groupBy('student_grade')
    ->selectRaw('student_grade as grade_id, COUNT(*) as minor_offenses')
    ->pluck('minor_offenses', 'grade_id');

// Combine and sum the offenses
$totalOffenses = collect($majorOffenses)
    ->mapWithKeys(function ($majorOffensesCount, $gradeId) use ($minorOffenses) {
        return [$gradeId => $majorOffensesCount + ($minorOffenses[$gradeId] ?? 0)];
    })
    ->union($minorOffenses->filter(fn($minorOffensesCount, $gradeId) => !$majorOffenses->has($gradeId)))
    ->toArray();


        // Normalize data with all grades
        $offensesPerGradeWithZeroes = collect($allGrades)->map(function ($grade_id) use ($offendersByGrade, $totalUnresolved, $totalResolved, $totalOffenses) {
            return [
                'grade_id' => $grade_id,
                'offenders' => $offendersByGrade[$grade_id] ?? 0,
                'unresolved' => $totalUnresolved[$grade_id] ?? 0,
                'resolved' => $totalResolved[$grade_id] ?? 0,
                'offenses' => $totalOffenses[$grade_id] ?? 0,
            ];
        });
    
        // Return as JSON
        return response()->json([
            'offendersPerGrade' => $offensesPerGradeWithZeroes,
        ]);
    }
    
    
    
}
