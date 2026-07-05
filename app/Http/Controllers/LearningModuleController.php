<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\MataPelajaran;
use App\Models\LearningModuleMateri;
use App\Models\LearningModuleTugas;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\LearningModuleAbsensi;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\IzinRequest;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningModuleController extends Controller
{
    public function index()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        $activeAcademicYear = TahunAkademik::where('is_active', true)->first();
        
        $selectedAcademicYearId = request('tahun_akademik_id', 'all');
        $selectedSemester = 'all';

        // Get modules owned by this teacher
        $learningModules = LearningModule::where('guru_id', $guru->id)
            ->when($selectedAcademicYearId && $selectedAcademicYearId !== 'all', function($q) use ($selectedAcademicYearId) {
                return $q->where('tahun_akademik_id', $selectedAcademicYearId);
            })
            ->with(['mataPelajaran', 'tahunAkademik'])
            ->withCount(['materis', 'tugas', 'quizzes', 'ujians', 'absensis'])
            ->latest()
            ->get();

        // Get only mata_pelajarans assigned to this teacher
        $mata_pelajarans = $guru->mataPelajarans()
            ->where('is_active', true)
            ->orderBy('nama_pelajaran')
            ->get();

        $semesters = $academicYears;
        $selectedTahunAkademikId = $activeAcademicYear?->id;

        return view('guru.learning_modules.index', compact('learningModules', 'mata_pelajarans', 'semesters', 'academicYears', 'selectedAcademicYearId', 'selectedSemester', 'selectedTahunAkademikId'));
    }

    public function create()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        $activeAcademicYear = TahunAkademik::where('is_active', true)->first();

        $mata_pelajarans = $guru->mataPelajarans()
            ->where('is_active', true)
            ->orderBy('nama_pelajaran')
            ->get();

        $semesters = $academicYears;
        $selectedTahunAkademikId = $activeAcademicYear?->id;

        return view('guru.learning_modules.create', compact('mata_pelajarans', 'semesters', 'selectedTahunAkademikId'));
    }

    public function store(Request $request)
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',
            'tahun_akademik_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_akademik_id.exists' => 'Tahun ajaran tidak valid.',
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
            'description.required' => 'Deskripsi modul wajib diisi.',
        ]);

        // Authorize that the teacher is assigned to this subject
        if (!$guru->mataPelajarans()->where('mata_pelajarans.id', $request->mata_pelajaran_id)->exists()) {
            return redirect()->back()
                ->withErrors(['mata_pelajaran_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        LearningModule::create([
            'guru_id' => $guru->id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('guru.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',
            'tahun_akademik_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_akademik_id.exists' => 'Tahun ajaran tidak valid.',
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
            'description.required' => 'Deskripsi modul wajib diisi.',
        ]);

        // Authorize that the teacher is assigned to this subject
        if (!$guru->mataPelajarans()->where('mata_pelajarans.id', $request->mata_pelajaran_id)->exists()) {
            return redirect()->back()
                ->withErrors(['mata_pelajaran_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        $data = [
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        $learningModule->update($data);

        return redirect()->route('guru.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil diperbarui.');
    }

    public function edit(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();

        $mata_pelajarans = $guru->mataPelajarans()
            ->where('is_active', true)
            ->orderBy('nama_pelajaran')
            ->get();

        $semesters = $academicYears;

        return view('guru.learning_modules.edit', compact('learningModule', 'mata_pelajarans', 'semesters'));
    }

    public function destroy(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->delete();

        return redirect()->route('guru.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil dihapus.');
    }

    public function show(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->filter(function($s) {
                return date('Y-m-d') >= $s->start_date && date('Y-m-d') <= $s->end_date;
            })->first() 
            ?? $semesters->where('is_active', true)->first() 
            ?? $semesters->first();
        }
        $selectedSemesterId = $selectedSemester?->id;

        // Load all sub-contents counts filtered by selected semester
        $learningModule->loadCount([
            'materis' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'tugas' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'quizzes' => fn($q) => $q->where('semester_id', $selectedSemesterId),
            'ujians' => fn($q) => $q->where('semester_id', $selectedSemesterId)
        ]);

        // Load recent items (latest 10 of each for dashboard timeline) filtered by selected semester
        $recentMateris = LearningModuleMateri::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentTugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentQuizzes = LearningModuleQuiz::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();
        $recentUjians = LearningModuleUjian::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemesterId)->latest()->limit(10)->get();

        // Get student list based on subject's jurusan and module's academic year
        $jurusanId = $learningModule->mataPelajaran->jurusan_id;
        $query = Classroom::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
            ->where('is_active', true);
        if ($jurusanId) {
            $query->where('jurusan_id', $jurusanId);
        }
        $classroomIds = $query->pluck('id');

        $muridsCount = Murid::whereIn('classroom_id', $classroomIds)->count();

        // Map them to a unified activity feed
        $materisMapped = $recentMateris->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'materi',
            'description' => \Illuminate\Support\Str::limit($item->content, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => false,
            'url' => route('guru.learning-modules.materis.index', $learningModule->id),
        ]);

        $tugasMapped = $recentTugas->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'tugas',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('guru.learning-modules.tugas.index', $learningModule->id),
        ]);

        $quizzesMapped = $recentQuizzes->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'kuis',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('guru.learning-modules.quizzes.index', $learningModule->id),
        ]);

        $ujiansMapped = $recentUjians->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'type' => 'ujian',
            'description' => \Illuminate\Support\Str::limit($item->instructions, 80),
            'created_at' => $item->created_at,
            'created_at_formatted' => $item->created_at->translatedFormat('d F Y H:i'),
            'due_date_formatted' => $item->due_date ? $item->due_date->translatedFormat('d F Y H:i') : null,
            'is_recent' => $item->created_at->diffInDays(now()) <= 7,
            'is_upcoming' => $item->due_date ? $item->due_date->isFuture() : false,
            'url' => route('guru.learning-modules.ujians.index', $learningModule->id),
        ]);

        $activities = collect()
            ->concat($materisMapped)
            ->concat($tugasMapped)
            ->concat($quizzesMapped)
            ->concat($ujiansMapped)
            ->sortByDesc('created_at')
            ->take(15) // Show top 15 overall
            ->values();

        return view('guru.learning_modules.show', compact(
            'learningModule',
            'muridsCount',
            'activities',
            'semesters',
            'selectedSemester'
        ));
    }

    public function absensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');

        // Selected date for attendance
        $date = request('date', date('Y-m-d'));

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = null;
        if (request()->has('semester_id')) {
            $selectedSemester = $semesters->where('id', request('semester_id'))->first();
        }
        if (!$selectedSemester) {
            // Find semester by date range
            $selectedSemester = $semesters->filter(function($s) use ($date) {
                return $date >= $s->start_date && $date <= $s->end_date;
            })->first();
        }
        if (!$selectedSemester) {
            $selectedSemester = $semesters->where('is_active', true)->first() ?? $semesters->first();
        }

        // Get student list based on subject's jurusan and module's academic year
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

        // Load existing attendance
        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->where('semester_id', $selectedSemester?->id)
            ->where('date', $date)
            ->get()
            ->keyBy('murid_id');

        // Load pending leave requests for the selected date
        $pendingIzinForDate = IzinRequest::where('learning_module_id', $learningModule->id)
            ->whereDate('date', $date)
            ->where('status', 'pending')
            ->get()
            ->keyBy('murid_id');

        // Get total pending leave requests count for this module
        $totalPendingIzinCount = IzinRequest::where('learning_module_id', $learningModule->id)
            ->where('status', 'pending')
            ->count();

        return view('guru.learning_modules.absensi.index', compact(
            'learningModule',
            'murids',
            'absensis',
            'date',
            'semesters',
            'selectedSemester',
            'pendingIzinForDate',
            'totalPendingIzinCount'
        ));
    }

    public function storeAbsensi(Request $request, LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'date' => ['required', 'date'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'status' => ['required', 'array'],
            'status.*' => ['in:hadir,sakit,izin,alpa'],
        ]);

        $date = $request->date;
        $semesterId = $request->semester_id;
        if (!$semesterId) {
            $semesterId = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                    ->where('is_active', true)
                    ->value('id')
                ?? Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)
                    ->value('id');
        }

        foreach ($request->status as $muridId => $status) {
            $oldAbsensi = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
                ->where('murid_id', $muridId)
                ->where('date', $date)
                ->where('semester_id', $semesterId)
                ->first();

            $newStatus = $status;
            $shouldNotify = false;

            if ($newStatus === 'alpa') {
                if (!$oldAbsensi || $oldAbsensi->status !== $newStatus) {
                    $shouldNotify = true;
                }
            }

            $absensi = LearningModuleAbsensi::updateOrCreate(
                [
                    'learning_module_id' => $learningModule->id,
                    'murid_id' => $muridId,
                    'date' => $date,
                    'semester_id' => $semesterId,
                ],
                [
                    'status' => $newStatus,
                ]
            );

            if ($shouldNotify) {
                $murid = $absensi->murid;
                if ($murid && $murid->no_telepon_orang_tua) {
                    $subjectName = $learningModule->mataPelajaran->nama_pelajaran;
                    $studentName = $murid->user->name ?? 'Siswa';
                    $statusLabel = ucfirst($newStatus);
                    $formattedDate = Carbon::parse($date)->translatedFormat('d F Y');

                    $message = "Notifikasi Kehadiran SMKN 1 Jakarta:\n" .
                        "Yth. Orang Tua/Wali dari siswa *{$studentName}*,\n\n" .
                        "Menginformasikan bahwa putra/putri Anda dinyatakan *{$statusLabel}* pada mata pelajaran *{$subjectName}* pada tanggal *{$formattedDate}*.\n\n" .
                        "Terima kasih atas perhatian Anda.";

                    FonnteService::sendWhatsappNotification($murid->no_telepon_orang_tua, $message);
                }
            }
        }

        return redirect()->route('guru.learning-modules.absensi.index', [
            'learning_module' => $learningModule->id,
            'date' => $date,
            'semester_id' => $semesterId,
        ])->with('status', 'Absensi berhasil disimpan.');
    }

    public function rekapAbsensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = $semesters->where('id', request('semester_id'))->first() ?? $semesters->where('is_active', true)->first() ?? $semesters->first();

        // Get student list based on subject's jurusan and module's academic year
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

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->when($selectedSemester, function($q) use ($selectedSemester) {
                return $q->where('semester_id', $selectedSemester->id);
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

        return view('guru.learning_modules.absensi.rekap', compact('learningModule', 'rekap', 'semesters', 'selectedSemester'));
    }

    public function exportAbsensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('mataPelajaran');

        $semesters = Semester::where('tahun_akademik_id', $learningModule->tahun_akademik_id)->get();
        $selectedSemester = $semesters->where('id', request('semester_id'))->first() ?? $semesters->where('is_active', true)->first() ?? $semesters->first();

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

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->when($selectedSemester, function($q) use ($selectedSemester) {
                return $q->where('semester_id', $selectedSemester->id);
            })
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'REKAPITULASI ABSENSI SISWA');
        $sheet->setCellValue('A2', 'Modul: ' . $learningModule->title);
        $sheet->setCellValue('A3', 'Mata Pelajaran: ' . $learningModule->mataPelajaran->nama_pelajaran);
        $sheet->setCellValue('A4', 'Tahun Ajaran: ' . ($learningModule->tahunAkademik->tahun_ajaran ?? '-'));
        $sheet->setCellValue('A5', 'Semester: ' . ($selectedSemester ? $selectedSemester->semester : '-'));
        $sheet->setCellValue('A6', 'Dicetak pada: ' . date('d F Y H:i'));

        $sheet->setCellValue('A8', 'No');
        $sheet->setCellValue('B8', 'Nama Siswa');
        $sheet->setCellValue('C8', 'NISN');
        $sheet->setCellValue('D8', 'Kelas');
        $sheet->setCellValue('E8', 'Hadir');
        $sheet->setCellValue('F8', 'Sakit');
        $sheet->setCellValue('G8', 'Izin');
        $sheet->setCellValue('H8', 'Alpa');
        $sheet->setCellValue('I8', 'Total Pertemuan');
        $sheet->setCellValue('J8', 'Persentase Kehadiran (%)');

        $rowNum = 9;
        foreach ($murids as $index => $murid) {
            $studentAbsensis = $absensis->where('murid_id', $murid->id);
            $totalHadir = $studentAbsensis->where('status', 'hadir')->count();
            $totalSakit = $studentAbsensis->where('status', 'sakit')->count();
            $totalIzin = $studentAbsensis->where('status', 'izin')->count();
            $totalAlpa = $studentAbsensis->where('status', 'alpa')->count();
            $totalPertemuan = $studentAbsensis->count();
            $percentage = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 100;

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $murid->user->name ?? '-');
            $sheet->setCellValue('C' . $rowNum, $murid->nisn);
            $sheet->setCellValue('D' . $rowNum, $murid->classroom->nama_kelas ?? '-');
            $sheet->setCellValue('E' . $rowNum, $totalHadir);
            $sheet->setCellValue('F' . $rowNum, $totalSakit);
            $sheet->setCellValue('G' . $rowNum, $totalIzin);
            $sheet->setCellValue('H' . $rowNum, $totalAlpa);
            $sheet->setCellValue('I' . $rowNum, $totalPertemuan);
            $sheet->setCellValue('J' . $rowNum, $percentage . '%');

            $rowNum++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'rekap_absensi_' . str_replace(' ', '_', strtolower($learningModule->title)) . '_' . ($selectedSemester ? str_replace(' ', '_', strtolower($selectedSemester->semester)) : 'all') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
