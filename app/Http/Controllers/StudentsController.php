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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $grade = $request->input('grade');
        $section = $request->input('section');
    
        // Fetch students with their grade, section, and submittedMinorOffenses relationships
        $students = Student::with(['grade', 'section'])
            ->withCount('submittedMinorOffenses', 'submittedMajorOffenses') // Add this to count the submitted minor offenses
            ->when($search, function ($query, $search) {
                $query->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('lrn', 'like', "%{$search}%");
            })
            ->when($grade, function ($query, $grade) {
                $query->where('grade_id', $grade);
            })
            ->when($section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->paginate(10)
            ->appends(['search' => $search, 'grade' => $grade, 'section' => $section]);
    
        $sections = Section::all(); // Fetch available sections for the dropdown
    
        return Inertia::render('Student/Index', [
            'students' => $students,
            'search' => $search,
            'grade' => $grade,
            'section' => $section,
            'sections' => $sections, // Pass sections for the dropdown
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
        // Update the student record with validated data from the request
        $student->update($request->validated());

        // Redirect back to the student list with a success message
        return redirect()->route('students.index')->with('message', 'Student updated successfully');
    }



    public function store(StudentDetailRequest $request)
    {
        // Use the validated data from the request to create a student
        Student::create($request->validated());

        // Redirect back to the students index with a success message
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

    public function destroy(Student $student)
    {
        if ($student->submittedMinorOffenses()->count() === 0 && $student->submittedMajorOffenses()->count() === 0) {
            $student->delete();
            return Redirect::back()->with('message', 'Student Deleted Successfully');
        } else {
            return Redirect::back()->withErrors(['message', 'Student can not be deleted ']);
        }
    }
}
