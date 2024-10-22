<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Extract filter inputs
        $search = $request->input('search');
        $sanction = $request->input('sanction');
        $offenseFilter = $request->input('offenseFilter');
        $sex = $request->input('sex');
        $grade = $request->input('grade');
        $section = $request->input('section');
        $selectedYear = $request->input('selectedYear');
        $selectedQuarter = $request->input('selectedQuarter');
        $perPage = 1; 
        
        // Retrieve and map minor offenses with pagination
        $submittedminorOffenses = SubmittedMinorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($offenseFilter === 'Minor', fn($q) => $q->whereNotNull('minor_offense'))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction)) 
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage) // Use paginate instead of get
            ->through(function ($offense) { // Use through() for mapped values
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });
    
        // Retrieve and map major offenses with pagination
        $submittedmajorOffenses = SubmittedMajorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($offenseFilter === 'Major', fn($q) => $q->whereNotNull('major_offense'))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction)) 
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage) // Use paginate instead of get
            ->through(function ($offense) { 
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });

        // Determine which offenses to return based on filter
        $offensesData = collect();
        if ($offenseFilter === 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter === 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            // Merging paginated collections
            $mergedOffenses = $submittedminorOffenses->getCollection()->merge($submittedmajorOffenses->getCollection());
    
            // Create a new paginator for the merged collection
            $offensesData = new \Illuminate\Pagination\LengthAwarePaginator(
                $mergedOffenses, 
                max($submittedminorOffenses->total(), $submittedmajorOffenses->total()), // Use the maximum total count
                $perPage,
                $request->input('page', 1), // Current page
                ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
            );
        }

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
    
        return Inertia::render('Report/Index', [
            'offenses' => $offensesData,
            'grades' => Grade::all(),
            'schoolYears' => $finalResult
        ]);
    }
    
    public function printoffenses(Request $request)
    {
        // Extract filter inputs
        $search = $request->input('search');
        $sanction = $request->input('sanction');
        $offenseFilter = $request->input('offenseFilter');
        $sex = $request->input('sex');
        $grade = $request->input('grade');
        $section = $request->input('section');
        $selectedYear = $request->input('selectedYear');
        $selectedQuarter = $request->input('selectedQuarter');
        $perPage = 1; 
        
        // Retrieve and map minor offenses with pagination
        $submittedminorOffenses = SubmittedMinorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($offenseFilter === 'Minor', fn($q) => $q->whereNotNull('minor_offense'))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction)) 
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage) // Use paginate instead of get
            ->through(function ($offense) { // Use through() for mapped values
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });
    
        // Retrieve and map major offenses with pagination
        $submittedmajorOffenses = SubmittedMajorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($offenseFilter === 'Major', fn($q) => $q->whereNotNull('major_offense'))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction)) 
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage) // Use paginate instead of get
            ->through(function ($offense) { 
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });

        // Determine which offenses to return based on filter
        $offensesData = collect();
        if ($offenseFilter === 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter === 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            // Merging paginated collections
            $mergedOffenses = $submittedminorOffenses->getCollection()->merge($submittedmajorOffenses->getCollection());
    
            // Create a new paginator for the merged collection
            $offensesData = new \Illuminate\Pagination\LengthAwarePaginator(
                $mergedOffenses, 
                max($submittedminorOffenses->total(), $submittedmajorOffenses->total()), // Use the maximum total count
                $perPage,
                $request->input('page', 1), // Current page
                ['path' => $request->url(), 'query' => $request->query()] // Preserve query parameters
            );
        }

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
    

        $imagePath = public_path('Images/SCNHS-Logo.png');
        $date = Carbon::now()->format('F j, Y');

        $pdf = Pdf::loadView('print-template.print-offenses', [
            'offensesData'   => $offensesData,
            'imagePath'      => $imagePath,
            'date'           => $date,
            'offenseFilter'  => $offenseFilter,
        ])->setPaper('legal', 'landscape'); 

        return $pdf->stream('List-of-Offenses.pdf');
    }

}
