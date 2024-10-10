<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\MajorOffense;
use App\Models\MinorOffense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OffendersExport;
use Maatwebsite\Excel\Facades\Excel;

class OffendersPerSexController extends Controller
{

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
        $endDate = $request->input('endDate') ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) : null;
        $offenseFilter = $request->offenseFilter ?? "";
    
        // Filter minor offenses
        $submittedminorOffenses = MinorOffense::withCount([
            'submittedMinorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMinorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Minor';
            return $offense;
        });
    
        // Filter major offenses
        $submittedmajorOffenses = MajorOffense::withCount([
            'submittedMajorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMajorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Major';
            return $offense;
        });

        $selectedDate = [
            'startDate' => $startDate ? Carbon::createFromFormat('Y-m-d H:i:s', $startDate) : null,
            'endDate' => $endDate ? Carbon::createFromFormat('Y-m-d H:i:s', $endDate) : null,
        ];
    
        // Combine offenses based on filter
        $offensesData = [];
        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            $offensesData = $submittedminorOffenses->merge($submittedmajorOffenses);
        }
        // Generate PDF
        $date = Carbon::now()->format('F j, Y');
        $formattedSelectedDate = [
            'startDate' => $selectedDate['startDate'] ? $selectedDate['startDate']->format('F j, Y') : 'Not specified',
            'endDate' => $selectedDate['endDate'] ? $selectedDate['endDate']->format('F j, Y') : 'Not specified',
        ];
    
        // Use a custom Excel export class
        return Excel::download(new OffendersExport($offensesData, $date, $formattedSelectedDate, $offenseFilter), 'offenders.xlsx');
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////
    public function printoffenders(Request $request)
    {
        $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
        $endDate = $request->input('endDate') ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) : null;
        $offenseFilter = $request->offenseFilter ?? "";
    
        // Filter minor offenses
        $submittedminorOffenses = MinorOffense::withCount([
            'submittedMinorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMinorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Minor';
            return $offense;
        });
    
        // Filter major offenses
        $submittedmajorOffenses = MajorOffense::withCount([
            'submittedMajorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMajorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Major';
            return $offense;
        });

        $selectedDate = [
            'startDate' => $startDate ? Carbon::createFromFormat('Y-m-d H:i:s', $startDate) : null,
            'endDate' => $endDate ? Carbon::createFromFormat('Y-m-d H:i:s', $endDate) : null,
        ];
    
        // Combine offenses based on filter
        $offensesData = [];
        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            $offensesData = $submittedminorOffenses->merge($submittedmajorOffenses);
        }
        // Generate PDF
        $imagePath = public_path('Images/SCNHS-Logo.png');
        $date = Carbon::now()->format('F j, Y');
        $formattedSelectedDate = [
            'startDate' => $selectedDate['startDate'] ? $selectedDate['startDate']->format('F j, Y') : 'Not specified',
            'endDate' => $selectedDate['endDate'] ? $selectedDate['endDate']->format('F j, Y') : 'Not specified',
        ];
        
        $pdf = Pdf::loadView('print-template.print-offenders', [
            'offensesData' => $offensesData,
            'imagePath' => $imagePath,
            'date' => $date,
            'selectedDate' => $formattedSelectedDate,
            'offenseFilter' => $offenseFilter,
        ]);
        
        return $pdf->stream('offenders-per-sex.pdf');
    }
    
    public function index(Request $request)
    {
        // Correctly extract start and end dates
        $startDate = $request->input('startDate') ? date('Y-m-d 00:00:00', strtotime($request->input('startDate'))) : null;
        $endDate = $request->input('endDate') ? date('Y-m-d 23:59:59', strtotime($request->input('endDate'))) : null;
        $offenseFilter = $request->offenseFilter ?? "";
    
        // Prepare minor offenses query
        $submittedminorOffenses = MinorOffense::withCount([
            'submittedMinorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMinorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Minor';
            return $offense;
        });
    
        // Prepare major offenses query
        $submittedmajorOffenses = MajorOffense::withCount([
            'submittedMajorOffenses as male_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Male');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            },
            'submittedMajorOffenses as female_count' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('student', function ($q) {
                    $q->where('sex', 'Female');
                });
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        ])
        ->havingRaw('(male_count > 0 OR female_count > 0)')
        ->get()
        ->map(function ($offense) {
            $offense->type = 'Major';
            return $offense;
        });
    
        // Filter offenses based on offense type
        $offensesData = [];
        if ($offenseFilter == 'Minor') {
            $offensesData = $submittedminorOffenses;
        } elseif ($offenseFilter == 'Major') {
            $offensesData = $submittedmajorOffenses;
        } else {
            // Combine both minor and major offenses when no filter is applied
            $offensesData = $submittedminorOffenses->merge($submittedmajorOffenses);
        }
    
        return Inertia::render('Report/OffendersPerSex', [
            'offensesData' => $offensesData,
        ]);
    }
    
    
}
