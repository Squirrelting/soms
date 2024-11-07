<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MajorOffense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\AddMajorDetailRequest;

class AddMajorOffensesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortColumn = $request->input('sortColumn', 'updated_at');  
        $sortOrder = $request->input('sortOrder', 'desc'); 
    
        $allowedSortColumns = ['major_offenses', 'updated_at'];  // Add allowed sort columns
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'updated_at'; 
        }
    
        // Apply the query, sorting, and pagination
        $majorOffense = MajorOffense::when($search, function ($query, $search) {
                return $query->where('major_offenses', 'like', "%{$search}%");
            })
            ->orderBy($sortColumn, $sortOrder) 
            ->paginate(10)
            ->appends(['search' => $search, 'sortColumn' => $sortColumn, 'sortOrder' => $sortOrder]);
    
        return Inertia::render('Add-Offense/Major/Index', [
            'majorOffense' => $majorOffense
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Add-Offense/Major/Create');
    }

    public function store(AddMajorDetailRequest $request)
    {
        MajorOffense::create($request->validated());
    
        return redirect()->route('majoroffense.index')->with('message', 'Major Offense added successfully');
    }

    public function edit(MajorOffense $major)
    {
        return Inertia::render('Add-Offense/Major/Edit', [
            'major_offenses' => $major,
        ]);
    }

    public function update(AddMajorDetailRequest $request, MajorOffense $major)
    {
        $major->update($request->validated());
        
        return redirect()->route('majoroffense.index')->with('message', 'Student updated successfully');
    }

    public function destroy(MajorOffense $major)
    {
        $major->delete(); 
        return Redirect::back()->with('message', 'Major offense Deleted Successfully');
    }
}

