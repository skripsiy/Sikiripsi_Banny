<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleMateri;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');

        $selectedSemesterId = $request->query('semester_id');
        if (!$selectedSemesterId) {
            $selectedSemesterId = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                    ->where('is_active', true)
                    ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->value('id');
        }

        $materis = LearningModuleMateri::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->latest()
            ->get();

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();

        return view('guru.learning_modules.materis.index', compact('learningModule', 'materis', 'selectedSemesterId', 'semesters'));
    }

    public function create(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');
        $selectedSemesterId = $request->query('semester_id');

        return view('guru.learning_modules.materis.create', compact('learningModule', 'selectedSemesterId'));
    }

    public function store(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'title' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules/materi', 'public');
        }

        $semesterId = $request->semester_id;
        if (!$semesterId) {
            $semesterId = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                    ->where('is_active', true)
                    ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                    ->value('id');
        }

        LearningModuleMateri::create([
            'learning_module_id' => $learningModule->id,
            'semester_id' => $semesterId,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Materi berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleMateri $materi)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $materi->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'title' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $semesterId = $request->semester_id ?? $materi->semester_id;

        $data = [
            'semester_id' => $semesterId,
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

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Materi berhasil diperbarui.');
    }

    public function edit(LearningModule $learningModule, LearningModuleMateri $materi)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $materi->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $selectedSemesterId = $materi->semester_id;

        return view('guru.learning_modules.materis.edit', compact('learningModule', 'materi', 'selectedSemesterId'));
    }

    public function destroy(Request $request, LearningModule $learningModule, LearningModuleMateri $materi)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $materi->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $semesterId = $materi->semester_id;
        $materi->delete();

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Materi berhasil dihapus.');
    }
}
