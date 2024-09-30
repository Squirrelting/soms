<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MajorOffense;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MajorOffenseDetailRequest;

class MajorOffensesController extends Controller
{
    public function major(Student $student)
    {
        // Fetch all major offenses
        $majorOffenses = MajorOffense::all();
    
        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()
            ->with('majorOffense', 'majorPenalty')
            ->get()
            ->map(function($offense) {
                // Format the created_at date to "Month Day, Year"
                $offense->offense_date = Carbon::parse($offense->created_at)->format('F d, Y');
                
                // Format the sanction_date if it exists
                if ($offense->cleansed_date) {
                    $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
                } else {
                    $offense->cleansed_date = null; // Or you can set a default value if needed
                }

                return $offense;
            });
        
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
        $existingOffenses = SubmittedMajorOffense::where('lrn', $validated['lrn'])->count();
    
        // Determine the penalty based on the number of offenses
        $penaltyId = 1; // Default to the first penalty
    
        if ($existingOffenses == 1) {
            $penaltyId = 2; // Second offense, second penalty
        } elseif ($existingOffenses >= 2) {
            $penaltyId = 3; // Third or more offenses, third penalty
        }
    
        // Include the penalty in the data to be saved
        SubmittedMajorOffense::create([
            'lrn' => $validated['lrn'],
            'student_name' => $validated['student_name'],
            'student_grade' => $validated['student_grade'],
            'student_sex' => $validated['student_sex'],
            'major_offense_id' => $validated['major_offense_id'],
            'major_penalty_id' => $penaltyId, // Save the penalty based on the number of offenses
        ]);
    
        return Redirect::back()->with('message', 'Offense and corresponding penalty added successfully');
    }
    
    public function sanction(SubmittedMajorOffense $offense)
    {
        // Update the sanction field to 1 and set the cleansed_date to the current timestamp
        $offense->sanction = 1;
        $offense->cleansed_date = Carbon::now();
        $offense->save();
        $student = Student::where('lrn', $offense->lrn)->first();
    
        return Redirect::route('major.offenses', ['student' => $student->id]);
    }
    


}
