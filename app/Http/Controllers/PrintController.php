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
        $search = $request->input('search', '');
        $sortColumn = $request->input('sortColumn', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $perPage = (int) $request->input('perPage', 10);
    
        $studentsQuery = PrintCGM::query()
            ->select('print_cgm.*')
            ->joinSub(
                PrintCGM::selectRaw('MAX(id) as latest_id')
                    ->groupBy('lrn'),
                'latest_records',
                'print_cgm.id',
                '=',
                'latest_records.latest_id'
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('firstname', 'like', "%{$search}%")
                          ->orWhere('middlename', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortColumn, $sortOrder);
    
        $students = $studentsQuery->paginate($perPage)->withQueryString();
            // Fetch signatory data
            $signatory = Signatory::all();
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
        $imagePath1 = public_path('Images/SCNHS-Logo.png');
        $imagePath2 = public_path('Images/bagongpilipinas.png');
        $date = Carbon::now()->format('F j, Y'); 
    
        // Pass the student's name to the view
        $pdf = Pdf::loadView('print-template.print-cgm', [
            'signatory' => $getsignatory, 
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'imagePath1' => $imagePath1,
            'imagePath2' => $imagePath2,
            'date' => $date,
        ]);
    
        return $pdf->stream('certificate.pdf');
    }
    
    public function destroy($id)
    {
        // Find the record by ID
        $record = PrintCGM::findOrFail($id);
    
        // Delete the record
        $record->delete();
    
    }
    

    public function view($lrn)
    {
        // Fetch all records for the given LRN
        $records = PrintCGM::where('lrn', $lrn)->get();
    
        return Inertia::render('PrintCGM/View', [
            'records' => $records,
        ]);
    }
    
    
    
}