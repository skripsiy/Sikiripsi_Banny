<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\Subject;
use App\Models\LearningModuleMateri;
use App\Models\LearningModuleTugas;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\LearningModuleAbsensi;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\TahunAjaran;
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

        $semesters = TahunAjaran::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        $academicYears = \App\Models\AcademicYear::orderBy('tahun_ajaran', 'desc')->get();
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        $selectedAcademicYearId = request('academic_year_id', 'all');
        $selectedSemester = request('semester', 'all');

        // Get modules owned by this teacher
        $learningModules = LearningModule::where('guru_id', $guru->id)
            ->when($selectedAcademicYearId && $selectedAcademicYearId !== 'all', function($q) use ($selectedAcademicYearId) {
                return $q->whereIn('tahun_ajaran_id', function($subQuery) use ($selectedAcademicYearId) {
                    $subQuery->select('id')
                             ->from('tahun_ajarans')
                             ->where('academic_year_id', $selectedAcademicYearId);
                });
            })
            ->when($selectedSemester && $selectedSemester !== 'all', function($q) use ($selectedSemester) {
                return $q->whereIn('tahun_ajaran_id', function($subQuery) use ($selectedSemester) {
                    $subQuery->select('id')
                             ->from('tahun_ajarans')
                             ->where('semester', $selectedSemester);
                });
            })
            ->with(['subject', 'tahunAjaran'])
            ->withCount(['materis', 'tugas', 'quizzes', 'ujians', 'absensis'])
            ->latest()
            ->get();

        // Get only subjects assigned to this teacher
        $subjects = $guru->subjects()
            ->where('is_active', true)
            ->orderBy('nama_pelajaran')
            ->get();

        $tahunAjarans = $semesters;
        $selectedTahunAjaranId = $activeTahunAjaran?->id;

        return view('guru.learning_modules.index', compact('learningModules', 'subjects', 'tahunAjarans', 'academicYears', 'selectedAcademicYearId', 'selectedSemester', 'selectedTahunAjaranId'));
    }

    public function store(Request $request)
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,png,jpg,jpeg', 'max:10240'],
        ], [
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'subject_id.exists' => 'Mata pelajaran tidak valid.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
            'description.required' => 'Deskripsi modul wajib diisi.',
            'file.mimes' => 'Format file pendukung tidak didukung.',
            'file.max' => 'Ukuran file pendukung maksimal 10MB.',
        ]);

        // Authorize that the teacher is assigned to this subject
        if (!$guru->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules', 'public');
        }

        LearningModule::create([
            'guru_id' => $guru->id,
            'subject_id' => $request->subject_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
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
            'subject_id' => ['required', 'exists:subjects,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,png,jpg,jpeg', 'max:10240'],
        ], [
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'subject_id.exists' => 'Mata pelajaran tidak valid.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',
            'title.required' => 'Judul modul wajib diisi.',
            'title.max' => 'Judul modul maksimal 255 karakter.',
            'description.required' => 'Deskripsi modul wajib diisi.',
            'file.mimes' => 'Format file pendukung tidak didukung.',
            'file.max' => 'Ukuran file pendukung maksimal 10MB.',
        ]);

        // Authorize that the teacher is assigned to this subject
        if (!$guru->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        $data = [
            'subject_id' => $request->subject_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($learningModule->file_path && Storage::disk('public')->exists($learningModule->file_path)) {
                Storage::disk('public')->delete($learningModule->file_path);
            }
            $data['file_path'] = $request->file('file')->store('learning_modules', 'public');
        }

        $learningModule->update($data);

        return redirect()->route('guru.learning-modules.index')
            ->with('status', 'Modul pembelajaran berhasil diperbarui.');
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

        $learningModule->load('subject');

        // Load all sub-contents counts
        $learningModule->loadCount(['materis', 'tugas', 'quizzes', 'ujians']);

        // Load recent items (latest 10 of each for dashboard timeline)
        $recentMateris = LearningModuleMateri::where('learning_module_id', $learningModule->id)->latest()->limit(10)->get();
        $recentTugas = LearningModuleTugas::where('learning_module_id', $learningModule->id)->latest()->limit(10)->get();
        $recentQuizzes = LearningModuleQuiz::where('learning_module_id', $learningModule->id)->latest()->limit(10)->get();
        $recentUjians = LearningModuleUjian::where('learning_module_id', $learningModule->id)->latest()->limit(10)->get();

        // Get student list based on subject's jurusan and module's academic year
        $jurusanId = $learningModule->subject->jurusan_id;
        $query = Classroom::where('tahun_ajaran_id', $learningModule->tahun_ajaran_id)
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
            'activities'
        ));
    }

    public function absensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');

        // Get student list based on subject's jurusan and module's academic year
        $jurusanId = $learningModule->subject->jurusan_id;
        $query = Classroom::where('tahun_ajaran_id', $learningModule->tahun_ajaran_id)
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

        // Selected date for attendance
        $date = request('date', date('Y-m-d'));

        // Load existing attendance
        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
            ->where('date', $date)
            ->get()
            ->keyBy('murid_id');

        return view('guru.learning_modules.absensi.index', compact(
            'learningModule',
            'murids',
            'absensis',
            'date'
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
            'status' => ['required', 'array'],
            'status.*' => ['in:hadir,sakit,izin,alpa'],
        ]);

        $date = $request->date;

        foreach ($request->status as $muridId => $status) {
            $oldAbsensi = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)
                ->where('murid_id', $muridId)
                ->where('date', $date)
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
                ],
                [
                    'status' => $newStatus,
                ]
            );

            if ($shouldNotify) {
                $murid = $absensi->murid;
                if ($murid && $murid->no_telepon_orang_tua) {
                    $subjectName = $learningModule->subject->nama_pelajaran;
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
        ])->with('status', 'Absensi berhasil disimpan.');
    }

    public function rekapAbsensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');

        $jurusanId = $learningModule->subject->jurusan_id;
        $query = Classroom::where('tahun_ajaran_id', $learningModule->tahun_ajaran_id)
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

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)->get();

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

        return view('guru.learning_modules.absensi.rekap', compact('learningModule', 'rekap'));
    }

    public function exportAbsensi(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');

        $jurusanId = $learningModule->subject->jurusan_id;
        $query = Classroom::where('tahun_ajaran_id', $learningModule->tahun_ajaran_id)
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

        $absensis = LearningModuleAbsensi::where('learning_module_id', $learningModule->id)->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'REKAPITULASI ABSENSI SISWA');
        $sheet->setCellValue('A2', 'Modul: ' . $learningModule->title);
        $sheet->setCellValue('A3', 'Mata Pelajaran: ' . $learningModule->subject->nama_pelajaran);
        $sheet->setCellValue('A4', 'Tahun Ajaran: ' . ($learningModule->tahunAjaran->tahun_ajaran ?? '-'));
        $sheet->setCellValue('A5', 'Dicetak pada: ' . date('d F Y H:i'));

        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'Nama Siswa');
        $sheet->setCellValue('C7', 'NISN');
        $sheet->setCellValue('D7', 'Kelas');
        $sheet->setCellValue('E7', 'Hadir');
        $sheet->setCellValue('F7', 'Sakit');
        $sheet->setCellValue('G7', 'Izin');
        $sheet->setCellValue('H7', 'Alpa');
        $sheet->setCellValue('I7', 'Total Pertemuan');
        $sheet->setCellValue('J7', 'Persentase Kehadiran (%)');

        $rowNum = 8;
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
        $fileName = 'rekap_absensi_' . str_replace(' ', '_', strtolower($learningModule->title)) . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
