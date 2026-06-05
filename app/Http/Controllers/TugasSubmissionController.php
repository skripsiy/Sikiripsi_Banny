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
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,png,jpg,jpeg'],
            'catatan_murid' => ['nullable', 'string', 'max:1000'],
        ]);

        $filePath = $request->file('file')->store('learning_modules/submissions', 'public');

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
