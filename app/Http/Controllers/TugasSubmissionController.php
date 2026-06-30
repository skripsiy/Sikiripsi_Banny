<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use App\Models\LearningModuleTugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TugasSubmissionController extends Controller
{
    public function store(Request $request, LearningModule $learningModule, LearningModuleTugas $tuga)
    {
        $murid = auth()->user()->murid;
        if (!$murid) {
            abort(403, 'Aksi tidak diizinkan. Hanya murid yang dapat mengumpulkan tugas.');
        }

        // Validate that this assignment belongs to this module
        if ($tuga->learning_module_id !== $learningModule->id) {
            abort(404, 'Tugas tidak ditemukan di modul ini.');
        }

        // Validate due date
        if ($tuga->due_date && Carbon::parse($tuga->due_date)->isPast()) {
            return back()->with('error', 'Batas waktu pengumpulan tugas sudah terlewat.');
        }

        // Validate if already submitted
        $existingSubmission = TugasSubmission::where('learning_module_tugas_id', $tuga->id)
            ->where('murid_id', $murid->id)
            ->first();

        if ($existingSubmission) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        $request->validate([
            'file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,ppt,pptx'],
            'catatan_murid' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
            'file.mimes' => 'Format file harus berupa PDF, DOC, DOCX, PPT, atau PPTX.',
            'catatan_murid.max' => 'Catatan tambahan maksimal 1000 karakter.',
        ]);

        // Pastikan minimal file atau catatan diisi
        if (!$request->hasFile('file') && !$request->filled('catatan_murid')) {
            return back()->withErrors(['file' => 'Harap unggah file tugas atau isi catatan jawaban.'])->withInput();
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_modules/submissions', 'public');
        }

        TugasSubmission::create([
            'learning_module_tugas_id' => $tuga->id,
            'murid_id' => $murid->id,
            'file_path' => $filePath,
            'catatan_murid' => $request->catatan_murid,
            'submitted_at' => now(),
        ]);

        return back()->with('status', 'Tugas berhasil dikumpulkan.');
    }
}
