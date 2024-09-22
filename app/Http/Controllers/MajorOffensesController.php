<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MajorOffense;
use App\Models\MajorPenalty;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffenses;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MajorOffenseDetailRequest;
use App\Http\Requests\StudentDetailRequest;

class MajorOffensesController extends Controller
{
    public function major(Student $student)
    {
        // Fetch all major offenses
        $majorOffenses = MajorOffense::all();
    
        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()->with('MajorOffense', 'MajorPenalty')->get();
        
        // Pass the student, major offenses, and submitted major offenses to the view
        return Inertia::render('Offenses/MajorOffenses', [
            'student' => $student,
            'majorOffenses' => $majorOffenses,
            'submittedmajorOffenses' => $submittedmajorOffenses,
        ]);
    }
    

    public function store(MajorOffenseDetailRequest $request)
    {
        // Validate the form input
        $validated = $request->validated();
    
        // Fetch the student's offenses
        $existingOffenses = SubmittedMajorOffenses::where('lrn', $validated['lrn'])->count();
    
        // Determine the penalty based on the number of offenses
        $penaltyId = 1; // Default to the first penalty
    
        if ($existingOffenses == 1) {
            $penaltyId = 2; // Second offense, second penalty
        } elseif ($existingOffenses >= 2) {
            $penaltyId = 3; // Third or more offenses, third penalty
        }
    
        // Include the penalty in the data to be saved
        SubmittedMajorOffenses::create([
            'lrn' => $validated['lrn'],
            'student_name' => $validated['student_name'],
            'student_grade' => $validated['student_grade'],
            'major_offense_id' => $validated['major_offense_id'],
            'major_penalty_id' => $penaltyId, // Save the penalty based on the number of offenses
        ]);
    
        return Redirect::back()->with('message', 'Offense and corresponding penalty added successfully');
    }
    


}
