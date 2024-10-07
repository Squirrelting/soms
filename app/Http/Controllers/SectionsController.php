<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\SectionDetailRequest;

class SectionsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $grade = $request->input('grade');
        $section = $request->input('section');
    
        // Fetch sections with grade relation and apply search filters
        $sectionsTable = Section::with('grade') // Assuming you have a `grade` relationship defined in your Section model
            ->when($search, function ($query, $search) {
                $query->where('section', 'like', "%{$search}%")
                      ->orWhereHas('grade', function ($q) use ($search) {
                          $q->where('grade', 'like', "%{$search}%");
                      });
            })
            ->when($grade, function ($query, $grade) {
                $query->where('grade_id', $grade);
            })
            ->when($section, function ($query, $section) {
                $query->where('id', $section);
            })
            ->paginate(10)
            ->appends(['search' => $search, 'grade' => $grade, 'section' => $section]);
    
            $sections = Section::all(); // Fetch available sections for the dropdown


            return Inertia::render('Section/Index', [
                'sectionsTable' => $sectionsTable,
                'search' => $search,
                'grade' => $grade,
                'section' => $section,
                'sections' => $sections, // Pass the paginated sections correctly
            ]);
            
    }
    
    public function create(Request $request)
    {
        // Fetch all grades
        $grades = Grade::all();
    
        // Pass only the grades, sections are fetched dynamically
        return Inertia::render('Section/Create', [
            'grades' => $grades,
        ]);
    }

    public function edit(Section $section)
    {
        // Fetch all grades
        $grades = Grade::all();
    
        // Fetch sections based on the student's current grade
        $sections = Section::where('grade_id', $section->grade_id)->get();
    
        return Inertia::render('Section/Edit', [
            'section' => $section,
            'grades' => $grades,
            'sections' => $sections, 
        ]);
    }

    public function update(SectionDetailRequest $request, Section $section)
    {
        $section->update($request->validated());
    
        return redirect()->route('section.index')->with('message', 'Student updated successfully');
    }
    

    
    public function store(SectionDetailRequest $request)
    {
        Section::create($request->validated());
    
        return redirect()->route('section.index')->with('message', 'Section added successfully');
    }

    public function destroy(Section $section)
    {
        $section->delete(); 
        return Redirect::back()->with('message', 'Section Deleted Successfully');
    }
}
