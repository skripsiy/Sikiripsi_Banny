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
        $semesters = Semester::latest()->get();
        return view('admin.manage.semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('admin.manage.semesters.create');
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
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
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
            $ay->semesters()->withTrashed()->restore();
        } else {
            $ay = TahunAkademik::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        }

        // Now find or create ganjil semester
        $ganjil = $ay->semesters()->where('semester', 'ganjil')->first();
        if ($ganjil) {
            $ganjil->update([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        } else {
            Semester::create([
                'tahun_akademik_id' => $ay->id,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => 'ganjil',
                'is_active' => true,
                'admin_id' => auth()->user()->admin?->id,
            ]);
        }

        // Now find or create genap semester
        $genap = $ay->semesters()->where('semester', 'genap')->first();
        if ($genap) {
            $genap->update([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        } else {
            Semester::create([
                'tahun_akademik_id' => $ay->id,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => 'genap',
                'is_active' => true,
                'admin_id' => auth()->user()->admin?->id,
            ]);
        }

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil ditambahkan beserta semester Ganjil & Genap.');
    }

    public function edit(Semester $semester)
    {
        return view('admin.manage.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
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
                Rule::unique('tahun_akademiks')->whereNull('deleted_at')->ignore($semester->tahun_akademik_id),
            ],
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Tahun Ajaran ini sudah terdaftar.',
        ]);

        $ay = $semester->tahunAkademik;
        if ($ay) {
            $ay->update([
                'tahun_ajaran' => $request->tahun_ajaran,
            ]);
            $ay->semesters()->update(['tahun_ajaran' => $request->tahun_ajaran]);
        }

        $semester->update([
            'semester' => $request->semester,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $ay = $semester->tahunAkademik;
        $semester->delete();

        if ($ay && $ay->semesters()->count() === 0) {
            $ay->delete();
        }

        return redirect()->route('admin.manage.semesters.index')
            ->with('status', 'Semester berhasil dihapus.');
    }
}
