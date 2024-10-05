<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Mail\SendEmail;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendemail(Student $student)
    {

        $subject = "Notice to your Child";

        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('minorOffense', 'minorPenalty')->get();

        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()->with('majorOffense', 'majorPenalty')->get();

        // Send email to the student's email
        Mail::to($student->email)->send(new SendEmail($subject, $submittedminorOffenses, $submittedmajorOffenses, $student));
    }

    public function email(Student $student)
    {
            // Fetch the student's grade and section by resolving their foreign key relationships
        $studentWithGradeAndSection = $student->load('grade', 'section');

        $submittedminorOffenses = $student->submittedMinorOffenses()
            ->with('minorOffense', 'minorPenalty')
            ->get()
            ->map(function($offense) {
                $offense->offense_date = Carbon::parse($offense->created_at)->format('F d, Y');

                if ($offense->cleansed_date) {
                    $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
                } else {
                    $offense->cleansed_date = null;
                }
                
                return $offense;
            });   

        $submittedmajorOffenses = $student->submittedMajorOffenses()
            ->with('majorOffense', 'majorPenalty')
            ->get()
            ->map(function($offense) {
                $offense->offense_date = Carbon::parse($offense->created_at)->format('F d, Y');

                if ($offense->cleansed_date) {
                    $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
                } else {
                    $offense->cleansed_date = null;
                }

                return $offense;
            });
            
        return Inertia::render('Student/ShowEmail', [
            'student' => $studentWithGradeAndSection,
            'submittedminorOffenses' => $submittedminorOffenses,
            'submittedmajorOffenses' => $submittedmajorOffenses,
        ]);
    }
}

