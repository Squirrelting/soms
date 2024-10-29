<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Mail\SendEmail;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    static public function sendemail(Student $student)
    {

        $subject = "Notice to your Advisory Student";

        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->get();

        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()->get();

        // Send email to the student's email
        Mail::to($student->email)->send(new SendEmail($subject, $submittedminorOffenses, $submittedmajorOffenses, $student));
    }
}

