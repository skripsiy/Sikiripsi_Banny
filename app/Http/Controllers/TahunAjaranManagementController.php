<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranManagementController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::latest()->get();
        return view('admin.manage.tahun_ajarans.index', compact('tahunAjarans'));
    }

    public function create()
    {
        return view('admin.manage.tahun_ajarans.create');
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
                Rule::unique('academic_years')->whereNull('deleted_at'),
            ],
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Tahun Ajaran ini sudah terdaftar.',
        ]);

        $ay = \App\Models\AcademicYear::withTrashed()
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->first();

        if ($ay) {
            if ($ay->trashed()) {
                $ay->restore();
            }
            $ay->update(['is_active' => true]);
            $ay->tahunAjarans()->withTrashed()->restore();
        } else {
            $ay = \App\Models\AcademicYear::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        }

        // Now find or create ganjil semester
        $ganjil = $ay->tahunAjarans()->where('semester', 'ganjil')->first();
        if ($ganjil) {
            $ganjil->update([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        } else {
            TahunAjaran::create([
                'academic_year_id' => $ay->id,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => 'ganjil',
                'is_active' => true,
            ]);
        }

        // Now find or create genap semester
        $genap = $ay->tahunAjarans()->where('semester', 'genap')->first();
        if ($genap) {
            $genap->update([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => true,
            ]);
        } else {
            TahunAjaran::create([
                'academic_year_id' => $ay->id,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => 'genap',
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil ditambahkan beserta semester Ganjil & Genap.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.manage.tahun_ajarans.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
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
                Rule::unique('academic_years')->whereNull('deleted_at')->ignore($tahunAjaran->academic_year_id),
            ],
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Tahun Ajaran ini sudah terdaftar.',
        ]);

        $ay = $tahunAjaran->academicYear;
        if ($ay) {
            $ay->update([
                'tahun_ajaran' => $request->tahun_ajaran,
            ]);
            $ay->tahunAjarans()->update(['tahun_ajaran' => $request->tahun_ajaran]);
        }

        $tahunAjaran->update([
            'semester' => $request->semester,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $ay = $tahunAjaran->academicYear;
        $tahunAjaran->delete();

        if ($ay && $ay->tahunAjarans()->count() === 0) {
            $ay->delete();
        }

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil dihapus.');
    }
}
