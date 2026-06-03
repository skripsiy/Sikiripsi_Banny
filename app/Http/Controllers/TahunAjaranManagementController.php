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
                Rule::unique('tahun_ajarans')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->semester)
                                 ->whereNull('deleted_at');
                }),
            ],
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Kombinasi Tahun Ajaran dan Semester ini sudah terdaftar.',
        ]);

        TahunAjaran::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil ditambahkan.');
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
                Rule::unique('tahun_ajarans')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->semester)
                                 ->whereNull('deleted_at');
                })->ignore($tahunAjaran->id),
            ],
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026).',
            'tahun_ajaran.size' => 'Tahun ajaran harus tepat 9 karakter.',
            'tahun_ajaran.unique' => 'Kombinasi Tahun Ajaran dan Semester ini sudah terdaftar.',
        ]);

        $tahunAjaran->update([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();

        return redirect()->route('admin.manage.tahun-ajarans.index')
            ->with('status', 'Tahun Ajaran berhasil dihapus.');
    }
}
