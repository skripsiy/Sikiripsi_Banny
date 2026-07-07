<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleMateri;
use App\Models\LearningModuleTugas;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\LearningModuleAbsensi;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\UjianAttempt;
use App\Models\UjianAnswer;
use Illuminate\Http\Request;

class MuridLearningModuleController extends Controller
{
    private function authorizeModule(LearningModule $learningModule)
    {
        $murid = auth()->user()->murid;
        if (!$murid) {
            abort(403, 'Profil Murid tidak ditemukan.');
        }

        $classroom = $murid->classroom;
        if (!$classroom || !$classroom->is_active) {
            abort(403, 'Kelas Anda tidak aktif atau tidak ditemukan.');
        }

        // Check if learning module has the same classroom
        if ($learningModule->classroom_id !== $classroom->id) {
            abort(403, 'Aksi tidak diizinkan. Modul tidak ditujukan untuk kelas Anda.');
        }
    }

    public function index()
    {
        $murid = auth()->user()->murid;
        if (!$murid) {
            abort(403, 'Profil Murid tidak ditemukan.');
        }

        $classroom = $murid->classroom;
        if (!$classroom) {
            $learningModules = collect();
        } else {
            $learningModules = LearningModule::where('classroom_id', $classroom->id)
                ->with(['guru.user', 'mataPelajaran', 'tahunAkademik'])
                ->withCount(['materis', 'tugas', 'quizzes', 'ujians'])
                ->latest()
                ->get();
        }

        return view('murid.learning_modules.index', compact('learningModules', 'classroom'));
    }

    public function show(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load(['mataPelajaran', 'guru.user', 'tahunAkademik']);
        
        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        // Load all sub-contents counts filtered by selected semester
        $learningModule->loadCount([
            'materis' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'tugas' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'quizzes' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'ujians' => fn($q) => $q->where('semester_id', $selectedSemesterId)
        ]);

        // Recent items (latest 10 of each for dashboard timeline) filtered by selected semester
        $recentMateris = $learningModule->materis()->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentTugas = $learningModule->tugas()->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentQuizzes = $learningModule->quizzes()->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentUjians = $learningModule->ujians()->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();

        // Map them to a unified activity feed for the timeline
        $materisMapped = $recentMateris->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'materi',
            'label' => 'Buka Materi',
            'description' => \Illuminate\Support\Str::limit(strip_tags($item->content), 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => false,
            'url' => route('murid.learning-modules.materis.index', [$learningModule->id, 'semester_id' => $selectedSemesterId]),
        ]);

        $tugasMapped = $recentTugas->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'tugas',
            'label' => 'Lihat Detail Tugas',
            'description' => \Illuminate\Support\Str::limit(strip_tags($item->instructions), 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.tugas.show', [$learningModule->id, $item->id]),
        ]);

        $quizzesMapped = $recentQuizzes->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'kuis',
            'label' => 'Buka Kuis',
            'description' => \Illuminate\Support\Str::limit(strip_tags($item->instructions), 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.quizzes.index', [$learningModule->id, 'semester_id' => $selectedSemesterId]),
        ]);

        $ujiansMapped = $recentUjians->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'ujian',
            'label' => 'Buka Ujian',
            'description' => \Illuminate\Support\Str::limit(strip_tags($item->instructions), 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.ujians.index', [$learningModule->id, 'semester_id' => $selectedSemesterId]),
        ]);

        $activities = collect()
            ->concat($materisMapped)
            ->concat($tugasMapped)
            ->concat($quizzesMapped)
            ->concat($ujiansMapped)
            ->sortByDesc('created_at')
            ->take(15)
            ->values();

        return view('murid.learning_modules.show', compact('learningModule', 'activities', 'semesters', 'selectedSemester'));
    }

    public function materis(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');
        
        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $materis = LearningModuleMateri::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->latest()
            ->get();

        return view('murid.learning_modules.materis.index', compact('learningModule', 'materis', 'semesters', 'selectedSemester'));
    }

    public function tugas(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');

        $murid = auth()->user()->murid;
        
        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $tugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->with(['submissions' => function ($query) use ($murid) {
                $query->where('murid_id', $murid->id);
            }])
            ->latest()
            ->get();

        return view('murid.learning_modules.tugas.index', compact('learningModule', 'tugas', 'semesters', 'selectedSemester'));
    }

    public function tugasShow(LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $this->authorizeModule($learningModule);

        if ($tuga->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $learningModule->load('mataPelajaran');
        $murid = auth()->user()->murid;

        $tuga->load(['submissions' => function ($query) use ($murid) {
            $query->where('murid_id', $murid->id);
        }]);

        $submission = $tuga->submissions->first();

        return view('murid.learning_modules.tugas.show', compact('learningModule', 'tuga', 'submission'));
    }

    public function quizzes(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');
        $murid = auth()->user()->murid;

        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $quizzes = LearningModuleQuiz::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->withCount('soals')
            ->latest()
            ->get();

        $attempts = QuizAttempt::where('murid_id', $murid->id)
            ->whereIn('learning_module_quiz_id', $quizzes->pluck('id'))
            ->get()
            ->keyBy('learning_module_quiz_id');

        return view('murid.learning_modules.quizzes.index', compact('learningModule', 'quizzes', 'attempts', 'semesters', 'selectedSemester'));
    }

    public function startQuiz(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($quiz->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        // Check if quiz has questions
        if ($quiz->soals()->count() === 0) {
            return redirect()->back()->withErrors(['error' => 'Kuis ini belum memiliki soal.']);
        }

        // Check for existing attempt
        $existing = QuizAttempt::where('murid_id', $murid->id)
            ->where('learning_module_quiz_id', $quiz->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'in_progress' && !$existing->isExpired()) {
                return redirect()->route('murid.learning-modules.quizzes.take', [$learningModule->id, $quiz->id]);
            }
            return redirect()->route('murid.learning-modules.quizzes.result', [$learningModule->id, $quiz->id]);
        }

        // Check due date
        if ($quiz->due_date && $quiz->due_date->isPast()) {
            return redirect()->back()->withErrors(['error' => 'Batas waktu pengerjaan kuis ini sudah lewat.']);
        }

        QuizAttempt::create([
            'learning_module_quiz_id' => $quiz->id,
            'murid_id' => $murid->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('murid.learning-modules.quizzes.take', [$learningModule->id, $quiz->id]);
    }

    public function takeQuiz(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($quiz->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = QuizAttempt::where('murid_id', $murid->id)
            ->where('learning_module_quiz_id', $quiz->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('murid.learning-modules.quizzes.result', [$learningModule->id, $quiz->id]);
        }

        if ($attempt->isExpired()) {
            // Auto submit empty or force calculation
            $attempt->update([
                'finished_at' => $attempt->started_at->copy()->addMinutes($quiz->duration_minutes),
                'status' => 'submitted',
            ]);
            $attempt->calculateScore();
            return redirect()->route('murid.learning-modules.quizzes.result', [$learningModule->id, $quiz->id])
                ->with('status', 'Waktu pengerjaan telah habis. Jawaban Anda otomatis dikirim.');
        }

        $attempt->load('answers');
        $existingAnswers = [];
        foreach ($attempt->answers as $ans) {
            $existingAnswers[$ans->bank_soal_id] = $ans->bankSoal->tipe === 'pg' ? $ans->jawaban_pg : $ans->jawaban_essay;
        }

        // Get questions
        $soals = $quiz->soals()->with('options')->get();

        return view('murid.learning_modules.quizzes.take', compact('learningModule', 'quiz', 'attempt', 'soals', 'existingAnswers'));
    }

    public function submitQuiz(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($quiz->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = QuizAttempt::where('murid_id', $murid->id)
            ->where('learning_module_quiz_id', $quiz->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('murid.learning-modules.quizzes.result', [$learningModule->id, $quiz->id]);
        }

        $finishedAt = now();
        if ($attempt->isExpired()) {
            $finishedAt = $attempt->started_at->copy()->addMinutes($quiz->duration_minutes);
        }

        $attempt->update([
            'finished_at' => $finishedAt,
            'status' => 'submitted',
        ]);

        $jawaban = $request->input('jawaban', []);

        foreach ($quiz->soals as $soal) {
            $ansData = [];
            if ($soal->tipe === 'pg') {
                $ansData['jawaban_pg'] = $jawaban[$soal->id] ?? null;
                $ansData['jawaban_essay'] = null;
            } else {
                $ansData['jawaban_essay'] = $jawaban[$soal->id] ?? null;
                $ansData['jawaban_pg'] = null;
            }

            QuizAnswer::updateOrCreate([
                'quiz_attempt_id' => $attempt->id,
                'bank_soal_id' => $soal->id,
            ], $ansData);
        }

        $attempt->calculateScore();

        return redirect()->route('murid.learning-modules.quizzes.result', [$learningModule->id, $quiz->id])
            ->with('status', 'Kuis berhasil dikirim.');
    }

    public function saveQuizAnswer(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        $attempt = QuizAttempt::where('murid_id', $murid->id)
            ->where('learning_module_quiz_id', $quiz->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress' || $attempt->isExpired()) {
            return response()->json(['error' => 'Kuis sudah selesai.'], 403);
        }

        $bankSoalId = $request->input('bank_soal_id');
        $tipe = $request->input('tipe');
        $nilai = $request->input('nilai');

        $ansData = [];
        if ($tipe === 'pg') {
            $ansData['jawaban_pg'] = $nilai;
            $ansData['jawaban_essay'] = null;
        } else {
            $ansData['jawaban_essay'] = $nilai;
            $ansData['jawaban_pg'] = null;
        }

        $answer = QuizAnswer::updateOrCreate([
            'quiz_attempt_id' => $attempt->id,
            'bank_soal_id' => $bankSoalId,
        ], $ansData);

        return response()->json(['success' => true, 'answer_id' => $answer->id]);
    }

    public function quizResult(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($quiz->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = QuizAttempt::where('murid_id', $murid->id)
            ->where('learning_module_quiz_id', $quiz->id)
            ->with(['answers.bankSoal.options'])
            ->firstOrFail();

        $soals = $quiz->soals()->with('options')->get();

        return view('murid.learning_modules.quizzes.result', compact('learningModule', 'quiz', 'attempt', 'soals'));
    }

    public function ujians(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');
        $murid = auth()->user()->murid;

        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $ujians = LearningModuleUjian::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->withCount('soals')
            ->latest()
            ->get();

        $attempts = UjianAttempt::where('murid_id', $murid->id)
            ->whereIn('learning_module_ujian_id', $ujians->pluck('id'))
            ->get()
            ->keyBy('learning_module_ujian_id');

        return view('murid.learning_modules.ujians.index', compact('learningModule', 'ujians', 'attempts', 'semesters', 'selectedSemester'));
    }

    public function startUjian(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($ujian->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        // Check if ujian has questions
        if ($ujian->soals()->count() === 0) {
            return redirect()->back()->withErrors(['error' => 'Ujian ini belum memiliki soal.']);
        }

        $existing = UjianAttempt::where('murid_id', $murid->id)
            ->where('learning_module_ujian_id', $ujian->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'in_progress' && !$existing->isExpired()) {
                return redirect()->route('murid.learning-modules.ujians.take', [$learningModule->id, $ujian->id]);
            }
            return redirect()->route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id]);
        }

        if ($ujian->due_date && $ujian->due_date->isPast()) {
            return redirect()->back()->withErrors(['error' => 'Batas waktu pengerjaan ujian ini sudah lewat.']);
        }

        UjianAttempt::create([
            'learning_module_ujian_id' => $ujian->id,
            'murid_id' => $murid->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('murid.learning-modules.ujians.take', [$learningModule->id, $ujian->id]);
    }

    public function takeUjian(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($ujian->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = UjianAttempt::where('murid_id', $murid->id)
            ->where('learning_module_ujian_id', $ujian->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id]);
        }

        if ($attempt->isExpired()) {
            $attempt->update([
                'finished_at' => $attempt->started_at->copy()->addMinutes($ujian->duration_minutes),
                'status' => 'submitted',
            ]);
            $attempt->calculateScore();
            return redirect()->route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id])
                ->with('status', 'Waktu pengerjaan telah habis. Jawaban Anda otomatis dikirim.');
        }

        $attempt->load('answers');
        $existingAnswers = [];
        foreach ($attempt->answers as $ans) {
            $existingAnswers[$ans->bank_soal_id] = $ans->bankSoal->tipe === 'pg' ? $ans->jawaban_pg : $ans->jawaban_essay;
        }

        $soals = $ujian->soals()->with('options')->get();

        return view('murid.learning_modules.ujians.take', compact('learningModule', 'ujian', 'attempt', 'soals', 'existingAnswers'));
    }

    public function submitUjian(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($ujian->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = UjianAttempt::where('murid_id', $murid->id)
            ->where('learning_module_ujian_id', $ujian->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id]);
        }

        $finishedAt = now();
        if ($attempt->isExpired()) {
            $finishedAt = $attempt->started_at->copy()->addMinutes($ujian->duration_minutes);
        }

        $attempt->update([
            'finished_at' => $finishedAt,
            'status' => 'submitted',
        ]);

        $jawaban = $request->input('jawaban', []);

        foreach ($ujian->soals as $soal) {
            $ansData = [];
            if ($soal->tipe === 'pg') {
                $ansData['jawaban_pg'] = $jawaban[$soal->id] ?? null;
                $ansData['jawaban_essay'] = null;
            } else {
                $ansData['jawaban_essay'] = $jawaban[$soal->id] ?? null;
                $ansData['jawaban_pg'] = null;
            }

            UjianAnswer::updateOrCreate([
                'ujian_attempt_id' => $attempt->id,
                'bank_soal_id' => $soal->id,
            ], $ansData);
        }

        $attempt->calculateScore();

        return redirect()->route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id])
            ->with('status', 'Ujian berhasil dikirim.');
    }

    public function saveUjianAnswer(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        $attempt = UjianAttempt::where('murid_id', $murid->id)
            ->where('learning_module_ujian_id', $ujian->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress' || $attempt->isExpired()) {
            return response()->json(['error' => 'Ujian sudah selesai.'], 403);
        }

        $bankSoalId = $request->input('bank_soal_id');
        $tipe = $request->input('tipe');
        $nilai = $request->input('nilai');

        $ansData = [];
        if ($tipe === 'pg') {
            $ansData['jawaban_pg'] = $nilai;
            $ansData['jawaban_essay'] = null;
        } else {
            $ansData['jawaban_essay'] = $nilai;
            $ansData['jawaban_pg'] = null;
        }

        $answer = UjianAnswer::updateOrCreate([
            'ujian_attempt_id' => $attempt->id,
            'bank_soal_id' => $bankSoalId,
        ], $ansData);

        return response()->json(['success' => true, 'answer_id' => $answer->id]);
    }

    public function ujianResult(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $this->authorizeModule($learningModule);
        $murid = auth()->user()->murid;

        if ($ujian->learning_module_id !== $learningModule->id) {
            abort(404);
        }

        $attempt = UjianAttempt::where('murid_id', $murid->id)
            ->where('learning_module_ujian_id', $ujian->id)
            ->with(['answers.bankSoal.options'])
            ->firstOrFail();

        $soals = $ujian->soals()->with('options')->get();

        return view('murid.learning_modules.ujians.result', compact('learningModule', 'ujian', 'attempt', 'soals'));
    }

    public function absensi(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');

        $murid = auth()->user()->murid;

        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->where('murid_id', $murid->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('murid.learning_modules.absensi.index', compact('learningModule', 'absensis', 'semesters', 'selectedSemester'));
    }

    public function rekapNilai(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('mataPelajaran');

        $murid = auth()->user()->murid;

        $semesters = \App\Models\Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        $tugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->with(['submissions' => function ($query) use ($murid) {
                $query->where('murid_id', $murid->id);
            }])
            ->latest()
            ->get();

        return view('murid.learning_modules.rekap_nilai.index', compact('learningModule', 'tugas', 'semesters', 'selectedSemester'));
    }
}
