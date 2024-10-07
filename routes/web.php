<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarGraphController;
use App\Http\Controllers\PieChartController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LineChartController;
use App\Http\Controllers\SignatoryController;
use App\Http\Controllers\MajorOffensesController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\StudentsImportController;
use App\Http\Controllers\OffendersPerSexController;
use App\Http\Controllers\MinorOffensesController;    

//Dashboard Graphs
Route::get('/get-pie-data', [PieChartController::class, 'getPieData'])->name('get.pie.data');
Route::get('/get-line-data', [LineChartController::class, 'getLineData'])->name('get.line.data');
Route::get('/get-bar-data', [BarGraphController::class, 'getBarData'])->name('get.bar.data');

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/offenders', [OffendersPerSexController::class, 'index'])->name('offenders.index');
    //print offenders male/female
    Route::get('/print-offenders', [OffendersPerSexController::class, 'printoffenders'])->name('printoffenders');
});


//students
Route::prefix('students')->middleware(['auth', 'verified'])->group(function () {
    // API route for fetching sections dynamically
    Route::get('/sections', [StudentsController::class, 'getSections'])->name('students.sections');
    Route::get('/', [StudentsController::class, 'index'])->name('students.index');
    Route::get('/create', [StudentsController::class, 'create'])->name('students.create');
    Route::get('/{student}/edit', [StudentsController::class, 'edit'])->name('students.edit');
    Route::put('/{student}', [StudentsController::class, 'update'])->name('students.update');
    Route::post('/', [StudentsController::class, 'store'])->name('students.store');
    Route::get('/{student}/print', [StudentsController::class, 'print'])->name('students.print');

    Route::delete('/{student}', [StudentsController::class, 'destroy'])->name('students.destroy');
    Route::get('/{student}/print', [StudentsController::class, 'print'])->name('students.print');

    //print cgm
    Route::get('/print-certificate/{signatory}/{student}', [PrintController::class, 'printcgm'])->name('printcgm');
});

    //student Import
Route::prefix('email')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}', [EmailController::class, 'email'])->name('show.email');
    Route::post('/{student}/send_email', [EmailController::class, 'sendemail'])->name('send.email');
});


    //student Import
Route::prefix('import')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [StudentsImportController::class, 'index'])->name('import.students');
    Route::post('/store', [StudentsImportController::class, 'store'])->name('import.store');
});

    //add section
Route::prefix('section')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SectionsController::class, 'index'])->name('section.index');
    Route::get('/create', [SectionsController::class, 'create'])->name('section.create');
    Route::post('/store', [SectionsController::class, 'store'])->name('section.store');
    Route::get('/{section}/edit', [SectionsController::class, 'edit'])->name('section.edit');
    Route::put('/{section}', [SectionsController::class, 'update'])->name('section.update');
    Route::delete('/{section}', [SectionsController::class, 'destroy'])->name('section.destroy');

    });
    

//signatory
Route::prefix('signatory')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SignatoryController::class, 'index'])->name('signatory.index');
    Route::get('/create', [SignatoryController::class, 'create'])->name('signatory.create');
    Route::post('/', [SignatoryController::class, 'store'])->name('signatory.store');
    Route::get('/{signatory}/edit', [SignatoryController::class, 'edit'])->name('signatory.edit');
    Route::put('/{signatory}', [SignatoryController::class, 'update'])->name('signatory.update');
    Route::delete('/{signatory}', [SignatoryController::class, 'destroy'])->name('signatory.destroy');
});

// Registered User Routes
Route::prefix('user')->middleware(['auth', 'verified', 'can:Manage POD Users'])->group(function () {
    Route::get('/', [RegisteredUserController::class, 'index'])->name('user.index');
    Route::get('/{user}/edit', [RegisteredUserController::class, 'edit'])->name('user.edit');
    Route::put('/{user}', [RegisteredUserController::class, 'update'])->name('user.update');
    Route::delete('/{user}', [RegisteredUserController::class, 'destroy'])->name('user.destroy');
    Route::get('register', [RegisteredUserController::class, 'create'])->name('user.add');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('user.store');
});


    //Minor Offenses
Route::prefix('minor')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}/minor', [MinorOffensesController::class, 'minor'])->name('minor.offenses');
    Route::post('/', [MinorOffensesController::class, 'store'])->name('minor.store');
    Route::post('/{offense}/sanction', [MinorOffensesController::class, 'sanction'])->name('minor.sanction');
});

//Major Offenses
Route::prefix('major')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}/major', [MajorOffensesController::class, 'major'])->name('major.offenses');
    Route::post('/', [MajorOffensesController::class, 'store'])->name('major.store');
    Route::post('/{offense}/sanction', [MajorOffensesController::class, 'sanction'])->name('major.sanction');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['prefix' => 'roles-and-permissions'], function () {
    Route::group(['prefix' => 'roles', 'middleware' => 'can:Manage Roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('users.roles-permissions.roles.index');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('users.roles-permissions.roles.edit');
        Route::post('/assignPermission/{id}', [RoleController::class, 'assignPermission'])->name('users.roles-permissions.roles.assignPermission');
    });
});

require __DIR__.'/auth.php';

