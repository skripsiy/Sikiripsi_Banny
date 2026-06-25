<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAkademikManagementController extends Controller
{
    public function index()
    {
        $academicYears = TahunAkademik::latest()->get();
        return view('admin.manage.tahun_akademiks.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.manage.tahun_akademiks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'string',
                'size:9',
                'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('/', $value);
                    if (count($parts) === 2) {
                        $year1 = (int) $parts[0];
                        $year2 = (int) $parts[1];
                        if ($year2 !== $year1 + 1) {
                            $fail('Format tahun ajaran tidak valid. Tahun kedua harus tepat 1 tahun setelah tahun pertama (misal: 2025/2026).');
                        }
                    }
                },
                Rule::unique('tahun_akademiks')->whereNull('deleted_at'),
            ],
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Tahun Ajaran ini sudah terdaftar.',
        ]);

        $ay = TahunAkademik::withTrashed()
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->first();

        if ($ay) {
            if ($ay->trashed()) {
                $ay->restore();
            }
            $ay->update(['is_active' => true]);
        } else {
            $ay = TahunAkademik::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.manage.tahun_akademiks.index')
            ->with('status', 'Tahun Akademik berhasil ditambahkan.');
    }

    public function edit(TahunAkademik $tahunAkademik)
    {
        return view('admin.manage.tahun_akademiks.edit', compact('tahunAkademik'));
    }

    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'string',
                'size:9',
                'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('/', $value);
                    if (count($parts) === 2) {
                        $year1 = (int) $parts[0];
                        $year2 = (int) $parts[1];
                        if ($year2 !== $year1 + 1) {
                            $fail('Format tahun ajaran tidak valid. Tahun kedua harus tepat 1 tahun setelah tahun pertama (misal: 2025/2026).');
                        }
                    }
                },
                Rule::unique('tahun_akademiks')->whereNull('deleted_at')->ignore($tahunAkademik->id),
            ],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Tahun Ajaran ini sudah terdaftar.',
        ]);

        $tahunAkademik->update([
            'tahun_ajaran' => $request->tahun_ajaran,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.tahun_akademiks.index')
            ->with('status', 'Tahun Akademik berhasil diperbarui.');
    }

    public function destroy(TahunAkademik $tahunAkademik)
    {
        // Delete semesters under this year first (soft delete)
        $tahunAkademik->semesters()->delete();
        $tahunAkademik->delete();

        return redirect()->route('admin.manage.tahun_akademiks.index')
            ->with('status', 'Tahun Akademik beserta semesternya berhasil dihapus.');
    }
}
