<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterManagementController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('tahunAkademik')->latest()->get();
        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        return view('admin.manage.semesters.index', compact('semesters', 'academicYears'));
    }

    public function create()
    {
        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        return view('admin.manage.semesters.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'semester' => [
                'required',
                'string',
                Rule::in(['ganjil', 'genap']),
                Rule::unique('semesters')
                    ->where('tahun_akademik_id', $request->tahun_akademik_id)
                    ->whereNull('deleted_at')
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'semester.unique' => 'Semester ini sudah terdaftar pada tahun akademik yang dipilih.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh mendahului tanggal mulai.',
        ]);

        $ay = TahunAkademik::findOrFail($request->tahun_akademik_id);

        Semester::create([
            'tahun_akademik_id' => $ay->id,
            'semester' => $request->semester,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
            'admin_id' => auth()->user()->admin?->id,
        ]);

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        $academicYears = TahunAkademik::orderBy('tahun_ajaran', 'desc')->get();
        return view('admin.manage.semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'semester' => [
                'required',
                'string',
                Rule::in(['ganjil', 'genap']),
                Rule::unique('semesters')
                    ->where('tahun_akademik_id', $request->tahun_akademik_id)
                    ->whereNull('deleted_at')
                    ->ignore($semester->id)
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ], [
            'semester.unique' => 'Semester ini sudah terdaftar pada tahun akademik yang dipilih.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh mendahului tanggal mulai.',
        ]);

        $ay = TahunAkademik::findOrFail($request->tahun_akademik_id);

        $semester->update([
            'tahun_akademik_id' => $ay->id,
            'semester' => $request->semester,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil dihapus.');
    }
}
