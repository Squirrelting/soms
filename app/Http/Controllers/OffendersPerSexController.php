<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OffendersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SubmittedMinorOffense;
use App\Models\SubmittedMajorOffense;

class OffendersPerSexController extends Controller
{
    public function index(Request $request)
    {
    // Set default startDate to 1 month ago and endDate to today if not provided
    $startDate = $request->input('startDate') 
        ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) 
        : date('Y-m-d 00:00:00', strtotime('-1 month')); // 1 month ago

    $endDate = $request->input('endDate') 
        ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) 
        : date('Y-m-d 23:59:59'); // Today
        $offenseFilter = $request->offenseFilter ?? "";

        // Fetch and group minor offenses
        $submittedminorOffenses = SubmittedMinorOffense::select('minor_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('minor_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->minor_offense;
                $offense->type = 'Minor';
                return $offense;
            });

        // Fetch and group major offenses
        $submittedmajorOffenses = SubmittedMajorOffense::select('major_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('major_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->major_offense;
                $offense->type = 'Major';
                return $offense;
            });

        $submittedminorOffenses = collect($submittedminorOffenses); 
        $submittedmajorOffenses = collect($submittedmajorOffenses); 

        $offensesData = collect();
        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            $offensesData = $submittedminorOffenses->merge($submittedmajorOffenses);
        }

        return Inertia::render('Dashboard/OffendersPerSex', [
            'offensesData' => $offensesData,
        ]);
    }
    public function exportExcel(Request $request)
    {
        // Extract start and end dates
        $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
        $endDate   = $request->input('endDate')   ? date('Y-m-d 23:59:59', strtotime($request->input('endDate')))   : null;
        $offenseFilter = $request->offenseFilter ?? "";

        // Fetch and group minor offenses
        $submittedminorOffenses = SubmittedMinorOffense::select('minor_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate){
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('minor_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->minor_offense;
                $offense->type = 'Minor';
                return $offense;
            });

        // Fetch and group major offenses
        $submittedmajorOffenses = SubmittedMajorOffense::select('major_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate){
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('major_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->major_offense;
                $offense->type = 'Major';
                return $offense;
            });

        $submittedminorOffenses = collect($submittedminorOffenses); 
        $submittedmajorOffenses = collect($submittedmajorOffenses); 
        $offensesData = collect();

        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            $offensesData = $submittedmajorOffenses->merge($submittedminorOffenses);
        }

        // Prepare date formats
        $date = Carbon::now()->format('F j, Y');
        $formattedSelectedDate = [
            'startDate' => $startDate ? Carbon::parse($startDate)->format('F j, Y') : 'Not specified',
            'endDate'   => $endDate   ? Carbon::parse($endDate)->format('F j, Y')   : 'Not specified',
        ];

        // Use a custom Excel export class
        return Excel::download(new OffendersExport($offensesData, $date, $formattedSelectedDate, $offenseFilter), 'offenders.xlsx');
    }

    public function printoffenders(Request $request)
    {
        // (Same as index method)
        // Extract start and end dates
        $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
        $endDate   = $request->input('endDate')   ? date('Y-m-d 23:59:59', strtotime($request->input('endDate')))   : null;
        $offenseFilter = $request->offenseFilter ?? "";

        // Fetch and group minor offenses
        $submittedminorOffenses = SubmittedMinorOffense::select('minor_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate){
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('minor_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->minor_offense;
                $offense->type = 'Minor';
                return $offense;
            });

        // Fetch and group major offenses
        $submittedmajorOffenses = SubmittedMajorOffense::select('major_offense')
            ->selectRaw("SUM(CASE WHEN student_sex = 'Male' THEN 1 ELSE 0 END) as male_count")
            ->selectRaw("SUM(CASE WHEN student_sex = 'Female' THEN 1 ELSE 0 END) as female_count")
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate){
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('major_offense')
            ->havingRaw('(male_count > 0 OR female_count > 0)')
            ->get()
            ->map(function ($offense) {
                $offense->offense_name = $offense->major_offense;
                $offense->type = 'Major';
                return $offense;
            });

        $submittedminorOffenses = collect($submittedminorOffenses); 
        $submittedmajorOffenses = collect($submittedmajorOffenses); 
        $offensesData = collect();

        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            $offensesData = $submittedminorOffenses->merge($submittedmajorOffenses);
        }

        // Prepare date formats
        $imagePath = public_path('Images/SCNHS-Logo.png');
        $date = Carbon::now()->format('F j, Y');
        $formattedSelectedDate = [
            'startDate' => $startDate ? Carbon::parse($startDate)->format('F j, Y') : 'Not specified',
            'endDate'   => $endDate   ? Carbon::parse($endDate)->format('F j, Y')   : 'Not specified',
        ];

        $pdf = Pdf::loadView('print-template.print-offenders', [
            'offensesData'   => $offensesData,
            'imagePath'      => $imagePath,
            'date'           => $date,
            'selectedDate'   => $formattedSelectedDate,
            'offenseFilter'  => $offenseFilter,
        ]);

        return $pdf->stream('offenders-per-sex.pdf');
    }
}
