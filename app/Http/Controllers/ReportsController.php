<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use App\Models\MajorOffense;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\offendersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sanction = $request->input('sanction');
        $offenseFilter = $request->input('offenseFilter');
        $sex = $request->input('sex');
        $grade = $request->input('grade');
        $section = $request->input('section');
        $selectedYear = $request->input('selectedYear');
        $selectedQuarter = $request->input('selectedQuarter');
        $selectedOffense = $request->input('selectedOffense');
        $perPage = 2; 
        $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

        // Retrieve and filter minor offenses
        $minorOffenses = SubmittedMinorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction))
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($selectedOffense, fn($q) => $q->where('minor_offense', $selectedOffense))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->get()            
            ->map(function ($offense) {
                $offense->offense_type = 'Minor';
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });

        // Retrieve and filter major offenses
        $majorOffenses = SubmittedMajorOffense::query()
            ->when($selectedYear, fn($q) => $q->where('student_schoolyear', $selectedYear))
            ->when($selectedQuarter, fn($q) => $q->where('student_quarter', $selectedQuarter))
            ->when($sanction !== null, fn($q) => $q->where('sanction', $sanction))
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($selectedOffense, fn($q) => $q->where('major_offense', $selectedOffense))
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_firstname', 'like', "%{$search}%")
                          ->orWhere('student_middlename', 'like', "%{$search}%")
                          ->orWhere('student_lastname', 'like', "%{$search}%")
                          ->orWhere('lrn', 'like', "%{$search}%");
                });
            })
            ->get()  
            ->map(function ($offense) {
                $offense->offense_type = 'Major';
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
                return $offense;
            });

            $offenderData = collect();
            if ($offenseFilter === 'Minor') {
                $offenderData = $minorOffenses;
            } elseif ($offenseFilter === 'Major') {
                $offenderData = $majorOffenses;
            } else {
        $offenderData = $minorOffenses->concat($majorOffenses);
    }

        // Paginate the combined collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        
        $offendersData = new LengthAwarePaginator(
            $offenderData->forPage($currentPage, $perPage),
            $offenderData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        
        // Retrieve distinct school years and quarters
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
            usort($schoolYear['quarters'], fn($a, $b) => array_search($a, $quarterOrder) - array_search($b, $quarterOrder));
        }

        $finalResult = array_values($groupedSchoolYears);

        $offenseList = [
            'all_offenses' => array_merge(
                MinorOffense::pluck('minor_offenses')->toArray(),
                MajorOffense::pluck('major_offenses')->toArray()
            ),
            'minor_offenses' => MinorOffense::pluck('minor_offenses')->toArray(),
            'major_offenses' => MajorOffense::pluck('major_offenses')->toArray(),
        ];

        return Inertia::render('Report/Index', [
            'offendersData' => $offendersData,
            'grades' => Grade::all(),
            'schoolYears' => $finalResult,
            'offenses' => $offenseList,
            'grade' => $grade,
            'section' => $section,
        ]);
    }
}
