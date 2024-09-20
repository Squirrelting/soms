<?php

namespace App\Http\Controllers;

use App\Http\Requests\OffenseDetailRequest;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use App\Models\SubmittedMinorOffenses;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StudentDetailRequest;
use Carbon\Carbon;

class OffensesController extends Controller
{
    public function minor(Student $student)
    {
        // Fetch all minor offenses
        $minorOffenses = MinorOffense::all();
    
        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('minorOffense')->get();
    
        // Pass the student, minor offenses, and submitted minor offenses to the view
        return Inertia::render('Offenses/MinorOffenses', [
            'student' => $student,
            'minorOffenses' => $minorOffenses,
            'submittedminorOffenses' => $submittedminorOffenses,
        ]);
    }
    

    public function store(OffenseDetailRequest $request)
    {
        $validated = $request->validated();
        SubmittedMinorOffenses::create($validated);
        return Redirect::back()->with('message', 'Offense added successfully');
    }
    

}
