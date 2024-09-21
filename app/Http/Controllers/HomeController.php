<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function home(){
        return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
            
            
        ]);

    }
}
