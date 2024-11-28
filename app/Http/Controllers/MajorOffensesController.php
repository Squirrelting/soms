<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\MajorOffense;
use App\Models\MajorPenalty;
use Illuminate\Http\Request;
use App\Models\SubmittedMajorOffense;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MajorOffenseDetailRequest;

class MajorOffensesController extends Controller
{
public function major(Student $student, Request $request)
{
    // Fetch the student's grade and section by resolving their foreign key relationships
    $studentWithGradeAndSection = $student->load('grade', 'section');

    // Fetch all major offenses
    $majorOffenses = MajorOffense::all();

    // Fetch submitted major offenses related to the student
    $submittedmajorOffenses = $student->submittedMajorOffenses()
        ->get()
        ->map(function($offense) {
            // Format the created_at date to "Month Day, Year"
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date ? Carbon::parse($offense->cleansed_date)->format('F d, Y') : null;

            
            return $offense;
        });
        $user = $request->user();

    // Pass the student, major offenses, and submitted major offenses to the view
    return Inertia::render('Offenses/MajorOffenses', [
        'user' => $user,
        'student' => $studentWithGradeAndSection, // Pass the student with the resolved grade and section
        'majorOffenses' => $majorOffenses,
        'submittedmajorOffenses' => $submittedmajorOffenses,
    ]);
}
    
    public function store(MajorOffenseDetailRequest $request)
    {
        // Validate the form input
        $validated = $request->validated();
        $recordedBy = auth()->user()->name;

        
        // Fetch the student using the provided lrn
        $student = Student::where('lrn', $validated['lrn'])->first();
    
        // Ensure the student exists
        if (!$student) {
            return Redirect::back()->withErrors(['lrn' => 'Student not found.']);
        }
    
        // Fetch the student's offenses
        $existingOffenses = SubmittedMajorOffense::where('lrn', $validated['lrn'])->count();
        
        // Determine the penalty based on the number of offenses
        $penaltyId = 1; // Default to the first penalty
        
        if ($existingOffenses == 1) {
            $penaltyId = 2; // Second offense, second penalty
        } elseif ($existingOffenses >= 2) {
            $penaltyId = 3; // Third or more offenses, third penalty
        }
        $penalty = MajorPenalty::find($penaltyId);

        // Create a new SubmittedMajorOffense
        SubmittedMajorOffense::create([
            'lrn' => $validated['lrn'],
            'student_firstname' => $validated['student_firstname'],
            'student_middlename' => $validated['student_middlename'],
            'student_lastname' => $validated['student_lastname'], // Correct field name
            'student_grade' => $validated['student_grade'],
            'student_section' => $validated['student_section'],
            'student_sex' => $validated['student_sex'],
            'student_schoolyear' => $validated['student_schoolyear'],
            'student_quarter' => $validated['student_quarter'],
            'committed_date' => $validated['committed_date'],
            'major_offense' => $validated['major_offense'],
            'major_penalty' => $penalty->major_penalties,
            'recorded_by' => $recordedBy,
 
        ]);

        $student->updated_at = Carbon::now();
        $student->save();
        
        EmailController::sendemail($student);

        return Redirect::back()->with('message', 'Offense and corresponding penalty added successfully');
    }
    
    
    public function sanction(SubmittedMajorOffense $offense)
    {
        $offense->update([
            'sanction' => 1,
            'cleansed_by' => auth()->user()->name,
            'cleansed_date' => Carbon::now(),
        ]);

        $student = Student::where('lrn', $offense->lrn)->first();
    
        $student->updated_at = Carbon::now();
        $student->save();
        
        return Redirect::route('major.offenses', ['student' => $student->id]);
    }

}
