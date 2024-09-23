<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Student;
use App\Models\MajorOffense;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendemail(Student $student){
        
        $toEmail = $student->email;

        $message = "Confidentiality messae";
        $subject = "Student Offense Commited";
        // Fetch all minor offenses
        $minorOffenses = MinorOffense::all();
        // Fetch submitted minor offenses related to the student
        $submittedminorOffenses = $student->submittedMinorOffenses()->with('minorOffense', 'minorPenalty')->get();

        // Fetch all major offenses
        $majorOffenses = MajorOffense::all();
        // Fetch submitted major offenses related to the student
        $submittedmajorOffenses = $student->submittedMajorOffenses()->with('majorOffense', 'majorPenalty')->get();
        

        Mail::to($toEmail)->send(new SendEMail($message, $subject, $submittedminorOffenses,  $submittedmajorOffenses));

    }
}
