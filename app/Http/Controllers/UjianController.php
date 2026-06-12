<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleUjian;
use App\Models\BankSoal;
use App\Models\UjianAttempt;
use App\Models\UjianAnswer;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function index(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $ujians = LearningModuleUjian::where('learning_module_id', $learningModule->id)->latest()->get();

        return view('guru.learning_modules.ujians.index', compact('learningModule', 'ujians'));
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

        LearningModuleUjian::create([
            'learning_module_id' => $learningModule->id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('guru.learning-modules.ujians.index', $learningModule->id)
            ->with('status', 'Ujian berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'due_date' => ['required', 'date'],
        ]);

        $ujian->update([
            'title' => $request->title,
            'instructions' => $request->instructions,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('guru.learning-modules.ujians.index', $learningModule->id)
            ->with('status', 'Ujian berhasil diperbarui.');
    }

    public function destroy(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $ujian->delete();

        return redirect()->route('guru.learning-modules.ujians.index', $learningModule->id)
            ->with('status', 'Ujian berhasil dihapus.');
    }

    public function manageSoals(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $attachedSoalIds = $ujian->soals()->pluck('bank_soals.id')->toArray();

        // Get questions in bank that are NOT attached
        $availableSoals = BankSoal::where('mata_pelajaran_id', $learningModule->mata_pelajaran_id)
            ->where('guru_id', $guru->id)
            ->whereNotIn('id', $attachedSoalIds)
            ->with('options')
            ->get();

        $attachedSoals = $ujian->soals()->with('options')->get();

        return view('guru.learning_modules.ujians.soals', compact('learningModule', 'ujian', 'availableSoals', 'attachedSoals'));
    }

    public function attachSoal(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'soal_ids' => ['required', 'array'],
            'soal_ids.*' => ['exists:bank_soals,id'],
        ]);

        $maxUrutan = $ujian->soals()->max('ujian_soals.urutan') ?? 0;
        $currentOrder = max(1, $maxUrutan + 1);
        $syncData = [];
        foreach ($request->soal_ids as $soalId) {
            if (!$ujian->soals()->where('bank_soal_id', $soalId)->exists()) {
                $syncData[$soalId] = [
                    'urutan' => $currentOrder++
                ];
            }
        }
        if (!empty($syncData)) {
            $ujian->soals()->syncWithoutDetaching($syncData);
        }

        return redirect()->back()->with('status', 'Soal berhasil ditambahkan ke Ujian.');
    }

    public function detachSoal(LearningModule $learningModule, LearningModuleUjian $ujian, BankSoal $bankSoal)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $ujian->soals()->detach($bankSoal->id);

        return redirect()->back()->with('status', 'Soal berhasil dihapus dari Ujian.');
    }

    public function updateSoalOrder(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'soals' => ['required', 'array'],
            'soals.*.urutan' => ['required', 'integer', 'min:1'],
            'soals.*.bobot' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->soals as $soalId => $data) {
            $ujian->soals()->updateExistingPivot($soalId, [
                'urutan' => $data['urutan'],
                'bobot' => $data['bobot'],
            ]);
        }

        return redirect()->back()->with('status', 'Urutan dan bobot soal berhasil diperbarui.');
    }

    public function results(LearningModule $learningModule, LearningModuleUjian $ujian)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $attempts = UjianAttempt::where('learning_module_ujian_id', $ujian->id)
            ->with(['murid.user', 'answers.bankSoal'])
            ->latest()
            ->get();

        return view('guru.learning_modules.ujians.results', compact('learningModule', 'ujian', 'attempts'));
    }

    public function gradeEssay(Request $request, LearningModule $learningModule, LearningModuleUjian $ujian, UjianAttempt $attempt)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $attempt->ujian->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'skor' => ['required', 'array'],
            'skor.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($request->skor as $answerId => $skorVal) {
            $ans = UjianAnswer::findOrFail($answerId);
            $ans->update([
                'skor_manual' => $skorVal,
                'is_correct' => $skorVal > 0,
            ]);
        }

        $attempt->calculateScore();

        return redirect()->back()->with('status', 'Jawaban Essay berhasil dinilai.');
    }
}
