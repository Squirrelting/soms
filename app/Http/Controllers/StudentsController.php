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

class StudentsController extends Controller
{
// In StudentsController.php
public function index(Request $request)
{
    $search = $request->input('search');
    $grade = $request->input('grade');
    $section = $request->input('section');
    $sortColumn = $request->input('sortColumn', 'updated_at');  
    $sortOrder = $request->input('sortOrder', 'desc');
    
    $getSchoolYears = Student::select('schoolyear', 'quarter')
    ->distinct()
    ->get();

    $groupedSchoolYears = [];

    foreach ($getSchoolYears as $item) {
        $year = $item->schoolyear;
        $quarter = $item->quarter;

        // Check if the school year is already in the array
        if (!isset($groupedSchoolYears[$year])) {
            // Initialize with the school year and an empty quarters array
            $groupedSchoolYears[$year] = [
                'schoolyear' => $year,
                'quarter' => []
            ];
        }

        // Avoid duplicate quarters
        if (!in_array($quarter, $groupedSchoolYears[$year]['quarter'])) {
            $groupedSchoolYears[$year]['quarter'][] = $quarter;
        }
    }
$finalResult = array_values($groupedSchoolYears);


$selectedYear = $request->input('selectedYear');
$selectedQuarter = $request->input('selectedQuarter');


    // Define the allowed columns for sorting to avoid SQL injection
    $allowedSortColumns = ['lrn', 'lastname', 'grade_id', 'section_id', 'sex', 'email'];
    if (!in_array($sortColumn, $allowedSortColumns)) {
        $sortColumn = 'updated_at'; // Fallback to default if invalid column is passed
    }

    // Fetch students with their grade, section, and count offenses
    $students = Student::with(['grade', 'section'])
        ->withCount([
            'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
            'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
        ])
        ->when($selectedYear, function ($query, $selectedYear) {
            $query->where('schoolyear', $selectedYear); 
        })
        ->when($selectedQuarter, function ($query, $selectedQuarter) {
            $query->where('quarter', $selectedQuarter); 
        })
        ->when($grade, function ($query, $grade) {
            $query->where('grade_id', $grade);
        })
        ->when($section, function ($query, $section) {
            $query->where('section_id', $section);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('firstname', 'like', "%{$search}%")
                      ->orWhere('middlename', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortColumn, $sortOrder)
        ->paginate(2)
        ->appends([
            'search' => $search,
            'selectedYear' => $selectedYear,  
            'selectedQuarter' => $selectedQuarter,
            'grade' => $grade,
            'section' => $section,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ]);

    $sections = Section::all();
    $grades = Grade::all();
    
    return Inertia::render('Student/Index', [
        'students' => $students,
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
        $grades = Grade::all();
        $minorOffenses = MinorOffense::all();
        $majorOffenses = MajorOffense::all();

        // Pass only the grades, sections are fetched dynamically
        return Inertia::render('Student/Create', [
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
        $yearToday = now()->year;
        $nextYear = $yearToday + 1;
    
        $schoolYear = $yearToday . '-' . $nextYear;
    
        $studentData = array_merge($request->validated(), ['schoolyear' => $schoolYear]);

        $student->update($studentData);

        return redirect()->route('students.index')->with('message', 'Student updated successfully');
    }



    public function store(StudentDetailRequest $request)
    {
        // Get the current year and next year
        $yearToday = now()->year;
        $nextYear = $yearToday + 1;
    
        // Concatenate the years to form the school year
        $schoolYear = $yearToday . '-' . $nextYear;
    
        // Merge the school year with the validated data
        $studentData = array_merge($request->validated(), ['schoolyear' => $schoolYear]);
    
        // Create the student record and store in the database
        $newStudent = Student::create($studentData);

        $section = Section::where('id', $newStudent->section_id)->first();

        if(count($request->minor_offenses) > 0){
            for($i = 0; $i < count($request->minor_offenses); $i++){
                $minorPenaltyId = 1; // Default to the first penalty
        
                if ($i == 1) {
                    $minorPenaltyId = 2; // Second offense, second penalty
                } elseif ($i >= 2) {
                    $minorPenaltyId = 3; // Third or more offenses, third penalty
                }
        
                $minorPenalty = MinorPenalty::find($minorPenaltyId);
                    SubmittedMinorOffense::create([
                        'lrn' => $newStudent->lrn,
                        'student_firstname' => $newStudent->firstname,
                        'student_middlename' => $newStudent->middlename,
                        'student_lastname' => $newStudent->lastname,
                        'student_grade' => $newStudent->grade_id,
                        'student_section' => $section->section,
                        'student_sex' => $newStudent->sex,
                        'student_schoolyear' => $newStudent->schoolyear,
                        'student_quarter' => $newStudent->quarter,
                        'committed_date' => $request->minor_offenses[$i]['date_committed'],
                        'minor_offense' => $request->minor_offenses[$i]['minor_offenses'],
                        'minor_penalty' => $minorPenalty->minor_penalties, 
                    ]);
            }
        }

        if(count($request->major_offenses) > 0){
            for($i = 0; $i < count($request->major_offenses); $i++){
              
                    $majorPenaltyId = 1; // Default to the first penalty
                    
                    if ($i == 1) {
                        $majorPenaltyId = 2; // Second offense, second penalty
                    } elseif ($i >= 2) {
                        $majorPenaltyId = 3; // Third or more offenses, third penalty
                    }


                    $majorPenalty = MajorPenalty::find($majorPenaltyId);
                    SubmittedMajorOffense::create([
                        'lrn' => $newStudent->lrn,
                        'student_firstname' => $newStudent->firstname,
                        'student_middlename' => $newStudent->middlename,
                        'student_lastname' => $newStudent->lastname,
                        'student_grade' => $newStudent->grade_id,
                        'student_section' => $section->section,
                        'student_sex' => $newStudent->sex,
                        'student_schoolyear' => $newStudent->schoolyear,
                        'student_quarter' => $newStudent->quarter,
                        'committed_date' => $request->minor_offenses[$i]['date_committed'],
                        'major_offense' => $request->major_offenses[$i]['major_offenses'],
                        'major_penalty' => $majorPenalty->major_penalties, 
                    ]);
            }
        }


        EmailController::sendemail($newStudent);
    
        // Redirect back with success message
        return redirect()->route('students.index')->with('message', 'Student added successfully');
    }
    
}
