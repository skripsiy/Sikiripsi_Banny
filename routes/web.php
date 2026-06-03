<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\GuruManagementController;
use App\Http\Controllers\MuridManagementController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin/manage')->name('admin.manage.')->group(function () {
    // Gurus Template & Import
    Route::get('gurus/template', [GuruManagementController::class, 'downloadTemplate'])->name('gurus.template');
    Route::post('gurus/import', [GuruManagementController::class, 'import'])->name('gurus.import');
    Route::resource('gurus', GuruManagementController::class);

    // Murids Template & Import
    Route::get('murids/template', [MuridManagementController::class, 'downloadTemplate'])->name('murids.template');
    Route::post('murids/import', [MuridManagementController::class, 'import'])->name('murids.import');
    Route::resource('murids', MuridManagementController::class);

    Route::resource('admins', AdminManagementController::class);
});

require __DIR__.'/auth.php';
