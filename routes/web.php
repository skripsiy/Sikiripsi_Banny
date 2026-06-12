<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\GuruManagementController;
use App\Http\Controllers\MuridManagementController;
use App\Http\Controllers\SemesterManagementController;
use App\Http\Controllers\JurusanManagementController;
use App\Http\Controllers\ClassroomManagementController;
use App\Http\Controllers\MataPelajaranManagementController;
use App\Http\Controllers\PenugasanGuruController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $data = [];

    if ($user->role === 'admin') {
        $data['total_guru'] = \App\Models\Guru::count();
        $data['total_murid'] = \App\Models\Murid::count();
        $data['total_kelas'] = \App\Models\Classroom::where('is_active', true)->count();
        $data['total_modul'] = \App\Models\LearningModule::count();
    } elseif ($user->role === 'guru') {
        $guru = $user->guru;
        $moduleIds = $guru ? \App\Models\LearningModule::where('guru_id', $guru->id)->pluck('id') : collect();
        $tugasIds = \App\Models\LearningModuleTugas::whereIn('learning_module_id', $moduleIds)->pluck('id');

        $data['total_modul'] = $moduleIds->count();
        $data['pending_grades'] = \App\Models\TugasSubmission::whereIn('learning_module_tugas_id', $tugasIds)
            ->whereNull('nilai')
            ->count();
        $data['pending_izins'] = \App\Models\IzinRequest::whereIn('learning_module_id', $moduleIds)
            ->where('status', 'pending')
            ->count();
    } elseif ($user->role === 'murid') {
        $murid = $user->murid;
        if ($murid) {
            $classroom = $murid->classroom;
            if ($classroom) {
                $modules = \App\Models\LearningModule::where('tahun_akademik_id', $classroom->tahun_akademik_id)
                    ->whereHas('mataPelajaran', function ($query) use ($classroom) {
                        $query->where('is_active', true)
                              ->where(function ($q) use ($classroom) {
                                  $q->whereNull('jurusan_id')
                                    ->orWhere('jurusan_id', $classroom->jurusan_id);
                              });
                    })->get();
                
                $data['total_modul'] = $modules->count();

                $moduleIds = $modules->pluck('id');
                $tugasList = \App\Models\LearningModuleTugas::whereIn('learning_module_id', $moduleIds)->get();
                $submittedTugasIds = \App\Models\TugasSubmission::where('murid_id', $murid->id)->pluck('learning_module_tugas_id')->toArray();
                
                $unsubmittedCount = 0;
                foreach ($tugasList as $t) {
                    if (!in_array($t->id, $submittedTugasIds)) {
                        if (!$t->due_date || $t->due_date->isFuture()) {
                            $unsubmittedCount++;
                        }
                    }
                }
                $data['unsubmitted_tasks'] = $unsubmittedCount;

                $absensis = \App\Models\LearningModuleAbsensi::where('murid_id', $murid->id)->get();
                $totalHadir = $absensis->where('status', 'hadir')->count();
                $totalPertemuan = $absensis->count();
                $data['attendance_percentage'] = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 100;
            } else {
                $data['total_modul'] = 0;
                $data['unsubmitted_tasks'] = 0;
                $data['attendance_percentage'] = 100;
            }
        } else {
            $data['total_modul'] = 0;
            $data['unsubmitted_tasks'] = 0;
            $data['attendance_percentage'] = 100;
        }
    }

    return view('dashboard', compact('data'));
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

    // Semester
    Route::resource('semesters', SemesterManagementController::class);

    // Jurusan
    Route::resource('jurusans', JurusanManagementController::class);

    // Classroom
    Route::resource('classrooms', ClassroomManagementController::class);

    // Mata Pelajaran
    Route::resource('mata-pelajarans', MataPelajaranManagementController::class)->names('mata_pelajarans');
    Route::resource('penugasan-guru', PenugasanGuruController::class)->except(['create', 'show', 'edit']);

    Route::resource('admins', AdminManagementController::class);
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::resource('learning-modules', \App\Http\Controllers\LearningModuleController::class);
    Route::resource('learning-modules.materis', \App\Http\Controllers\MateriController::class)->except(['show']);
    Route::resource('learning-modules.tugas', \App\Http\Controllers\TugasController::class)->except(['show']);
    Route::get('learning-modules/{learning_module}/tugas/{tuga}/submissions', [\App\Http\Controllers\TugasController::class, 'submissions'])->name('learning-modules.tugas.submissions');
    Route::post('learning-modules/{learning_module}/tugas/submissions/{submission}/grade', [\App\Http\Controllers\TugasController::class, 'grade'])->name('learning-modules.tugas.grade');
    
    // Bank Soal
    Route::resource('bank-soal', \App\Http\Controllers\BankSoalController::class)->except(['show', 'create', 'edit']);

    // Quiz management
    Route::resource('learning-modules.quizzes', \App\Http\Controllers\QuizController::class)->except(['show']);
    Route::get('learning-modules/{learning_module}/quizzes/{quiz}/soals', [\App\Http\Controllers\QuizController::class, 'manageSoals'])->name('learning-modules.quizzes.soals');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/soals', [\App\Http\Controllers\QuizController::class, 'attachSoal'])->name('learning-modules.quizzes.soals.attach');
    Route::delete('learning-modules/{learning_module}/quizzes/{quiz}/soals/{bank_soal}', [\App\Http\Controllers\QuizController::class, 'detachSoal'])->name('learning-modules.quizzes.soals.detach');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/soals/order', [\App\Http\Controllers\QuizController::class, 'updateSoalOrder'])->name('learning-modules.quizzes.soals.order');
    Route::get('learning-modules/{learning_module}/quizzes/{quiz}/results', [\App\Http\Controllers\QuizController::class, 'results'])->name('learning-modules.quizzes.results');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/attempts/{attempt}/grade', [\App\Http\Controllers\QuizController::class, 'gradeEssay'])->name('learning-modules.quizzes.grade-essay');

    // Ujian management
    Route::resource('learning-modules.ujians', \App\Http\Controllers\UjianController::class)->except(['show']);
    Route::get('learning-modules/{learning_module}/ujians/{ujian}/soals', [\App\Http\Controllers\UjianController::class, 'manageSoals'])->name('learning-modules.ujians.soals');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/soals', [\App\Http\Controllers\UjianController::class, 'attachSoal'])->name('learning-modules.ujians.soals.attach');
    Route::delete('learning-modules/{learning_module}/ujians/{ujian}/soals/{bank_soal}', [\App\Http\Controllers\UjianController::class, 'detachSoal'])->name('learning-modules.ujians.soals.detach');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/soals/order', [\App\Http\Controllers\UjianController::class, 'updateSoalOrder'])->name('learning-modules.ujians.soals.order');
    Route::get('learning-modules/{learning_module}/ujians/{ujian}/results', [\App\Http\Controllers\UjianController::class, 'results'])->name('learning-modules.ujians.results');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/attempts/{attempt}/grade', [\App\Http\Controllers\UjianController::class, 'gradeEssay'])->name('learning-modules.ujians.grade-essay');

    Route::post('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\LearningModuleController::class, 'storeAbsensi'])->name('learning-modules.absensi.store');
    Route::get('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\LearningModuleController::class, 'absensi'])->name('learning-modules.absensi.index');
    Route::get('learning-modules/{learning_module}/rekap-absensi', [\App\Http\Controllers\LearningModuleController::class, 'rekapAbsensi'])->name('learning-modules.rekap-absensi');
    Route::get('learning-modules/{learning_module}/export-absensi', [\App\Http\Controllers\LearningModuleController::class, 'exportAbsensi'])->name('learning-modules.export-absensi');
    Route::get('learning-modules/{learning_module}/izin', [\App\Http\Controllers\IzinRequestController::class, 'listPending'])->name('learning-modules.izin.index');
    Route::post('learning-modules/{learning_module}/izin/{izin_request}/approve', [\App\Http\Controllers\IzinRequestController::class, 'approve'])->name('learning-modules.izin.approve');
    Route::post('learning-modules/{learning_module}/izin/{izin_request}/reject', [\App\Http\Controllers\IzinRequestController::class, 'reject'])->name('learning-modules.izin.reject');
});

Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('learning-modules', [\App\Http\Controllers\MuridLearningModuleController::class, 'index'])->name('learning-modules.index');
    Route::get('learning-modules/{learning_module}', [\App\Http\Controllers\MuridLearningModuleController::class, 'show'])->name('learning-modules.show');
    Route::get('learning-modules/{learning_module}/materis', [\App\Http\Controllers\MuridLearningModuleController::class, 'materis'])->name('learning-modules.materis.index');
    Route::get('learning-modules/{learning_module}/tugas', [\App\Http\Controllers\MuridLearningModuleController::class, 'tugas'])->name('learning-modules.tugas.index');
    Route::post('learning-modules/{learning_module}/tugas/{tuga}/submit', [\App\Http\Controllers\TugasSubmissionController::class, 'store'])->name('learning-modules.tugas.submit');
    Route::get('learning-modules/{learning_module}/rekap-nilai', [\App\Http\Controllers\MuridLearningModuleController::class, 'rekapNilai'])->name('learning-modules.rekap-nilai');
    
    // Quiz online taking
    Route::get('learning-modules/{learning_module}/quizzes', [\App\Http\Controllers\MuridLearningModuleController::class, 'quizzes'])->name('learning-modules.quizzes.index');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/start', [\App\Http\Controllers\MuridLearningModuleController::class, 'startQuiz'])->name('learning-modules.quizzes.start');
    Route::get('learning-modules/{learning_module}/quizzes/{quiz}/take', [\App\Http\Controllers\MuridLearningModuleController::class, 'takeQuiz'])->name('learning-modules.quizzes.take');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/save-answer', [\App\Http\Controllers\MuridLearningModuleController::class, 'saveQuizAnswer'])->name('learning-modules.quizzes.save-answer');
    Route::post('learning-modules/{learning_module}/quizzes/{quiz}/submit', [\App\Http\Controllers\MuridLearningModuleController::class, 'submitQuiz'])->name('learning-modules.quizzes.submit');
    Route::get('learning-modules/{learning_module}/quizzes/{quiz}/result', [\App\Http\Controllers\MuridLearningModuleController::class, 'quizResult'])->name('learning-modules.quizzes.result');

    // Ujian online taking
    Route::get('learning-modules/{learning_module}/ujians', [\App\Http\Controllers\MuridLearningModuleController::class, 'ujians'])->name('learning-modules.ujians.index');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/start', [\App\Http\Controllers\MuridLearningModuleController::class, 'startUjian'])->name('learning-modules.ujians.start');
    Route::get('learning-modules/{learning_module}/ujians/{ujian}/take', [\App\Http\Controllers\MuridLearningModuleController::class, 'takeUjian'])->name('learning-modules.ujians.take');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/save-answer', [\App\Http\Controllers\MuridLearningModuleController::class, 'saveUjianAnswer'])->name('learning-modules.ujians.save-answer');
    Route::post('learning-modules/{learning_module}/ujians/{ujian}/submit', [\App\Http\Controllers\MuridLearningModuleController::class, 'submitUjian'])->name('learning-modules.ujians.submit');
    Route::get('learning-modules/{learning_module}/ujians/{ujian}/result', [\App\Http\Controllers\MuridLearningModuleController::class, 'ujianResult'])->name('learning-modules.ujians.result');

    Route::get('learning-modules/{learning_module}/absensi', [\App\Http\Controllers\MuridLearningModuleController::class, 'absensi'])->name('learning-modules.absensi.index');
    Route::get('learning-modules/{learning_module}/izin', [\App\Http\Controllers\IzinRequestController::class, 'index'])->name('learning-modules.izin.index');
    Route::get('learning-modules/{learning_module}/izin/create', [\App\Http\Controllers\IzinRequestController::class, 'create'])->name('learning-modules.izin.create');
    Route::post('learning-modules/{learning_module}/izin', [\App\Http\Controllers\IzinRequestController::class, 'store'])->name('learning-modules.izin.store');
});

require __DIR__.'/auth.php';
