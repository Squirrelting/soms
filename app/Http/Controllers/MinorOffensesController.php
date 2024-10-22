<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use App\Models\SubmittedMinorOffense;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MinorOffenseDetailRequest;
use App\Models\MinorPenalty;

class MinorOffensesController extends Controller
{
public function minor(Student $student)
{
    // Fetch the student's grade and section by resolving their foreign key relationships
    $studentWithGradeAndSection = $student->load('grade', 'section');

    // Fetch all minor offenses
    $minorOffenses = MinorOffense::all();

    // Fetch submitted minor offenses related to the student
    $submittedminorOffenses = $student->submittedMinorOffenses()
        ->get()
        ->map(function($offense) {
            // Format the created_at date to "Month Day, Year"
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');

            // Format the sanction_date if it exists
            if ($offense->cleansed_date) {
                $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
            } else {
                $offense->cleansed_date = null; // Or set a default value if needed
            }
            
            return $offense;
        });

    // Pass the student, minor offenses, and submitted minor offenses to the view
    return Inertia::render('Offenses/MinorOffenses', [
        'student' => $studentWithGradeAndSection, // Pass the student with the resolved grade and section
        'minorOffenses' => $minorOffenses,
        'submittedminorOffenses' => $submittedminorOffenses,
    ]);
}
    
    public function store(MinorOffenseDetailRequest $request)
    {
        // Validate the form input
        $validated = $request->validated();
        
        // Fetch the student using the provided lrn
        $student = Student::where('lrn', $validated['lrn'])->first();
    
        // Ensure the student exists
        if (!$student) {
            return Redirect::back()->withErrors(['lrn' => 'Student not found.']);
        }
    
        // Fetch the student's offenses
        $existingOffenses = SubmittedMinorOffense::where('lrn', $validated['lrn'])->count();
        
        // Determine the penalty based on the number of offenses
        $penaltyId = 1; // Default to the first penalty
        
        if ($existingOffenses == 1) {
            $penaltyId = 2; // Second offense, second penalty
        } elseif ($existingOffenses >= 2) {
            $penaltyId = 3; // Third or more offenses, third penalty
        }

        $penalty = MinorPenalty::find($penaltyId);
    
        // Create a new SubmittedMinorOffense
        SubmittedMinorOffense::create([
            'lrn' => $validated['lrn'],
            'student_firstname' => $validated['student_firstname'],
            'student_middlename' => $validated['student_middlename'],
            'student_lastname' => $validated['student_lastname'],
            'student_grade' => $validated['student_grade'],
            'student_section' => $validated['student_section'],
            'student_sex' => $validated['student_sex'],
            'student_schoolyear' => $validated['student_schoolyear'],
            'student_quarter' => $validated['student_quarter'],
            'committed_date' => $validated['committed_date'],
            'minor_offense' => $validated['minor_offense'],
            'minor_penalty' => $penalty->minor_penalties, 
        ]);

        EmailController::sendemail($student);
        return Redirect::back()->with('message', 'Offense and corresponding penalty added successfully');
    }
    
    
    public function sanction(SubmittedMinorOffense $offense)
    {
        $offense->sanction = 1;
        $offense->cleansed_date = Carbon::now();
        $offense->save();
        $student = Student::where('lrn', $offense->lrn)->first();
        return Redirect::route('minor.offenses', ['student' => $student->id]);
    }

}
