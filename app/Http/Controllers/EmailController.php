<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendemail(Student $student)
    {
        $message = "Confidentiality message"; 
        $subject = "Student Offense Committed";

        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('minorOffense', 'minorPenalty')->get();

        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()->with('majorOffense', 'majorPenalty')->get();

        // Send email to the student's email
        Mail::to($student->email)->send(new SendEmail($message, $subject, $submittedminorOffenses, $submittedmajorOffenses, $student));
    }
}

