<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectManagementController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('jurusan')->latest()->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        return view('admin.manage.subjects.index', compact('subjects', 'jurusans'));
    }

    public function create()
    {
        return view('admin.manage.subjects.create');
    }

    public function store(Request $request)
    {
        if ($request->has('kode_pelajaran')) {
            $request->merge([
                'kode_pelajaran' => strtoupper($request->kode_pelajaran)
            ]);
        }

        $request->validate([
            'kode_pelajaran' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects')->whereNull('deleted_at'),
            ],
            'nama_pelajaran' => ['required', 'string', 'max:100'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
        ], [
            'kode_pelajaran.unique' => 'Kode mata pelajaran ini sudah terdaftar.',
            'kode_pelajaran.max' => 'Kode mata pelajaran maksimal 20 karakter.',
            'nama_pelajaran.required' => 'Nama mata pelajaran wajib diisi.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        Subject::create([
            'kode_pelajaran' => strtoupper($request->kode_pelajaran),
            'nama_pelajaran' => $request->nama_pelajaran,
            'jurusan_id' => $request->jurusan_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.manage.subjects.index')
            ->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.manage.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        if ($request->has('kode_pelajaran')) {
            $request->merge([
                'kode_pelajaran' => strtoupper($request->kode_pelajaran)
            ]);
        }

        $request->validate([
            'kode_pelajaran' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects')->whereNull('deleted_at')->ignore($subject->id),
            ],
            'nama_pelajaran' => ['required', 'string', 'max:100'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
            'is_active' => ['required', 'boolean'],
        ], [
            'kode_pelajaran.unique' => 'Kode mata pelajaran ini sudah terdaftar.',
            'kode_pelajaran.max' => 'Kode mata pelajaran maksimal 20 karakter.',
            'nama_pelajaran.required' => 'Nama mata pelajaran wajib diisi.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        $subject->update([
            'kode_pelajaran' => strtoupper($request->kode_pelajaran),
            'nama_pelajaran' => $request->nama_pelajaran,
            'jurusan_id' => $request->jurusan_id,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.subjects.index')
            ->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.manage.subjects.index')
            ->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}
