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
    public function view(Student $student)
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
            
        return Inertia::render('Student/View', [
            'student' => $studentWithGradeAndSection,
            'submittedminorOffenses' => $submittedminorOffenses,
            'submittedmajorOffenses' => $submittedmajorOffenses,
        ]);
    }

  public function printRecord(Student $student)
{
    // Load student data with related models
    $studentWithGradeAndSection = $student->load('grade', 'section');

    // Fetch and format minor offenses
    $submittedminorOffenses = $student->submittedMinorOffenses()
        ->get()
        ->map([$this, 'formatOffenseDates']);

    // Fetch and format major offenses
    $submittedmajorOffenses = $student->submittedMajorOffenses()
        ->get()
        ->map([$this, 'formatOffenseDates']);

    // Prepare date and image path
    $imagePath = public_path('Images/SCNHS-Logo.png');
    $date = Carbon::now()->format('F j, Y');

    // Generate PDF and stream it
    ob_start();
    $pdf = Pdf::loadView('print-template.print-record', [
        'student' => $studentWithGradeAndSection,
        'submittedminorOffenses' => $submittedminorOffenses,
        'submittedmajorOffenses' => $submittedmajorOffenses,
        'imagePath' => $imagePath,
        'date' => $date,
    ]);
    ob_end_clean();

    return response($pdf->output())
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="student-record.pdf"');
}

    
}
