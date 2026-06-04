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

        // Check if learning module has the same academic year
        if ($learningModule->tahun_ajaran_id !== $classroom->tahun_ajaran_id) {
            abort(403, 'Aksi tidak diizinkan. Modul tidak sesuai dengan tahun ajaran kelas Anda.');
        }

        // Check if subject is suitable for the student's jurusan
        $subject = $learningModule->subject;
        if (!$subject || !$subject->is_active) {
            abort(403, 'Mata pelajaran untuk modul ini tidak aktif atau tidak ditemukan.');
        }

        if ($subject->jurusan_id !== null && $subject->jurusan_id !== $classroom->jurusan_id) {
            abort(403, 'Aksi tidak diizinkan. Modul tidak sesuai dengan jurusan kelas Anda.');
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
            $learningModules = LearningModule::where('tahun_ajaran_id', $classroom->tahun_ajaran_id)
                ->whereHas('subject', function ($query) use ($classroom) {
                    $query->where('is_active', true)
                          ->where(function ($q) use ($classroom) {
                              $q->whereNull('jurusan_id')
                                ->orWhere('jurusan_id', $classroom->jurusan_id);
                          });
                })
                ->with(['guru.user', 'subject', 'tahunAjaran'])
                ->withCount(['materis', 'tugas', 'quizzes', 'ujians'])
                ->latest()
                ->get();
        }

        return view('murid.learning_modules.index', compact('learningModules', 'classroom'));
    }

    public function show(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load(['subject', 'guru.user', 'tahunAjaran']);
        $learningModule->loadCount(['materis', 'tugas', 'quizzes', 'ujians']);

        // Recent items (latest 10 of each for dashboard timeline)
        $recentMateris = $learningModule->materis()->latest()->limit(10)->get();
        $recentTugas = $learningModule->tugas()->latest()->limit(10)->get();
        $recentQuizzes = $learningModule->quizzes()->latest()->limit(10)->get();
        $recentUjians = $learningModule->ujians()->latest()->limit(10)->get();

        // Map them to a unified activity feed for the timeline
        $materisMapped = $recentMateris->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'materi',
            'description' => \Illuminate\Support\Str::limit($item->content, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => false,
            'url' => route('murid.learning-modules.materis.index', $learningModule->id),
        ]);

        $tugasMapped = $recentTugas->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'tugas',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.tugas.index', $learningModule->id),
        ]);

        $quizzesMapped = $recentQuizzes->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'kuis',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.quizzes.index', $learningModule->id),
        ]);

        $ujiansMapped = $recentUjians->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'ujian',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('murid.learning-modules.ujians.index', $learningModule->id),
        ]);

        $activities = collect()
            ->concat($materisMapped)
            ->concat($tugasMapped)
            ->concat($quizzesMapped)
            ->concat($ujiansMapped)
            ->sortByDesc('created_at')
            ->take(15)
            ->values();

        return view('murid.learning_modules.show', compact('learningModule', 'activities'));
    }

    public function materis(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('subject');
        $materis = LearningModuleMateri::where('learning_module_id', $learningModule->id)
            ->latest()
            ->get();

        return view('murid.learning_modules.materis', compact('learningModule', 'materis'));
    }

    public function tugas(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('subject');
        $tugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)
            ->latest()
            ->get();

        return view('murid.learning_modules.tugas', compact('learningModule', 'tugas'));
    }

    public function quizzes(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('subject');
        $quizzes = LearningModuleQuiz::where('learning_module_id', $learningModule->id)
            ->latest()
            ->get();

        return view('murid.learning_modules.quizzes', compact('learningModule', 'quizzes'));
    }

    public function ujians(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('subject');
        $ujians = LearningModuleUjian::where('learning_module_id', $learningModule->id)
            ->latest()
            ->get();

        return view('murid.learning_modules.ujians', compact('learningModule', 'ujians'));
    }

    public function absensi(LearningModule $learningModule)
    {
        $this->authorizeModule($learningModule);

        $learningModule->load('subject');
        
        $murid = auth()->user()->murid;

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->where('murid_id', $murid->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('murid.learning_modules.absensi', compact('learningModule', 'absensis'));
    }
}
