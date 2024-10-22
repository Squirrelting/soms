<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\AddMinorDetailRequest;

class AddMinorOffensesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortColumn = $request->input('sortColumn', 'updated_at');  
        $sortOrder = $request->input('sortOrder', 'desc'); 
    
        $allowedSortColumns = ['minor_offenses', 'updated_at'];  // Add allowed sort columns
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'updated_at'; 
        }
    
        // Apply the query, sorting, and pagination
        $minorOffense = MinorOffense::when($search, function ($query, $search) {
                return $query->where('minor_offenses', 'like', "%{$search}%");
            })
            ->orderBy($sortColumn, $sortOrder) 
            ->paginate(10)
            ->appends(['search' => $search, 'sortColumn' => $sortColumn, 'sortOrder' => $sortOrder]);
    
        return Inertia::render('Add-Offense/Minor/Index', [
            'minorOffense' => $minorOffense
        ]);
    }
    
    public function create(Request $request)
    {
        return Inertia::render('Add-Offense/Minor/Create');
    }

    public function store(AddMinorDetailRequest $request)
    {
        MinorOffense::create($request->validated());
    
        return redirect()->route('minoroffense.index')->with('message', 'Minor Offense added successfully');
    }

    public function edit(MinorOffense $minor)
    {
        return Inertia::render('Add-Offense/Minor/Edit', [
            'minor_offenses' => $minor,
        ]);
    }

    public function update(AddMinorDetailRequest $request, MinorOffense $minor)
    {
        $minor->update($request->validated());
        
        return redirect()->route('minoroffense.index')->with('message', 'Student updated successfully');
    }

    public function destroy(MinorOffense $minor)
    {
        $minor->delete(); 
        return Redirect::back()->with('message', 'Minor offense Deleted Successfully');
    }
}

