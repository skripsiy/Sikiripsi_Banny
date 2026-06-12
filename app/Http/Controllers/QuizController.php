<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleQuiz;
use App\Models\BankSoal;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
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

    public function manageSoals(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $attachedSoalIds = $quiz->soals()->pluck('bank_soals.id')->toArray();

        // Get questions in bank that are NOT attached
        $availableSoals = BankSoal::where('subject_id', $learningModule->subject_id)
            ->where('guru_id', $guru->id)
            ->whereNotIn('id', $attachedSoalIds)
            ->with('options')
            ->get();

        $attachedSoals = $quiz->soals()->with('options')->get();

        return view('guru.learning_modules.quizzes.soals', compact('learningModule', 'quiz', 'availableSoals', 'attachedSoals'));
    }

    public function attachSoal(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'soal_ids' => ['required', 'array'],
            'soal_ids.*' => ['exists:bank_soals,id'],
        ]);

        $maxUrutan = $quiz->soals()->max('quiz_soals.urutan') ?? 0;
        $currentOrder = max(1, $maxUrutan + 1);
        $syncData = [];
        foreach ($request->soal_ids as $soalId) {
            if (!$quiz->soals()->where('bank_soal_id', $soalId)->exists()) {
                $syncData[$soalId] = [
                    'urutan' => $currentOrder++
                ];
            }
        }
        if (!empty($syncData)) {
            $quiz->soals()->syncWithoutDetaching($syncData);
        }

        return redirect()->back()->with('status', 'Soal berhasil ditambahkan ke Kuis.');
    }

    public function detachSoal(LearningModule $learningModule, LearningModuleQuiz $quiz, BankSoal $bankSoal)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $quiz->soals()->detach($bankSoal->id);

        return redirect()->back()->with('status', 'Soal berhasil dihapus dari Kuis.');
    }

    public function updateSoalOrder(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'soals' => ['required', 'array'],
            'soals.*.urutan' => ['required', 'integer', 'min:1'],
            'soals.*.bobot' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->soals as $soalId => $data) {
            $quiz->soals()->updateExistingPivot($soalId, [
                'urutan' => $data['urutan'],
                'bobot' => $data['bobot'],
            ]);
        }

        return redirect()->back()->with('status', 'Urutan dan bobot soal berhasil diperbarui.');
    }

    public function results(LearningModule $learningModule, LearningModuleQuiz $quiz)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $attempts = QuizAttempt::where('learning_module_quiz_id', $quiz->id)
            ->with(['murid.user', 'answers.bankSoal'])
            ->latest()
            ->get();

        return view('guru.learning_modules.quizzes.results', compact('learningModule', 'quiz', 'attempts'));
    }

    public function gradeEssay(Request $request, LearningModule $learningModule, LearningModuleQuiz $quiz, QuizAttempt $attempt)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $attempt->quiz->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'skor' => ['required', 'array'],
            'skor.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($request->skor as $answerId => $skorVal) {
            $ans = QuizAnswer::findOrFail($answerId);
            $ans->update([
                'skor_manual' => $skorVal,
                'is_correct' => $skorVal > 0,
            ]);
        }

        $attempt->calculateScore();

        return redirect()->back()->with('status', 'Jawaban Essay berhasil dinilai.');
    }
}
