<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        $tugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)->latest()->get();

        return view('guru.learning_modules.tugas.index', compact('learningModule', 'tugas'));
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
            'due_date' => ['required', 'date'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules/tugas', 'public');
        }

        LearningModuleTugas::create([
            'learning_module_id' => $learningModule->id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'file_path' => $filePath,
        ]);

        return redirect()->route('guru.learning-modules.tugas.index', $learningModule->id)
            ->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_date' => ['required', 'date'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $data = [
            'title' => $request->title,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
        ];

        if ($request->hasFile('file')) {
            if ($tuga->file_path && Storage::disk('public')->exists($tuga->file_path)) {
                Storage::disk('public')->delete($tuga->file_path);
            }
            $data['file_path'] = $request->file('file')->store('learning_modules/tugas', 'public');
        }

        $tuga->update($data);

        return redirect()->route('guru.learning-modules.tugas.index', $learningModule->id)
            ->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $tuga->delete();

        return redirect()->route('guru.learning-modules.tugas.index', $learningModule->id)
            ->with('status', 'Tugas berhasil dihapus.');
    }
}
