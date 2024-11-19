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

    public function index(){
        $signatory = Signatory::all();

        return Inertia::render('PrintCGM/Index',[
            'signatory' => $signatory,
        ]);
    }

    
    public function printcgm($signatory, $firstname, $middlename = null, $lastname) {
        $getsignatory = Signatory::findOrFail($signatory);
        $imagePath = public_path('Images/SCNHS-Logo.png');
        $date = Carbon::now()->format('F j, Y'); 
    
        // Pass the student's name to the view
        $pdf = Pdf::loadView('print-template.print-cgm', [
            'signatory' => $getsignatory, 
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'imagePath' => $imagePath,
            'date' => $date,
        ]);
    
        return $pdf->stream('certificate.pdf');
    }
    
}