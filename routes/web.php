<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\GuruManagementController;
use App\Http\Controllers\MuridManagementController;
use App\Http\Controllers\TahunAjaranManagementController;
use App\Http\Controllers\JurusanManagementController;
use App\Http\Controllers\ClassroomManagementController;
use App\Http\Controllers\SubjectManagementController;
use App\Http\Controllers\PenugasanGuruController;

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

    // Tahun Ajaran
    Route::resource('tahun-ajarans', TahunAjaranManagementController::class);

    // Jurusan
    Route::resource('jurusans', JurusanManagementController::class);

    // Classroom
    Route::resource('classrooms', ClassroomManagementController::class);

    // Subject
    Route::resource('subjects', SubjectManagementController::class);
    Route::resource('penugasan-guru', PenugasanGuruController::class)->except(['create', 'show', 'edit']);

    Route::resource('admins', AdminManagementController::class);
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::resource('learning-modules', \App\Http\Controllers\LearningModuleController::class);
    Route::resource('learning-modules.materis', \App\Http\Controllers\MateriController::class)->except(['show']);
    Route::resource('learning-modules.tugas', \App\Http\Controllers\TugasController::class)->except(['show']);
    Route::resource('learning-modules.quizzes', \App\Http\Controllers\QuizController::class)->except(['show']);
    Route::resource('learning-modules.ujians', \App\Http\Controllers\UjianController::class)->except(['show']);
    Route::post('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\LearningModuleController::class, 'storeAbsensi'])->name('learning-modules.absensi.store');
    Route::get('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\LearningModuleController::class, 'absensi'])->name('learning-modules.absensi.index');
});

Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('learning-modules', [\App\Http\Controllers\MuridLearningModuleController::class, 'index'])->name('learning-modules.index');
    Route::get('learning-modules/{learning_module}', [\App\Http\Controllers\MuridLearningModuleController::class, 'show'])->name('learning-modules.show');
    Route::get('learning-modules/{learning_module}/materis', [\App\Http\Controllers\MuridLearningModuleController::class, 'materis'])->name('learning-modules.materis.index');
    Route::get('learning-modules/{learning_module}/tugas', [\App\Http\Controllers\MuridLearningModuleController::class, 'tugas'])->name('learning-modules.tugas.index');
    Route::get('learning-modules/{learning_module}/quizzes', [\App\Http\Controllers\MuridLearningModuleController::class, 'quizzes'])->name('learning-modules.quizzes.index');
    Route::get('learning-modules/{learning_module}/ujians', [\App\Http\Controllers\MuridLearningModuleController::class, 'ujians'])->name('learning-modules.ujians.index');
    Route::get('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\MuridLearningModuleController::class, 'absensi'])->name('learning-modules.absensi.index');
});

require __DIR__.'/auth.php';
