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
        if ($request->filled('tahun_ajaran') && (!$request->filled('tahun_awal') || !$request->filled('tahun_akhir'))) {
            $parts = explode('/', $request->tahun_ajaran);
            if (count($parts) === 2) {
                $request->merge([
                    'tahun_awal' => $parts[0],
                    'tahun_akhir' => $parts[1],
                ]);
            }
        }

        $currentYear = (int) date('Y');

        $request->validate([
            'tahun_awal' => [
                'required',
                'numeric',
                'digits:4',
                'gte:' . $currentYear,
            ],
            'tahun_akhir' => [
                'required',
                'numeric',
                'digits:4',
                function ($attribute, $value, $fail) use ($request) {
                    if ((int)$value !== (int)$request->tahun_awal + 1) {
                        $fail('Tahun akhir harus tepat 1 tahun setelah tahun awal (contoh: ' . $request->tahun_awal . '/' . ((int)$request->tahun_awal + 1) . ').');
                    }
                },
            ],
        ], [
            'tahun_awal.required' => 'Tahun awal wajib diisi.',
            'tahun_awal.numeric' => 'Tahun awal harus berupa angka.',
            'tahun_awal.digits' => 'Tahun awal harus tepat 4 digit angka.',
            'tahun_awal.gte' => 'Tahun awal tidak boleh di masa lalu (minimal tahun ' . $currentYear . ').',
            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.numeric' => 'Tahun akhir harus berupa angka.',
            'tahun_akhir.digits' => 'Tahun akhir harus tepat 4 digit angka.',
        ]);

        $tahunAjaran = $request->tahun_awal . '/' . $request->tahun_akhir;

        $existsActive = TahunAkademik::where('tahun_ajaran', $tahunAjaran)
            ->whereNull('deleted_at')
            ->exists();

        if ($existsActive) {
            return back()->withErrors(['tahun_awal' => 'Tahun Ajaran ini sudah terdaftar.'])->withInput();
        }

        $ay = TahunAkademik::withTrashed()
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        if ($ay) {
            if ($ay->trashed()) {
                $ay->restore();
            }
            $ay->update(['is_active' => true]);
        } else {
            $ay = TahunAkademik::create([
                'tahun_ajaran' => $tahunAjaran,
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
        if ($request->filled('tahun_ajaran') && (!$request->filled('tahun_awal') || !$request->filled('tahun_akhir'))) {
            $parts = explode('/', $request->tahun_ajaran);
            if (count($parts) === 2) {
                $request->merge([
                    'tahun_awal' => $parts[0],
                    'tahun_akhir' => $parts[1],
                ]);
            }
        }

        $currentYear = (int) date('Y');

        $request->validate([
            'tahun_awal' => [
                'required',
                'numeric',
                'digits:4',
                'gte:' . $currentYear,
            ],
            'tahun_akhir' => [
                'required',
                'numeric',
                'digits:4',
                function ($attribute, $value, $fail) use ($request) {
                    if ((int)$value !== (int)$request->tahun_awal + 1) {
                        $fail('Tahun akhir harus tepat 1 tahun setelah tahun awal (contoh: ' . $request->tahun_awal . '/' . ((int)$request->tahun_awal + 1) . ').');
                    }
                },
            ],
            'is_active' => ['required', 'boolean'],
        ], [
            'tahun_awal.required' => 'Tahun awal wajib diisi.',
            'tahun_awal.numeric' => 'Tahun awal harus berupa angka.',
            'tahun_awal.digits' => 'Tahun awal harus tepat 4 digit angka.',
            'tahun_awal.gte' => 'Tahun awal tidak boleh di masa lalu (minimal tahun ' . $currentYear . ').',
            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.numeric' => 'Tahun akhir harus berupa angka.',
            'tahun_akhir.digits' => 'Tahun akhir harus tepat 4 digit angka.',
        ]);

        $tahunAjaran = $request->tahun_awal . '/' . $request->tahun_akhir;

        $existsOther = TahunAkademik::where('tahun_ajaran', $tahunAjaran)
            ->where('id', '!=', $tahunAkademik->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($existsOther) {
            return back()->withErrors(['tahun_awal' => 'Tahun Ajaran ini sudah terdaftar.'])->withInput();
        }

        $tahunAkademik->update([
            'tahun_ajaran' => $tahunAjaran,
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
