<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');
        $materis = LearningModuleMateri::where('learning_module_id', $learningModule->id)->latest()->get();

        return view('guru.learning_modules.materis.index', compact('learningModule', 'materis'));
    }

    public function store(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules/materi', 'public');
        }

        LearningModuleMateri::create([
            'learning_module_id' => $learningModule->id,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);

        return redirect()->route('guru.learning-modules.materis.index', $learningModule->id)
            ->with('status', 'Materi berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleMateri $materi)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $materi->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
        ];

        if ($request->hasFile('file')) {
            if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $data['file_path'] = $request->file('file')->store('learning_modules/materi', 'public');
        }

        $materi->update($data);

        return redirect()->route('guru.learning-modules.materis.index', $learningModule->id)
            ->with('status', 'Materi berhasil diperbarui.');
    }

    public function destroy(LearningModule $learningModule, LearningModuleMateri $materi)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $materi->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $materi->delete();

        return redirect()->route('guru.learning-modules.materis.index', $learningModule->id)
            ->with('status', 'Materi berhasil dihapus.');
    }
}
