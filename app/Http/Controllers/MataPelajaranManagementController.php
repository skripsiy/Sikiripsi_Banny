<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Jurusan;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranManagementController extends Controller
{
    public function index()
    {
        $mata_pelajarans = MataPelajaran::with('jurusan')->latest()->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama_jurusan')->get();
        return view('admin.manage.mata_pelajarans.index', compact('mata_pelajarans', 'jurusans'));
    }

    public function create()
    {
        return view('admin.manage.mata_pelajarans.create');
    }

    public function store(Request $request)
    {
        if (!$request->filled('kode_pelajaran') || $request->kode_pelajaran === 'DIBUAT OTOMATIS') {
            // Generate a unique 6-character random uppercase alphanumeric string
            do {
                $kode = strtoupper(\Illuminate\Support\Str::random(6));
            } while (MataPelajaran::where('kode_pelajaran', $kode)->whereNull('deleted_at')->exists());

            $request->merge([
                'kode_pelajaran' => $kode
            ]);
        } else {
            $request->merge([
                'kode_pelajaran' => strtoupper($request->kode_pelajaran)
            ]);
        }

        $request->validate([
            'kode_pelajaran' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_pelajarans')->whereNull('deleted_at'),
            ],
            'nama_pelajaran' => ['required', 'string', 'max:100'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
        ], [
            'kode_pelajaran.unique' => 'Kode mata pelajaran ini sudah terdaftar.',
            'kode_pelajaran.max' => 'Kode mata pelajaran maksimal 20 karakter.',
            'nama_pelajaran.required' => 'Nama mata pelajaran wajib diisi.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        MataPelajaran::create([
            'kode_pelajaran' => $request->kode_pelajaran,
            'nama_pelajaran' => $request->nama_pelajaran,
            'jurusan_id' => $request->jurusan_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.manage.mata_pelajarans.index')
            ->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.manage.mata_pelajarans.edit', compact('mataPelajaran'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
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
                Rule::unique('mata_pelajarans')->whereNull('deleted_at')->ignore($mataPelajaran->id),
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

        $mataPelajaran->update([
            'kode_pelajaran' => strtoupper($request->kode_pelajaran),
            'nama_pelajaran' => $request->nama_pelajaran,
            'jurusan_id' => $request->jurusan_id,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.mata_pelajarans.index')
            ->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('admin.manage.mata_pelajarans.index')
            ->with('status', 'Mata pelajaran berhasil dihapus.');
    }

    public function assignTeachers(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'guru_ids' => ['nullable', 'array'],
            'guru_ids.*' => ['exists:gurus,id'],
        ], [
            'guru_ids.*.exists' => 'Guru yang dipilih tidak valid.',
        ]);

        $mataPelajaran->gurus()->sync($request->input('guru_ids', []));

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Guru pengampu berhasil diperbarui.');
    }

    public function penugasanGuru()
    {
        $mata_pelajarans = MataPelajaran::with(['jurusan', 'gurus.user'])->latest()->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();
        return view('admin.manage.mata_pelajarans.assign_index', compact('mata_pelajarans', 'gurus'));
    }
}
