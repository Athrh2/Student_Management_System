<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController; // Don't forget to import this!
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// We can keep the default dashboard for now, or delete it later
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- PROTECTED STUDENT ROUTES ---
Route::middleware('auth')->group(function () {
    
    // 1. Profile Routes (Default from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Your Student Management Routes
    // Bulk Manage Page
    Route::get('/students/bulk-manage', [StudentController::class, 'bulkManage'])->name('students.bulkManage');
    Route::post('/students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulkDelete');
    
    // Export/Import
    Route::get('/students/export', [StudentController::class, 'exportCsv'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'importCsv'])->name('students.import');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/visualize', [StudentController::class, 'visualize'])->name('students.visualize');
    Route::get('/students/admin', [StudentController::class, 'admin'])->name('students.admin');

    // Main Resource (Index, Create, Store, Edit, Update, Destroy, Show)
    Route::resource('students', StudentController::class);
});

require __DIR__.'/auth.php';