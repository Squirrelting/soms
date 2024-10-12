<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request){
        return Inertia::render('Report/Index');
    }
}
