<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class LineChartController extends Controller
{
    public function getLineData(Request $request)
    {
        // Convert start date and end date to appropriate format with time range
        $startDate = date('Y-m-d 00:00:00', strtotime($request->input('start_date')));
        $endDate = date('Y-m-d 23:59:59', strtotime($request->input('end_date')));

        // Group minor offenses by date
        $minorOffenses = Student::whereHas('submittedMinorOffenses', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->with([
                'submittedMinorOffenses' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->selectRaw('DATE(submitted_minor_offenses.created_at) as date, COUNT(*) as count')
            ->join('submitted_minor_offenses', 'students.lrn', '=', 'submitted_minor_offenses.lrn')
            ->whereBetween('submitted_minor_offenses.created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        // Group major offenses by date
        $majorOffenses = Student::whereHas('submittedMajorOffenses', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->with([
                'submittedMajorOffenses' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->selectRaw('DATE(submitted_major_offenses.created_at) as date, COUNT(*) as count')
            ->join('submitted_major_offenses', 'students.lrn', '=', 'submitted_major_offenses.lrn')
            ->whereBetween('submitted_major_offenses.created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        // Prepare data for frontend
        $data = [
            'minor' => $minorOffenses,
            'major' => $majorOffenses
        ];

        return response()->json($data);
    }
}
