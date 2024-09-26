<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;

class PieController extends Controller
{
   public function getPieData(Request $request) 
   {
       // Get the start and end date from the request
       $startDate = $request->input('start_date');
       $endDate = $request->input('end_date');
   
       $endDate = date('Y-m-d 23:59:59', strtotime($endDate));

       // Query offenses by sex for the given date range
       $maleOffendersMinor = Student::where('sex', 'Male')
           ->whereHas('submittedMinorOffenses', function($query) use ($startDate, $endDate) {
               $query->whereBetween('created_at', [$startDate, $endDate]);
           })->count();
   
       $femaleOffendersMinor = Student::where('sex', 'Female')
           ->whereHas('submittedMinorOffenses', function($query) use ($startDate, $endDate) {
               $query->whereBetween('created_at', [$startDate, $endDate]);
           })->count();
   
       $maleOffendersMajor = Student::where('sex', 'Male')
           ->whereHas('submittedMajorOffenses', function($query) use ($startDate, $endDate) {
               $query->whereBetween('created_at', [$startDate, $endDate]);
           })->count();
   
       $femaleOffendersMajor = Student::where('sex', 'Female')
           ->whereHas('submittedMajorOffenses', function($query) use ($startDate, $endDate) {
               $query->whereBetween('created_at', [$startDate, $endDate]);
           })->count();
   
       // Prepare data for the frontend
       $offenseData = [
           'male' => $maleOffendersMinor + $maleOffendersMajor,
           'female' => $femaleOffendersMinor + $femaleOffendersMajor
       ];
   
       return response()->json($offenseData);
   }
   
}

