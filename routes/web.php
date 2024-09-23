<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SignatoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MajorOffensesController;
use App\Http\Controllers\MinorOffensesController;    
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/dashboard', [StudentsController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/signatory', [StudentsController::class, 'signatorypage'])->middleware(['auth', 'verified'])->name('signatorypage');

//students
Route::prefix('students')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create', [StudentsController::class, 'create'])->name('students.create');
    Route::post('/', [StudentsController::class, 'store'])->name('students.store');
    Route::get('/{student}', [StudentsController::class, 'email'])->name('students.show_email');
    Route::get('/{student}/edit', [StudentsController::class, 'edit'])->name('students.edit');
    Route::get('/{student}/print', [StudentsController::class, 'print'])->name('students.print');
    
    Route::put('/{student}', [StudentsController::class, 'update'])->name('students.update');
    Route::delete('/{student}', [StudentsController::class, 'destroy'])->name('students.destroy');
    Route::get('/{student}/print', [StudentsController::class, 'print'])->name('students.print');
    
    //send email
    Route::get('/{student}/send_email', [EmailController::class, 'sendemail'])->name('send.email');


});

//signatoy
Route::prefix('signatory')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create', [SignatoryController::class, 'create'])->name('signatory.create');
    Route::post('/', [SignatoryController::class, 'store'])->name('signatory.store');
    Route::get('/{signatory}/edit', [SignatoryController::class, 'edit'])->name('signatory.edit');
    Route::get('/{signatory}/print', [SignatoryController::class, 'print'])->name('signatory.print');
    Route::put('/{signatory}', [SignatoryController::class, 'update'])->name('signatory.update');
    Route::delete('/{signatory}', [SignatoryController::class, 'destroy'])->name('signatory.destroy');
});

    //Minor Offenses
Route::prefix('minor')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}/minor', [MinorOffensesController::class, 'minor'])->name('minor.offenses');
    Route::post('/', [MinorOffensesController::class, 'store'])->name('minor.store');
});

//Major Offenses
Route::prefix('major')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}/major', [MajorOffensesController::class, 'major'])->name('major.offenses');
    Route::post('/', [MajorOffensesController::class, 'store'])->name('major.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::group(['prefix' => 'roles-and-permissions'], function () {
    Route::group(['prefix' => 'roles', 'middleware' => 'can:Manage Roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('users.roles-permissions.roles.index');
        Route::get('/add', [RoleController::class, 'add'])->name('users.roles-permissions.roles.add');
        Route::post('/store', [RoleController::class, 'store'])->name('users.roles-permissions.roles.store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('users.roles-permissions.roles.edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('users.roles-permissions.roles.update');
        Route::delete('delete/{id}', [RoleController::class, 'destroy'])->name('users.roles-permissions.roles.delete');
        Route::post('/assignPermission/{id}', [RoleController::class, 'assignPermission'])->name('users.roles-permissions.roles.assignPermission');
    });

    Route::group(['prefix' => 'permissions', 'middleware' => 'can:Manage Permissions'], function () {
        Route::get('/', [PermissionController::class, 'index'])->name('users.roles-permissions.permissions.index');
        Route::get('/add', [PermissionController::class, 'add'])->name('users.roles-permissions.permissions.add');
        Route::post('/store', [PermissionController::class, 'store'])->name('users.roles-permissions.permissions.store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('users.roles-permissions.permissions.edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('users.roles-permissions.permissions.update');
        Route::delete('delete/{id}', [PermissionController::class, 'destroy'])->name('users.roles-permissions.permissions.destroy');
    });
});

require __DIR__.'/auth.php';

