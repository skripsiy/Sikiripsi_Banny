<?php

namespace App\Http\Controllers;

use App\Models\SubjectGuru;
use App\Models\Subject;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenugasanGuruController extends Controller
{
    public function index()
    {
        $assignments = SubjectGuru::with(['subject.jurusan', 'guru.user'])->latest()->get();
        $subjects = Subject::where('is_active', true)->orderBy('nama_pelajaran')->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();

        return view('admin.manage.subjects.assign_index', compact('assignments', 'subjects', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('subject_guru')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })
            ],
            'guru_id' => ['required', 'exists:gurus,id'],
        ], [
            'subject_id.unique' => 'Guru ini sudah ditugaskan untuk mata pelajaran yang dipilih.',
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
        ]);

        SubjectGuru::create([
            'subject_id' => $request->subject_id,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $penugasanGuru = SubjectGuru::findOrFail($id);

        $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('subject_guru')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })->ignore($penugasanGuru->id)
            ],
            'guru_id' => ['required', 'exists:gurus,id'],
        ], [
            'subject_id.unique' => 'Guru ini sudah ditugaskan untuk mata pelajaran yang dipilih.',
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
        ]);

        $penugasanGuru->update([
            'subject_id' => $request->subject_id,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penugasanGuru = SubjectGuru::findOrFail($id);
        $penugasanGuru->delete();

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil dihapus.');
    }
}
