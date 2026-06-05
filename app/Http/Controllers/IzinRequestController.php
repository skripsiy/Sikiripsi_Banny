<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\IzinRequest;
use App\Models\LearningModuleAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IzinRequestController extends Controller
{
    private function authorizeMuridModule(LearningModule $learningModule)
    {
        $murid = auth()->user()->murid;
        if (!$murid) {
            abort(403, 'Profil Murid tidak ditemukan.');
        }

        $classroom = $murid->classroom;
        if (!$classroom || !$classroom->is_active) {
            abort(403, 'Kelas Anda tidak aktif atau tidak ditemukan.');
        }

        $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran || $learningModule->tahun_ajaran_id !== $activeTahunAjaran->id || $classroom->tahun_ajaran_id !== $activeTahunAjaran->academic_year_id) {
            abort(403, 'Aksi tidak diizinkan. Modul tidak sesuai dengan tahun ajaran kelas Anda.');
        }

        $subject = $learningModule->subject;
        if (!$subject || !$subject->is_active) {
            abort(403, 'Mata pelajaran tidak aktif atau tidak ditemukan.');
        }

        if ($subject->jurusan_id !== null && $subject->jurusan_id !== $classroom->jurusan_id) {
            abort(403, 'Aksi tidak diizinkan. Modul tidak sesuai dengan jurusan kelas Anda.');
        }
    }

    public function index(LearningModule $learningModule)
    {
        $this->authorizeMuridModule($learningModule);

        $murid = auth()->user()->murid;
        $learningModule->load('subject');

        $izinRequests = IzinRequest::where('learning_module_id', $learningModule->id)
            ->where('murid_id', $murid->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('murid.learning_modules.izin.index', compact('learningModule', 'izinRequests'));
    }

    public function create(LearningModule $learningModule)
    {
        $this->authorizeMuridModule($learningModule);
        $learningModule->load('subject');

        return view('murid.learning_modules.izin.create', compact('learningModule'));
    }

    public function store(Request $request, LearningModule $learningModule)
    {
        $this->authorizeMuridModule($learningModule);

        $murid = auth()->user()->murid;

        $request->validate([
            'date' => ['required', 'date'],
            'jenis_izin' => ['required', 'in:sakit,izin'],
            'alasan' => ['required', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        // Check if there is already a permission request for this date
        $existing = IzinRequest::where('learning_module_id', $learningModule->id)
            ->where('murid_id', $murid->id)
            ->where('date', $request->date)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah mengajukan perizinan untuk tanggal ini.');
        }

        $filePath = $request->file('file')->store('learning_modules/izin', 'public');

        IzinRequest::create([
            'learning_module_id' => $learningModule->id,
            'murid_id' => $murid->id,
            'date' => $request->date,
            'jenis_izin' => $request->jenis_izin,
            'alasan' => $request->alasan,
            'bukti_file_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('murid.learning-modules.izin.index', $learningModule->id)
            ->with('status', 'Permohonan izin berhasil dikirim.');
    }

    // GURU SIDE
    public function listPending(LearningModule $learningModule)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $learningModule->load('subject');

        $izinRequests = IzinRequest::where('learning_module_id', $learningModule->id)
            ->with('murid.user', 'murid.classroom')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.learning_modules.izin.index', compact('learningModule', 'izinRequests'));
    }

    public function approve(LearningModule $learningModule, IzinRequest $izinRequest)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $izinRequest->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($izinRequest->status !== 'pending') {
            return back()->with('error', 'Status permohonan izin ini sudah diproses.');
        }

        $izinRequest->update([
            'status' => 'approved',
        ]);

        // Insert or update attendance record
        LearningModuleAbsensi::updateOrCreate(
            [
                'learning_module_id' => $izinRequest->learning_module_id,
                'murid_id' => $izinRequest->murid_id,
                'date' => $izinRequest->date->format('Y-m-d'),
            ],
            [
                'status' => $izinRequest->jenis_izin,
            ]
        );

        return back()->with('status', 'Permohonan izin berhasil disetujui.');
    }

    public function reject(Request $request, LearningModule $learningModule, IzinRequest $izinRequest)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $learningModule->guru_id !== $guru->id || $izinRequest->learning_module_id !== $learningModule->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($izinRequest->status !== 'pending') {
            return back()->with('error', 'Status permohonan izin ini sudah diproses.');
        }

        $request->validate([
            'catatan_guru' => ['required', 'string', 'max:500'],
        ]);

        $izinRequest->update([
            'status' => 'rejected',
            'catatan_guru' => $request->catatan_guru,
        ]);

        return back()->with('status', 'Permohonan izin berhasil ditolak.');
    }
}
