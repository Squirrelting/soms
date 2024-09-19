<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OffensesController;
use App\Http\Controllers\StudentsController;

Route::get('/', [LoginController::class, 'index']);

Route::get('/dashboard', [StudentsController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/about', [StudentsController::class, 'about'])->middleware(['auth', 'verified'])->name('aboutus');
Route::get('/admin', [StudentsController::class, 'adminpage'])->middleware(['auth', 'verified'])->name('adminpage');

Route::prefix('students')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create', [StudentsController::class, 'create'])->name('students.create');
    Route::post('/', [StudentsController::class, 'store'])->name('students.store');
    Route::get('/{student}', [StudentsController::class, 'show'])->name('students.show_email');
    Route::get('/{student}/edit', [StudentsController::class, 'edit'])->name('students.edit');
    Route::put('/{student}', [StudentsController::class, 'update'])->name('students.update');
    Route::delete('/{student}', [StudentsController::class, 'destroy'])->name('students.destroy');
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

require __DIR__.'/auth.php';

