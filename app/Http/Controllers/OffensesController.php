<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MinorOffense;
use App\Models\MinorPenalty;
use Illuminate\Http\Request;
use App\Models\SubmittedMinorOffenses;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\OffenseDetailRequest;
use App\Http\Requests\StudentDetailRequest;

class OffensesController extends Controller
{
    public function minor(Student $student)
    {
        // Fetch all minor offenses
        $minorOffenses = MinorOffense::all();
    
        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('MinorOffense', 'MinorPenalty')->get();
        
        // Pass the student, minor offenses, and submitted minor offenses to the view
        return Inertia::render('Offenses/MinorOffenses', [
            'student' => $student,
            'minorOffenses' => $minorOffenses,
            'submittedminorOffenses' => $submittedminorOffenses,
        ]);
    }
    

    public function store(OffenseDetailRequest $request)
    {
        // Validate the form input
        $validated = $request->validated();
    
        // Fetch the student's offenses
        $existingOffenses = SubmittedMinorOffenses::where('lrn', $validated['lrn'])->count();
    
        // Determine the penalty based on the number of offenses
        $penaltyId = 1; // Default to the first penalty
    
        if ($existingOffenses == 1) {
            $penaltyId = 2; // Second offense, second penalty
        } elseif ($existingOffenses >= 2) {
            $penaltyId = 3; // Third or more offenses, third penalty
        }
    
        // Include the penalty in the data to be saved
        SubmittedMinorOffenses::create([
            'lrn' => $validated['lrn'],
            'student_name' => $validated['student_name'],
            'student_grade' => $validated['student_grade'],
            'minor_offense_id' => $validated['minor_offense_id'],
            'minor_penalty_id' => $penaltyId, // Save the penalty based on the number of offenses
        ]);
    
        return Redirect::back()->with('message', 'Offense and corresponding penalty added successfully');
    }
    


}