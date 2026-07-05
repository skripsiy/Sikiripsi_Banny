<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleTugas;
use App\Models\TugasSubmission;
use App\Models\Classroom;
use App\Models\Murid;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
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

        $tugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)
            ->latest()
            ->get();

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();

        return view('guru.learning_modules.tugas.index', compact('learningModule', 'tugas', 'selectedSemesterId', 'semesters'));
    }

    public function create(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');
        $selectedSemesterId = $request->query('semester_id');

        return view('guru.learning_modules.tugas.create', compact('learningModule', 'selectedSemesterId'));
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
            'instructions' => ['required', 'string'],
            'due_date' => ['required', 'date'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules/tugas', 'public');
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

        LearningModuleTugas::create([
            'learning_module_id' => $learningModule->id,
            'semester_id' => $semesterId,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'file_path' => $filePath,
        ]);

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'title' => ['required', 'string', 'max:50'],
            'instructions' => ['required', 'string'],
            'due_date' => ['required', 'date'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $semesterId = $request->semester_id ?? $tuga->semester_id;

        $data = [
            'semester_id' => $semesterId,
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

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Tugas berhasil diperbarui.');
    }

    public function edit(LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi otonom tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');
        $selectedSemesterId = $tuga->semester_id;

        return view('guru.learning_modules.tugas.edit', compact('learningModule', 'tuga', 'selectedSemesterId'));
    }

    public function destroy(Request $request, LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $semesterId = $tuga->semester_id;
        $tuga->delete();

        return redirect()->route('guru.learning-modules.show', [$learningModule->id, 'semester_id' => $semesterId])
            ->with('status', 'Tugas berhasil dihapus.');
    }

    public function submissions(LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $tuga->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');
        
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
