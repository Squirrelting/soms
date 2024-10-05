<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Inertia\Inertia;

class StudentsImportController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all grades
        $grades = Grade::all();
        return Inertia::render('Student/Import', [
            'grades' => $grades,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|mimes:csv,txt',  // Ensuring the file is a CSV
            'grade_id' => 'required|exists:grade,id',  // Ensuring the grade exists
            'section_id' => 'required|exists:section,id',  // Ensuring the section exists
        ]);

        // Load the CSV file and process each row
        Excel::import(new StudentsImport($request->grade_id, $request->section_id), $request->file('file'));

        // Return a response (you can customize the view based on your needs)
        return back()->with('success', 'Students imported successfully!');
    }
}
