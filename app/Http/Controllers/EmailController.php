<?php

namespace App\Http\Controllers;

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
}

