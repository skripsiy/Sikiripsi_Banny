<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleTugas;
use App\Models\TugasSubmission;
use App\Models\Classroom;
use App\Models\Murid;
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

    public function submissions(LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');
        
        $jurusanId = $learningModule->mataPelajaran->jurusan_id;
        $query = Classroom::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
            ->where('is_active', true);
        if ($jurusanId) {
            $query->where('jurusan_id', $jurusanId);
        }
        $classroomIds = $query->pluck('id');

        $murids = Murid::whereIn('classroom_id', $classroomIds)
            ->with('user', 'classroom')
            ->get()
            ->sortBy(fn($m) => $m->user?->name ?? '')
            ->values();

        $submissions = TugasSubmission::where('learning_module_tugas_id', $tuga->id)
            ->get()
            ->keyBy('murid_id');

        return view('guru.learning_modules.tugas.submissions', compact('learningModule', 'tuga', 'murids', 'submissions'));
    }

    public function grade(Request $request, LearningModule $learningModule, TugasSubmission $submission)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($submission->tugas->learning_module_id !== $learningModule->id) {
            abort(404, 'Submission tidak ditemukan di modul ini.');
        }

        $request->validate([
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan_guru' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'nilai' => $request->nilai,
            'catatan_guru' => $request->catatan_guru,
            'graded_at' => now(),
        ]);

        return back()->with('status', 'Nilai berhasil disimpan.');
    }
}
