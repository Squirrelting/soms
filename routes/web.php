<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OffensesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SignatoryController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Route::get('/', [HomeController::class, 'home']);

Route::get('/dashboard', [StudentsController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/admin', [StudentsController::class, 'adminpage'])->middleware(['auth', 'verified'])->name('adminpage');
Route::get('/signatory', [StudentsController::class, 'signatorypage'])->middleware(['auth', 'verified'])->name('signatorypage');

//students
Route::prefix('students')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create', [StudentsController::class, 'create'])->name('students.create');
    Route::post('/', [StudentsController::class, 'store'])->name('students.store');
    Route::get('/{student}', [StudentsController::class, 'show'])->name('students.show_email');
    Route::get('/{student}/edit', [StudentsController::class, 'edit'])->name('students.edit');
    Route::get('/{student}/print', [StudentsController::class, 'print'])->name('students.print');
    Route::put('/{student}', [StudentsController::class, 'update'])->name('students.update');
    Route::delete('/{student}', [StudentsController::class, 'destroy'])->name('students.destroy');
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

    //Offenses
Route::prefix('minor')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{student}/minor', [OffensesController::class, 'minor'])->name('minor.offenses');
    Route::post('/', [OffensesController::class, 'store'])->name('minor.store');
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

require __DIR__.'/auth.php';

