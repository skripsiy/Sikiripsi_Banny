<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleUjian;
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
}
