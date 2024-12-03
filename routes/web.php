<?php

use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\BarGraphController;
use App\Http\Controllers\PieChartController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LineChartController;
use App\Http\Controllers\SignatoryController;
use App\Http\Controllers\MajorOffensesController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AddMajorOffensesController;
use App\Http\Controllers\AddMinorOffensesController;
use App\Http\Controllers\MinorOffensesController;    
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/get-status', [AuthenticatedSessionController::class, 'checkStatus'])->name('getStatus');


Route::post('/students/check-lrn', [StudentsController::class, 'checkLRN'])->name('check.lrn');

//Dashboard Graphs
Route::get('/get-pie-data', [PieChartController::class, 'getPieData'])->name('get.pie.data');
Route::get('/get-line-data', [LineChartController::class, 'getLineData'])->name('get.line.data');
Route::get('/get-bar-data', [BarGraphController::class, 'getBarData'])->name('get.bar.data');
Route::get('/get-grade-data/{selectedSchoolyear}/{selectedQuarter?}', [DashboardController::class, 'getGradeData'])->name('get.grade.data');


Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

//Reports
Route::prefix('reports')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('reports.index');

    Route::get('/print-offenders', [ReportsController::class, 'printoffenders'])->name('printoffenders');
    Route::get('/export-excel', [ReportsController::class, 'exportExcel'])->name('exportexcel');
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
    Route::get('/{student}/view', [ViewController::class, 'view'])->name('students.view');
    Route::get('/print-record/{student}', [ViewController::class, 'printRecord'])->name('printrecord');

});

//Print-cgm
Route::prefix('print')->middleware(['auth', 'verified', 'can:Manage Good Moral'])->group(function () {
    Route::get('/', [PrintController::class, 'index'])->name('print.index');
    Route::post('/', [PrintController::class, 'store'])->name('print.store');
    Route::delete('/{print}', [PrintController::class, 'destroy'])->name('print.destroy');
    Route::get('/print/{lrn}', [PrintController::class, 'view'])->name('print.view');
    Route::delete('/print/{id}', [PrintController::class, 'destroy'])->name('print.destroy');
});
Route::get("/print/print-certificate/{signatory}/{firstname}/{middlename}/{lastname}", [PrintController::class, 'printcgm'])->name('print.cgm');



    //add section
Route::prefix('section')->middleware(['auth', 'verified', 'can:Manage Sections'])->group(function () {
    Route::get('/', [SectionsController::class, 'index'])->name('section.index');
    Route::get('/create', [SectionsController::class, 'create'])->name('section.create');
    Route::post('/store', [SectionsController::class, 'store'])->name('section.store');
    Route::get('/{section}/edit', [SectionsController::class, 'edit'])->name('section.edit');
    Route::put('/{section}', [SectionsController::class, 'update'])->name('section.update');
    Route::delete('/{section}', [SectionsController::class, 'destroy'])->name('section.destroy');
    });

        //add minor offenses
Route::prefix('add-offense-minor')->middleware(['auth', 'verified', 'can:Manage Offenses'])->group(function () {
    Route::get('/', [AddMinorOffensesController::class, 'index'])->name('minoroffense.index');
    Route::get('/create', [AddMinorOffensesController::class, 'create'])->name('minoroffense.create');
    Route::post('/store', [AddMinorOffensesController::class, 'store'])->name('minoroffense.store');
    Route::get('/{minor}/edit', [AddMinorOffensesController::class, 'edit'])->name('minoroffense.edit');
    Route::put('/{minor}', [AddMinorOffensesController::class, 'update'])->name('minoroffense.update');
    Route::delete('/{minor}', [AddMinorOffensesController::class, 'destroy'])->name('minoroffense.destroy');
    });

            //add major offenses
Route::prefix('add-offense-major')->middleware(['auth', 'verified', 'can:Manage Offenses'])->group(function () {
    Route::get('/', [AddMajorOffensesController::class, 'index'])->name('majoroffense.index');
    Route::get('/create', [AddMajorOffensesController::class, 'create'])->name('majoroffense.create');
    Route::post('/store', [AddMajorOffensesController::class, 'store'])->name('majoroffense.store');
    Route::get('/{major}/edit', [AddMajorOffensesController::class, 'edit'])->name('majoroffense.edit');
    Route::put('/{major}', [AddMajorOffensesController::class, 'update'])->name('majoroffense.update');
    Route::delete('/{major}', [AddMajorOffensesController::class, 'destroy'])->name('majoroffense.destroy');
    });


//add signatory
Route::prefix('signatory')->middleware(['auth', 'verified', 'can:Manage Signatory'])->group(function () {
    Route::get('/', [SignatoryController::class, 'index'])->name('signatory.index');
    Route::get('/create', [SignatoryController::class, 'create'])->name('signatory.create');
    Route::post('/', [SignatoryController::class, 'store'])->name('signatory.store');
    Route::get('/{signatory}/edit', [SignatoryController::class, 'edit'])->name('signatory.edit');
    Route::put('/{signatory}', [SignatoryController::class, 'update'])->name('signatory.update');
    Route::delete('/{signatory}', [SignatoryController::class, 'destroy'])->name('signatory.destroy');
});

// Registered User Routes
Route::prefix('user')->middleware(['auth', 'verified', 'can:Manage Users'])->group(function () {
    Route::get('/', [RegisteredUserController::class, 'index'])->name('user.index');
    Route::get('/{user}/edit', [RegisteredUserController::class, 'edit'])->name('user.edit');
    Route::put('/{user}', [RegisteredUserController::class, 'update'])->name('user.update');
    Route::delete('/{user}', [RegisteredUserController::class, 'destroy'])->name('user.destroy');
    Route::get('register', [RegisteredUserController::class, 'create'])->name('user.add');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('user.store');
    Route::patch('/{user}/toggle-status', [RegisteredUserController::class, 'toggleStatus'])
    ->name('user.toggleStatus');

});


    //Minor Offenses
Route::prefix('minor')->middleware(['auth', 'verified', 'can:Student Offenses'])->group(function () {
    Route::get('/{student}/minor', [MinorOffensesController::class, 'minor'])->name('minor.offenses');
    Route::post('/', [MinorOffensesController::class, 'store'])->name('minor.store');
    Route::post('/{offense}/sanction', [MinorOffensesController::class, 'sanction'])->name('minor.sanction');
});

//Major Offenses
Route::prefix('major')->middleware(['auth', 'verified', 'can:Student Offenses'])->group(function () {
    Route::get('/{student}/major', [MajorOffensesController::class, 'major'])->name('major.offenses');
    Route::post('/', [MajorOffensesController::class, 'store'])->name('major.store');
    Route::post('/{offense}/sanction', [MajorOffensesController::class, 'sanction'])->name('major.sanction');

});

//Backup
Route::prefix('backup')->middleware(['auth', 'verified', 'can:Student Offenses'])->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/db', [BackupController::class, 'backup'])->name('backup.db');
    Route::get('/files', [BackupController::class, 'listFiles']);
    Route::get('/download/{fileName}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('/restore', [BackupController::class, 'restore'])->name('backup.restore');
    Route::get('/manual', [BackupController::class, 'manual'])->name('backup.manual');

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

