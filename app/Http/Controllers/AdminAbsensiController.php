<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\Murid;
use App\Models\LearningModule;
use App\Models\LearningModuleAbsensi;
use Illuminate\Http\Request;

class AdminAbsensiController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with(['jurusan', 'tahunAkademik'])
            ->withCount('murids')
            ->latest()
            ->get();

        return view('admin.manage.absensi.index', compact('classrooms'));
    }

    public function show(Classroom $classroom, Request $request)
    {
        $classroom->load(['jurusan', 'tahunAkademik']);

        $semesters = Semester::where('tahun_akademik_id', $classroom->tahun_akademik_id)->get();
        $selectedSemester = null;
        if ($request->has('semester_id')) {
            $selectedSemester = $semesters->where('id', $request->semester_id)->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->where('is_active', true)->first() ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        // Get unique subjects linked to this classroom via learning modules
        $learningModules = LearningModule::where('classroom_id', $classroom->id)->get();
        $subjects = MataPelajaran::whereIn('id', $learningModules->pluck('mata_pelajaran_id'))->get();

        $selectedSubjectId = $request->query('mata_pelajaran_id');

        $murids = Murid::where('classroom_id', $classroom->id)
            ->with('user')
            ->get()
            ->sortBy(fn($m) => $m->user?->name ?? '')
            ->values();

        // Get attendance records, optionally filtered by subject (through learning module)
        $absensis = LearningModuleAbsensi::whereIn('murid_id', $murids->pluck('id'))
            ->when($selectedSemesterId, function ($query, $semId) {
                return $query->where('semester_id', $semId);
            })
            ->when($selectedSubjectId, function ($query, $mapelId) {
                return $query->whereHas('learningModule', function ($q) use ($mapelId) {
                    $q->where('mata_pelajaran_id', $mapelId);
                });
            })
            ->get();

        $rekap = $murids->map(function ($murid) use ($absensis) {
            $studentAbsensis = $absensis->where('murid_id', $murid->id);
            $totalHadir = $studentAbsensis->where('status', 'hadir')->count();
            $totalSakit = $studentAbsensis->where('status', 'sakit')->count();
            $totalIzin = $studentAbsensis->where('status', 'izin')->count();
            $totalAlpa = $studentAbsensis->where('status', 'alpa')->count();
            $totalPertemuan = $studentAbsensis->count();

            $percentage = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 100;

            return (object) [
                'murid' => $murid,
                'hadir' => $totalHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'alpa' => $totalAlpa,
                'pertemuan' => $totalPertemuan,
                'percentage' => $percentage
            ];
        });

        return view('admin.manage.absensi.show', compact(
            'classroom', 'semesters', 'selectedSemester', 'subjects', 'selectedSubjectId', 'rekap'
        ));
    }
}
