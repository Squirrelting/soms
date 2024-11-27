<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\PrintCGM;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PrintCgmRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;

class PrintController extends Controller
{

    public function index(Request $request)
    {
        // Fetch inputs with default values
        $search = $request->input('search', '');
        $sortColumn = $request->input('sortColumn', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $perPage = $request->input('perPage', 10);
    
        // Validate sortColumn to avoid SQL injection
        $allowedSortColumns = ['firstname', 'middlename', 'lastname', 'created_at', 'generated_by', 'lrn'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at'; // Default to created_at if invalid column is provided
        }
    
        // Build query with search and sorting
        $studentsQuery = PrintCGM::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('firstname', 'like', "%{$search}%")
                          ->orWhere('middlename', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortColumn, $sortOrder);
    
        // Paginate the results
        $students = $studentsQuery->paginate($perPage)->withQueryString();
    
        // Fetch signatory data
        $signatory = Signatory::all();
    
        // Render the Inertia page with data
        return Inertia::render('PrintCGM/Index', [
            'signatory' => $signatory,
            'students' => $students,
            'perPage' => $perPage,
        ]);
    }
    
    

    public function store(PrintCgmRequest $request)
    {
        // Get the validated student data
        $studentData = $request->validated();
    
        // Add additional fields
        $studentData['generated_by'] = auth()->user()->name;
    
        // Fetch signatory details based on ID
        $signatory = Signatory::find($request->signatory);
    
        if (!$signatory) {
            return response()->json(['error' => 'Signatory not found.'], 404);
        }
    
        $studentData['signatory'] = $signatory->name;
        $studentData['position'] = $signatory->position;
    
        // Create the student record
        $student = PrintCGM::create($studentData);
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
    
    public function destroy(PrintCGM $print)
    {
        $print->delete(); 
        return Redirect::back()->with('message', 'Data Deleted Successfully');
    }

    // public function view(PrintCGM $print)
    // {
    //     // Fetch the specific student's data
    //     $student = $print;
    
    //     // Render the Inertia page with the student's data
    //     return Inertia::render('PrintCGM/View', [
    //         'student' => $student,
    //     ]);
    // }
    
}