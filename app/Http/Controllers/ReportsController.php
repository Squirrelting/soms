<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Grade;
use App\Models\Section;
use App\Models\SubmittedMajorOffense;
use App\Models\SubmittedMinorOffense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Extract filter inputs
    $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
    $endDate = $request->input('endDate') ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) : null;
        $offenseFilter = $request->input('offenseFilter');
        $sex = $request->input('sex');
        $grade = $request->input('grade');
        $section = $request->input('section');
    
        // Retrieve and map minor offenses
        $submittedminorOffenses = SubmittedMinorOffense::query()
            ->when($offenseFilter === 'Minor', fn($q) => $q->whereNotNull('minor_offense'))
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get() // Retrieve the results first
            ->map(function ($offense) { // Then map to add formatted date
                $offense->offense_date = Carbon::parse($offense->created_at)->format('F d, Y');
                return $offense;
            });
    
        // Retrieve and map major offenses
        $submittedmajorOffenses = SubmittedMajorOffense::query()
            ->when($offenseFilter === 'Major', fn($q) => $q->whereNotNull('major_offense'))
            ->when($sex, fn($q) => $q->where('student_sex', $sex))
            ->when($grade, fn($q) => $q->where('student_grade', $grade))
            ->when($section, fn($q) => $q->where('student_section', $section))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get() // Retrieve the results first
            ->map(function ($offense) { // Then map to add formatted date
                $offense->offense_date = Carbon::parse($offense->created_at)->format('F d, Y');
                return $offense;
            });
    
        // Determine which offenses to return based on filter
        $offensesData = [];
        if ($offenseFilter === 'Minor') {
            $offensesData = $submittedminorOffenses->toArray();
        } elseif ($offenseFilter === 'Major') {
            $offensesData = $submittedmajorOffenses->toArray();
        } else {
            $offensesData = array_merge($submittedminorOffenses->toArray(), $submittedmajorOffenses->toArray());
        }
    
        return Inertia::render('Report/Index', [
            'offensesData' => $offensesData,
            'grades' => Grade::all(),
            'sections' => Section::all(),
        ]);
    }
    
}
