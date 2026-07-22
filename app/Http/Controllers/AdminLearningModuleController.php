<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;

class AdminLearningModuleController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('nama_kelas')->get();
        $mataPelajarans = MataPelajaran::where('is_active', true)->orderBy('nama_pelajaran')->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();
        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();

        $selectedClassroomId = $request->query('classroom_id');
        $selectedMataPelajaranId = $request->query('mata_pelajaran_id');
        $selectedGuruId = $request->query('guru_id');
        $selectedSemesterId = $request->query('semester_id');

        $semesters = Semester::with('tahunAkademik')->latest()->get();

        $learningModules = LearningModule::with(['classroom', 'guru.user', 'mataPelajaran', 'tahunAkademik'])
            ->when($selectedClassroomId, function ($query, $classId) {
                return $query->where('classroom_id', $classId);
            })
            ->when($selectedMataPelajaranId, function ($query, $mapelId) {
                return $query->where('mata_pelajaran_id', $mapelId);
            })
            ->when($selectedGuruId, function ($query, $guruId) {
                return $query->where('guru_id', $guruId);
            })
            ->when($selectedSemesterId, function ($query, $semId) {
                $semester = Semester::find($semId);
                if ($semester) {
                    return $query->where('tahun_akademik_id', $semester->tahun_akademik_id);
                }
            })
            ->withCount([
                'materis' => function ($query) use ($selectedSemesterId) {
                    if ($selectedSemesterId) {
                        $query->where('semester_id', $selectedSemesterId);
                    }
                },
                'tugas' => function ($query) use ($selectedSemesterId) {
                    if ($selectedSemesterId) {
                        $query->where('semester_id', $selectedSemesterId);
                    }
                },
                'quizzes' => function ($query) use ($selectedSemesterId) {
                    if ($selectedSemesterId) {
                        $query->where('semester_id', $selectedSemesterId);
                    }
                },
                'ujians' => function ($query) use ($selectedSemesterId) {
                    if ($selectedSemesterId) {
                        $query->where('semester_id', $selectedSemesterId);
                    }
                },
                'absensis' => function ($query) use ($selectedSemesterId) {
                    if ($selectedSemesterId) {
                        $query->where('semester_id', $selectedSemesterId);
                    }
                }
            ])
            ->latest()
            ->get();

        return view('admin.manage.learning_modules.index', compact(
            'learningModules', 'classrooms', 'mataPelajarans', 'gurus', 'semesters', 'academicYears',
            'selectedClassroomId', 'selectedMataPelajaranId', 'selectedGuruId', 'selectedSemesterId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'description' => ['nullable', 'string'],
        ], [
            'guru_id.required' => 'Guru pengampu wajib dipilih.',
            'guru_id.exists' => 'Guru pengampu tidak valid.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',
            'tahun_akademik_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_akademik_id.exists' => 'Tahun ajaran tidak valid.',
            'classroom_id.required' => 'Kelas wajib dipilih.',
            'classroom_id.exists' => 'Kelas tidak valid.',
        ]);

        $classroom = Classroom::find($request->classroom_id);
        if ($classroom && $classroom->tahun_akademik_id != $request->tahun_akademik_id) {
            return redirect()->back()
                ->withErrors(['classroom_id' => 'Kelas yang dipilih tidak sesuai dengan Tahun Ajaran yang dipilih.'])
                ->withInput();
        }

        if ($classroom && !$classroom->mataPelajarans()->where('mata_pelajarans.id', $request->mata_pelajaran_id)->exists()) {
            return redirect()->back()
                ->withErrors(['classroom_id' => 'Mata pelajaran ini belum di-assign ke kelas tersebut dalam Kurikulum Kelas.'])
                ->withInput();
        }

        $mataPelajaran = MataPelajaran::findOrFail($request->mata_pelajaran_id);

        LearningModule::create([
            'guru_id' => $request->guru_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'classroom_id' => $request->classroom_id,
            'title' => $mataPelajaran->nama_pelajaran,
            'description' => $request->description ?? '',
        ]);

        return redirect()->route('admin.manage.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil ditambahkan.');
    }

    public function destroy(LearningModule $learningModule)
    {
        $learningModule->delete();

        return redirect()->route('admin.manage.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil dihapus.');
    }
}
