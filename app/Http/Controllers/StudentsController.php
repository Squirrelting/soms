<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StudentDetailRequest;

class StudentsController extends Controller
{
// In StudentsController.php
public function index(Request $request)
{
    // Extract start and end dates
    $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
    $endDate = $request->input('endDate') ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) : null;

    $search = $request->input('search');
    $grade = $request->input('grade');
    $section = $request->input('section');
    $sortColumn = $request->input('sortColumn', 'id');  // Default to 'id' if no sort column is provided
    $sortOrder = $request->input('sortOrder', 'desc');   // Default to 'asc' order if not provided

    // Define the allowed columns for sorting to avoid SQL injection
    $allowedSortColumns = ['lrn', 'lastname', 'grade_id', 'section_id', 'sex', 'email'];
    if (!in_array($sortColumn, $allowedSortColumns)) {
        $sortColumn = 'id'; // Fallback to default if invalid column is passed
    }

    // Fetch students with their grade, section, and count offenses
    $students = Student::with(['grade', 'section'])
        ->withCount([
            'submittedMinorOffensesWithNoSanction as submitted_minor_offenses_count',
            'submittedMajorOffensesWithNoSanction as submitted_major_offenses_count'
        ])
        ->when($grade, function ($query, $grade) {
            $query->where('grade_id', $grade);
        })
        ->when($section, function ($query, $section) {
            $query->where('section_id', $section);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('firstname', 'like', "%{$search}%")
                      ->orWhere('middlename', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortColumn, $sortOrder) // Apply sorting here
        ->paginate(10)
        ->appends([
            'search' => $search,
            'grade' => $grade,
            'section' => $section,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ]);

    $sections = Section::all();
    $grades = Grade::all();
    
    return Inertia::render('Student/Index', [
        'students' => $students,
        'search' => $search,
        'grade' => $grade,
        'grades' => $grades,
        'section' => $section,
        'sections' => $sections,
        'sortColumn' => $sortColumn,
        'sortOrder' => $sortOrder,
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

        // Pass only the grades, sections are fetched dynamically
        return Inertia::render('Student/Create', [
            'grades' => $grades,
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
        Student::create($studentData);
    
        // Redirect back with success message
        return redirect()->route('students.index')->with('message', 'Student added successfully');
    }

    public function print(Student $student)
    {
        $signatory = Signatory::all();

        return Inertia::render('Student/Print', [
            'student' => $student,
            'signatory' => $signatory,
        ]);
    }
    
}
