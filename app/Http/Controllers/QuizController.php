<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleQuiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $quizzes = LearningModuleQuiz::where('learning_module_id', $learningModule->id)->latest()->get();

        return view('guru.learning_modules.quizzes.index', compact('learningModule', 'quizzes'));
    }

    public function store(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'due_date' => ['required', 'date'],
        ]);

        LearningModuleQuiz::create([
            'learning_module_id' => $learningModule->id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('guru.learning-modules.quizzes.index', $learningModule->id)
            ->with('status', 'Kuis berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'due_date' => ['required', 'date'],
        ]);

        $quiz->update([
            'title' => $request->title,
            'instructions' => $request->instructions,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('guru.learning-modules.quizzes.index', $learningModule->id)
            ->with('status', 'Kuis berhasil diperbarui.');
    }

    public function destroy(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $quiz->delete();

        return redirect()->route('guru.learning-modules.quizzes.index', $learningModule->id)
            ->with('status', 'Kuis berhasil dihapus.');
    }
}
