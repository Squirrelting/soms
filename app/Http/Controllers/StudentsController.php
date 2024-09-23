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
    public function dashboard()   
    {
       $students = Student::paginate(2);

       return Inertia::render('Dashboard', [
           'students'=> $students
       ]);
   }


   public function signatorypage(){
    $signatory = Signatory::get();

    return Inertia::render('SignatoryPage',[
        'signatory'=> $signatory
    ]);

}
   
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Student/Create');
    }


    public function store(StudentDetailRequest $request)
    {
        $validated = $request->validated();
    
        Student::create($validated);
    
        return redirect()->route('dashboard')->with('message', 'Student added successfully');
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
            'student' => $student
        ]);
    }

    public function print(Student $student, Signatory $signatory)
    {
        return Inertia::render('Student/Print',[
            'student' => $student,
            'signatory'=> $signatory
        ]);
    }

    public function update(StudentDetailRequest $request, Student $student)
    {
        $validated = $request->validated();
    
        $student->update($validated);
    
        return redirect()->route('dashboard')->with('message', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        $student->delete(); 
        return Redirect::back()->with('message','Student Deleted Successfuly');
    }
}
