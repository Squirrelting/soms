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
        $perPage = (string) $request->input('perPage', 10); // Cast to string
        $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

        // Retrieve and filter minor offenses
        $minorOffenses = SubmittedMinorOffense::query()
        ->join('students', 'submitted_minor_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_minor_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_minor_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_minor_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_minor_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_minor_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_minor_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_minor_offenses.minor_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Minor';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });
    

        // Retrieve and filter major offenses
        $majorOffenses = SubmittedMajorOffense::query()
        ->join('students', 'submitted_major_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_major_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_major_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_major_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_major_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_major_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_major_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_major_offenses.major_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Major';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });
    

            $offenderData = collect();
            $offenderData = match ($offenseFilter) {
                'Minor' => $minorOffenses,
                'Major' => $majorOffenses,
                default => $minorOffenses->concat($majorOffenses),
            };

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

        $sections = Section::all();
        $grades = Grade::all();
        $user = $request->user();


        return Inertia::render('Report/Index', [
            'user' => $user,
            'offendersData' => $offendersData,
            'perPage' => $perPage,
            'grades' => $grades,
            'sections' => $sections,
            'schoolYears' => $finalResult,
            'offenses' => $offenseList,
            'grade' => $grade,
            'section' => $section,
        ]);
    }

    public function printoffenders(Request $request)
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

        // Retrieve and filter minor offenses
        $minorOffenses = SubmittedMinorOffense::query()
        ->join('students', 'submitted_minor_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_minor_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_minor_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_minor_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_minor_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_minor_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_minor_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_minor_offenses.minor_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Minor';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });

        // Retrieve and filter major offenses
        $majorOffenses = SubmittedMajorOffense::query()
        ->join('students', 'submitted_major_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_major_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_major_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_major_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_major_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_major_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_major_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_major_offenses.major_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Major';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });

            $offendersData = match ($offenseFilter) {
                'Minor' => $minorOffenses,
                'Major' => $majorOffenses,
                default => $minorOffenses->concat($majorOffenses),
            };

            // Fetch the currently logged-in user and their role
            $user = auth()->user();
            $userName = $user->name;
            $userRole = $user->getRoleNames()->first();
            
            $imagePath1 = public_path('Images/SCNHS-Logo.png');
            $imagePath2 = public_path('Images/bagongpilipinas.png');            $date = Carbon::now()->format('F j, Y');

            $pdf = Pdf::loadView('print-template.print-offenders', [
                'offendersData'    => $offendersData,
                'imagePath1' => $imagePath1,
                'imagePath2' => $imagePath2,
                'date'             => $date,
                'sanction'         => $sanction,
                'offenseFilter'    => $offenseFilter,
                'sex'              => $sex,
                'grade'            => $grade,
                'section'          => $section,
                'selectedYear'     => $selectedYear,
                'selectedQuarter'  => $selectedQuarter,
                'selectedOffense'  => $selectedOffense,
                'userName'         => $userName,
                'userRole'         => $userRole,
            ])->setPaper('legal', 'landscape'); 

            return $pdf->stream('List-of-Offenses.pdf');
            }

    public function exportExcel(Request $request)
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

        // Retrieve and filter minor offenses
        $minorOffenses = SubmittedMinorOffense::query()
        ->join('students', 'submitted_minor_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_minor_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_minor_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_minor_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_minor_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_minor_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_minor_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_minor_offenses.minor_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Minor';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });

        // Retrieve and filter major offenses
        $majorOffenses = SubmittedMajorOffense::query()
        ->join('students', 'submitted_major_offenses.lrn', '=', 'students.lrn')
        ->select(
            'submitted_major_offenses.*',
            'students.firstname as student_firstname',
            'students.middlename as student_middlename',
            'students.lastname as student_lastname'
        )
        ->when($selectedYear, fn($q) => $q->where('submitted_major_offenses.student_schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($q) => $q->where('submitted_major_offenses.student_quarter', $selectedQuarter))
        ->when($sanction !== null, fn($q) => $q->where('submitted_major_offenses.sanction', $sanction))
        ->when($sex, fn($q) => $q->where('students.sex', $sex))
        ->when($grade, fn($q) => $q->where('submitted_major_offenses.student_grade', $grade))
        ->when($section, fn($q) => $q->where('submitted_major_offenses.student_section', $section))
        ->when($selectedOffense, fn($q) => $q->where('submitted_major_offenses.major_offense', $selectedOffense))
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.middlename', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('students.lrn', 'like', "%{$search}%");
            });
        })
        ->get()
        ->map(function ($offense) {
            $offense->offense_type = 'Major';
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;
            return $offense;
        });

            $offendersData = match ($offenseFilter) {
                'Minor' => $minorOffenses,
                'Major' => $majorOffenses,
                default => $minorOffenses->concat($majorOffenses),
            };



    return Excel::download(new OffendersExport($offendersData ), 'offenders.xlsx');

    }

}
