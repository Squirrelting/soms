<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PrintController extends Controller
{

    public function printcgm($signatory, $id)
{
    $getstudent = Student::findOrFail($id);

    $getsignatory = Signatory::findOrFail($signatory);
    
    $imagePath = public_path('Images/SCNHS-Logo.png');
    $date = Carbon::now()->format('F j, Y'); 


    $pdf = Pdf::loadView('print-template.print-cgm', [
        'signatory' => $getsignatory, 
        'student' => $getstudent, 
        'imagePath' => $imagePath,
        'date' => $date,

    ]);
    
    return $pdf->stream('certificate.pdf');
    
}
}
