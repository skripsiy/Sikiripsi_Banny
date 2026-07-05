<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JurusanManagementController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::latest()->get();
        return view('admin.manage.jurusans.index', compact('jurusans'));
    }

    public function create()
    {
        return view('admin.manage.jurusans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('jurusans')->whereNull('deleted_at'),
            ],
            'nama_jurusan' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'kode_jurusan.unique' => 'Kode jurusan ini sudah terdaftar.',
            'kode_jurusan.max' => 'Kode jurusan maksimal 20 karakter.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        Jurusan::create([
            'kode_jurusan' => strtoupper($request->kode_jurusan),
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
            'admin_id' => auth()->user()->admin?->id,
        ]);

        return redirect()->route('admin.manage.jurusans.index')
            ->with('status', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.manage.jurusans.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('jurusans')->whereNull('deleted_at')->ignore($jurusan->id),
            ],
            'nama_jurusan' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ], [
            'kode_jurusan.unique' => 'Kode jurusan ini sudah terdaftar.',
            'kode_jurusan.max' => 'Kode jurusan maksimal 20 karakter.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        $jurusan->update([
            'kode_jurusan' => strtoupper($request->kode_jurusan),
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.jurusans.index')
            ->with('status', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();

        return redirect()->route('admin.manage.jurusans.index')
            ->with('status', 'Jurusan berhasil dihapus.');
    }
}
