<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomManagementController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with(['jurusan', 'tahunAjaran'])->latest()->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        $tahunAjarans = TahunAjaran::where('is_active', true)->latest()->get();

        return view('admin.manage.classrooms.index', compact('classrooms', 'jurusans', 'tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classrooms')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar pada tahun ajaran tersebut.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak valid.',
        ]);

        Classroom::create([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'jurusan_id' => $request->jurusan_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.manage.classrooms.index')
            ->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classrooms')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                                 ->whereNull('deleted_at');
                })->ignore($classroom->id),
            ],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'is_active' => ['required', 'boolean'],
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar pada tahun ajaran tersebut.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak valid.',
        ]);

        $classroom->update([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'jurusan_id' => $request->jurusan_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
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
