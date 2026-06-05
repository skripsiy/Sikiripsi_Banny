<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use App\Models\BankSoalOption;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $subjects = $guru->subjects()->where('is_active', true)->orderBy('nama_pelajaran')->get();
        $selectedSubjectId = $request->input('subject_id');

        $soals = BankSoal::where('guru_id', $guru->id)
            ->when($selectedSubjectId, function($q) use ($selectedSubjectId) {
                return $q->where('subject_id', $selectedSubjectId);
            })
            ->with(['subject', 'options'])
            ->latest()
            ->paginate(15);

        return view('guru.bank_soal.index', compact('subjects', 'soals', 'selectedSubjectId'));
    }

    public function store(Request $request)
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            abort(403, 'Profil Guru tidak ditemukan.');
        }

        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'tipe' => ['required', 'in:pg,essay'],
            'pertanyaan' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'max:2048'],
            'pembahasan' => ['nullable', 'string'],
            'teks_opsi' => ['required_if:tipe,pg', 'array'],
            'teks_opsi.A' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.B' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.C' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.D' => ['required_if:tipe,pg', 'string'],
            'correct_option' => ['required_if:tipe,pg', 'in:A,B,C,D'],
        ], [
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'tipe.required' => 'Tipe soal wajib dipilih.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'teks_opsi.A.required_if' => 'Opsi A wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.B.required_if' => 'Opsi B wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.C.required_if' => 'Opsi C wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.D.required_if' => 'Opsi D wajib diisi untuk soal Pilihan Ganda.',
            'correct_option.required_if' => 'Jawaban benar wajib dipilih untuk soal Pilihan Ganda.',
        ]);

        // Authorize that the teacher is assigned to this subject
        if (!$guru->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('bank_soal_images', 'public');
        }

        $soal = BankSoal::create([
            'subject_id' => $request->subject_id,
            'guru_id' => $guru->id,
            'tipe' => $request->tipe,
            'pertanyaan' => $request->pertanyaan,
            'gambar_path' => $gambarPath,
            'pembahasan' => $request->pembahasan,
        ]);

        if ($request->tipe === 'pg') {
            foreach (['A', 'B', 'C', 'D'] as $label) {
                BankSoalOption::create([
                    'bank_soal_id' => $soal->id,
                    'label' => $label,
                    'teks_opsi' => $request->input("teks_opsi.{$label}"),
                    'is_correct' => $request->correct_option === $label,
                ]);
            }
        }

        return redirect()->route('guru.bank-soal.index', ['subject_id' => $request->subject_id])
            ->with('status', 'Soal berhasil ditambahkan ke Bank Soal.');
    }

    public function update(Request $request, BankSoal $bankSoal)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $bankSoal->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'pertanyaan' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'max:2048'],
            'pembahasan' => ['nullable', 'string'],
            'teks_opsi' => ['required_if:tipe,pg', 'array'],
            'teks_opsi.A' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.B' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.C' => ['required_if:tipe,pg', 'string'],
            'teks_opsi.D' => ['required_if:tipe,pg', 'string'],
            'correct_option' => ['required_if:tipe,pg', 'in:A,B,C,D'],
        ], [
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'teks_opsi.A.required_if' => 'Opsi A wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.B.required_if' => 'Opsi B wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.C.required_if' => 'Opsi C wajib diisi untuk soal Pilihan Ganda.',
            'teks_opsi.D.required_if' => 'Opsi D wajib diisi untuk soal Pilihan Ganda.',
            'correct_option.required_if' => 'Jawaban benar wajib dipilih untuk soal Pilihan Ganda.',
        ]);

        if (!$guru->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return redirect()->back()
                ->withErrors(['subject_id' => 'Mata pelajaran yang dipilih tidak ditugaskan kepada Anda.'])
                ->withInput();
        }

        $gambarPath = $bankSoal->gambar_path;
        if ($request->hasFile('gambar')) {
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }
            $gambarPath = $request->file('gambar')->store('bank_soal_images', 'public');
        }

        $bankSoal->update([
            'subject_id' => $request->subject_id,
            'pertanyaan' => $request->pertanyaan,
            'gambar_path' => $gambarPath,
            'pembahasan' => $request->pembahasan,
        ]);

        if ($bankSoal->tipe === 'pg') {
            // Delete old options and recreate
            $bankSoal->options()->delete();
            foreach (['A', 'B', 'C', 'D'] as $label) {
                BankSoalOption::create([
                    'bank_soal_id' => $bankSoal->id,
                    'label' => $label,
                    'teks_opsi' => $request->input("teks_opsi.{$label}"),
                    'is_correct' => $request->correct_option === $label,
                ]);
            }
        }

        return redirect()->route('guru.bank-soal.index', ['subject_id' => $request->subject_id])
            ->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroy(BankSoal $bankSoal)
    {
        $guru = auth()->user()->guru;
        if (!$guru || $bankSoal->guru_id !== $guru->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Keep the image file for potential softDeletes restoration, or delete it if we want full delete.
        // Let's keep it for SoftDeletes support.
        $bankSoal->delete();

        return redirect()->back()->with('status', 'Soal berhasil dihapus.');
    }
}
