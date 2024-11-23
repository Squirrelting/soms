<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\Signatory;
use App\Models\MajorOffense;
use App\Models\MajorPenalty;
use App\Models\MinorOffense;
use App\Models\MinorPenalty;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StudentDetailRequest;
use Illuminate\Pagination\LengthAwarePaginator;


class StudentsController extends Controller
{
public function index(Request $request)
{
    // Fetch necessary data as you already have it here
    $search = $request->input('search');
    $grade = $request->input('grade');
    $section = $request->input('section');
    $sortColumn = $request->input('sortColumn', 'updated_at');
    $sortOrder = $request->input('sortOrder', 'desc');
    $selectedYear = $request->input('selectedYear');
    $selectedQuarter = $request->input('selectedQuarter');
    
    $perPage = $request->input('perPage', 10);
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $studentsQuery = Student::with(['grade', 'section'])
        ->withCount([
            'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
            'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
        ])
        ->when($selectedYear, fn($query) => $query->where('schoolyear', $selectedYear))
        ->when($selectedQuarter, fn($query) => $query->where('quarter', $selectedQuarter))
        ->when($grade, fn($query) => $query->where('grade_id', $grade))
        ->when($section, fn($query) => $query->where('section_id', $section))
        ->when($search, fn($query) => $query->where(function ($query) use ($search) {
            $query->where('firstname', 'like', "%{$search}%")
                  ->orWhere('middlename', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
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


    $quarterOrder = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
    $getSchoolYears = Student::select('schoolyear', 'quarter')
    ->distinct()
    ->get();

    $groupedSchoolYears = [];

    foreach ($getSchoolYears as $item) {
        $year = $item->schoolyear;
        $quarter = $item->quarter;
    
        if (!isset($groupedSchoolYears[$year])) {
            $groupedSchoolYears[$year] = [
                'schoolyear' => $year,
                'quarter' => []
            ];
        }
    
        if (!in_array($quarter, $groupedSchoolYears[$year]['quarter'])) {
            $groupedSchoolYears[$year]['quarter'][] = $quarter;
        }
    }
    
    // Sort the quarters in each school year
    foreach ($groupedSchoolYears as &$schoolYear) {
        usort($schoolYear['quarter'], function ($a, $b) use ($quarterOrder) {
            return array_search($a, $quarterOrder) - array_search($b, $quarterOrder);
        });
    }
    
    $finalResult = array_values($groupedSchoolYears);
    $sections = Section::all();
    $grades = Grade::all();
    
    return Inertia::render('Student/Index', [
        'students' => $students,
        'perPage' => $perPage,
        'grade' => $grade,
        'grades' => $grades,
        'section' => $section,
        'sections' => $sections,
        'schoolYears' => $finalResult,
        'selectedYear' => $selectedYear,  
        'selectedQuarter' => $selectedQuarter,  
    ]);
}

    public function getSections(Request $request)
    {
        $grade_id = $request->query('grade_id');

        // Fetch sections based on grade_id or return an empty array
        $sections = $grade_id ? Section::where('grade_id', $grade_id)->get() : [];

        return response()->json(['sections' => $sections]);
    }

    public function create(Request $request)
    {
        // Fetch all grades
        $studentsData = Student::all();
        $grades = Grade::all();
        $minorOffenses = MinorOffense::all();
        $majorOffenses = MajorOffense::all();

        // Pass only the grades, sections are fetched dynamically
        return Inertia::render('Student/Create', [
            'studentsData' => $studentsData,
            'grades' => $grades,
            'minorOffenses' => $minorOffenses,
            'majorOffenses' => $majorOffenses
        ]);
    }
    public function edit(Student $student)
    {
        // Fetch all grades
        $grades = Grade::all();

        // Fetch sections based on the student's current grade
        $sections = Section::where('grade_id', $student->grade_id)->get();

        return Inertia::render('Student/Edit', [
            'student' => $student,
            'grades' => $grades,
            'sections' => $sections, // Preload sections for student's grade
        ]);
    }

    public function update(StudentDetailRequest $request, Student $student)
    {
        $yearToday = $request->yeartoday; 
        $nextYear = $request->nextyear; 
    
        $schoolYear = $yearToday . '-' . $nextYear;
    
        $studentData = array_merge($request->validated(), ['schoolyear' => $schoolYear]);

        $student->update($studentData);

        return redirect()->route('students.index')->with('message', 'Student updated successfully');
    }


    public function store(StudentDetailRequest $request)
    {
        $yearToday = $request->yeartoday; 
        $nextYear = $request->nextyear; 

        $schoolYear = $yearToday . '-' . $nextYear;
    
        $studentData = array_merge($request->validated(), ['schoolyear' => $schoolYear]);
        $existingStudent = Student::where('lrn', $request->lrn)->first();
    
        if ($existingStudent) {
            $existingStudent->update($studentData);
            $student = $existingStudent;
        } else {
            $student = Student::create($studentData);
        }
    
        $section = Section::where('id', $student->section_id)->first();
        $recordedBy = auth()->user()->name;
    
        // Fetch current offense count
        $existingMinorOffensesCount = SubmittedMinorOffense::where('lrn', $student->lrn)->count();
    
        if (count($request->minor_offenses) > 0) {
            for ($i = 0; $i < count($request->minor_offenses); $i++) {

                $minorPenaltyId = 1;
                if ($existingMinorOffensesCount + $i == 1) {
                    $minorPenaltyId = 2;
                } elseif ($existingMinorOffensesCount + $i >= 2) {
                    $minorPenaltyId = 3;
                }
    
                $minorPenalty = MinorPenalty::find($minorPenaltyId);
    
                SubmittedMinorOffense::create([
                    'lrn' => $student->lrn,
                    'student_firstname' => $student->firstname,
                    'student_middlename' => $student->middlename,
                    'student_lastname' => $student->lastname,
                    'student_grade' => $student->grade_id,
                    'student_section' => $section->section,
                    'student_sex' => $student->sex,
                    'student_schoolyear' => $student->schoolyear,
                    'student_quarter' => $student->quarter,
                    'recorded_by' => $recordedBy,
                    'committed_date' => $request->minor_offenses[$i]['date_committed'],
                    'minor_offense' => $request->minor_offenses[$i]['minor_offenses'],
                    'minor_penalty' => $minorPenalty->minor_penalties,
                ]);
            }
        }
    
        // Fetch current offense count
        $existingMajorOffensesCount = SubmittedMajorOffense::where('lrn', $student->lrn)->count();

        if (count($request->major_offenses) > 0) {
            for ($i = 0; $i < count($request->major_offenses); $i++) {

                $majorPenaltyId = 1;
                if ($existingMajorOffensesCount + $i == 1) {
                    $majorPenaltyId = 2;
                } elseif ($existingMajorOffensesCount + $i >= 2) {
                    $majorPenaltyId = 3;
                }
    
                $majorPenalty = MajorPenalty::find($majorPenaltyId);
                SubmittedMajorOffense::create([
                    'lrn' => $student->lrn,
                    'student_firstname' => $student->firstname,
                    'student_middlename' => $student->middlename,
                    'student_lastname' => $student->lastname,
                    'student_grade' => $student->grade_id,
                    'student_section' => $section->section,
                    'student_sex' => $student->sex,
                    'student_schoolyear' => $student->schoolyear,
                    'student_quarter' => $student->quarter,
                    'recorded_by' => $recordedBy,
                    'committed_date' => $request->major_offenses[$i]['date_committed'],
                    'major_offense' => $request->major_offenses[$i]['major_offenses'],
                    'major_penalty' => $majorPenalty->major_penalties,
                ]);
            }
        }
    
        EmailController::sendemail($student);
    
        return redirect()->route('students.index')->with('message', $existingStudent ? 'Student updated successfully' : 'Student added successfully');
    }
    
    
    
}
