<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
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
        
        $students = Student::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%")
                      ->orWhere('grade', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->appends(['search' => $search]);

        return Inertia::render('Student/Index', [
            'students' => $students,
            'search' => $search,
        ]);
    }


    public function store(StudentDetailRequest $request)
    {
        $validated = $request->validated();
    
        Student::create($validated);
    
        return redirect()->route('students.index')->with('message', 'Student added successfully');
    }
    


    public function email(Student $student)
    {
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('minorOffense', 'minorPenalty')->get();
        $submittedmajorOffenses = $student->submittedMajorOffenses()->with('majorOffense', 'majorPenalty')->get();

        return Inertia::render('Student/ShowEmail',[
            'student' => $student,
            'submittedminorOffenses' => $submittedminorOffenses,
            'submittedmajorOffenses' => $submittedmajorOffenses,
        ]);
    }


    public function edit(Student $student)
    {
        return Inertia::render('Student/Edit',[
            'student' => $student,
        ]);
    }

    public function print(Student $student)
    {
        // Fetch all major signatories
        $signatory = Signatory::all();

        return Inertia::render('Student/Print',[
            'student' => $student,
            'signatory'=> $signatory
        ]);
    }

    public function update(StudentDetailRequest $request, Student $student)
    {
        $validated = $request->validated();
    
        $student->update($validated);
    
        return redirect()->route('students.index')->with('message', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        $student->delete(); 
        return Redirect::back()->with('message','Student Deleted Successfuly');
    }
}
