<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomManagementController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with(['jurusan', 'tahunAkademik'])->latest()->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        $semesters = TahunAkademik::where('is_active', true)->latest()->get();

        return view('admin.manage.classrooms.index', compact('classrooms', 'jurusans', 'semesters'));
    }

    public function create()
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        $semesters = TahunAkademik::where('is_active', true)->latest()->get();

        return view('admin.manage.classrooms.create', compact('jurusans', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classrooms')->where(function ($query) use ($request) {
                    return $query->where('tahun_akademik_id', $request->tahun_akademik_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar pada tahun akademik tersebut.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'tahun_akademik_id.exists' => 'Tahun akademik yang dipilih tidak valid.',
        ]);

        Classroom::create([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'jurusan_id' => $request->jurusan_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.manage.classrooms.index')
            ->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom)
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        $semesters = TahunAkademik::where('is_active', true)->latest()->get();

        return view('admin.manage.classrooms.edit', compact('classroom', 'jurusans', 'semesters'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classrooms')->where(function ($query) use ($request) {
                    return $query->where('tahun_akademik_id', $request->tahun_akademik_id)
                                 ->whereNull('deleted_at');
                })->ignore($classroom->id),
            ],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tahun_akademik_id' => ['required', 'exists:tahun_akademiks,id'],
            'is_active' => ['required', 'boolean'],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar pada tahun akademik tersebut.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'tahun_akademik_id.exists' => 'Tahun akademik yang dipilih tidak valid.',
        ]);

        $classroom->update([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'jurusan_id' => $request->jurusan_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.classrooms.index')
            ->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->route('admin.manage.classrooms.index')
            ->with('status', 'Kelas berhasil dihapus.');
    }
}
