<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class ViewController extends Controller
{
    public function view(Student $student, Request $request)
    {
        $studentWithGradeAndSection = $student->load('grade', 'section');

        $submittedminorOffenses = $student->submittedMinorOffenses()
            ->get()
            ->map(function($offense) {
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');

                if ($offense->cleansed_date) {
                    $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
                } else {
                    $offense->cleansed_date = null;
                }
                
                return $offense;
            });   

        $submittedmajorOffenses = $student->submittedMajorOffenses()
            ->get()
            ->map(function($offense) {
                $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
                $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');

                if ($offense->cleansed_date) {
                    $offense->cleansed_date = Carbon::parse($offense->cleansed_date)->format('F d, Y');
                } else {
                    $offense->cleansed_date = null;
                }

                return $offense;
            });
            
            $user = $request->user();

        return Inertia::render('Student/View', [
            'user' => $user,
            'student' => $studentWithGradeAndSection,
            'submittedminorOffenses' => $submittedminorOffenses,
            'submittedmajorOffenses' => $submittedmajorOffenses,
        ]);
    }

public function printRecord(Student $student)
{
    $studentWithGradeAndSection = $student->load('grade', 'section');

    $submittedminorOffenses = $student->submittedMinorOffenses()
        ->get()
        ->map(function($offense) {
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date 
                ? Carbon::parse($offense->cleansed_date)->format('F d, Y') 
                : null;
            return $offense;
        });

    $submittedmajorOffenses = $student->submittedMajorOffenses()
        ->get()
        ->map(function($offense) {
            $offense->recorded_date = Carbon::parse($offense->created_at)->format('F d, Y');
            $offense->committed_date = Carbon::parse($offense->committed_date)->format('F d, Y');
            $offense->cleansed_date = $offense->cleansed_date 
                ? Carbon::parse($offense->cleansed_date)->format('F d, Y') 
                : null;
            return $offense;
        });

    // Prepare date formats
    $imagePath1 = public_path('Images/SCNHS-Logo.png');
    $imagePath2 = public_path('Images/bagongpilipinas.png');    
    $imagePath3 = public_path('Images/deped.png');
    $imagePath4 = public_path('Images/footer.png');
    $imagePath5 = public_path('Images/header.png');
    $date = Carbon::now()->format('F j, Y');

    // Fetch the currently logged-in user and their role
    $user = auth()->user();
    $userName = $user->name;
    $userRole = $user->getRoleNames()->first();

    $pdf = Pdf::loadView('print-template.print-record', [
        'student' => $studentWithGradeAndSection,
        'submittedminorOffenses' => $submittedminorOffenses,
        'submittedmajorOffenses' => $submittedmajorOffenses,
        'imagePath1' => $imagePath1,
        'imagePath2' => $imagePath2,
        'imagePath3' => $imagePath3,
        'imagePath4' => $imagePath4,
        'imagePath5' => $imagePath5,        
        'date' => $date,
        'userName' => $userName,
        'userRole' => $userRole,
    ])->setPaper('legal', 'landscape');

    return $pdf->stream('student-record.pdf');
}

}
