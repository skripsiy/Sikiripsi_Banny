<?php

namespace App\Http\Controllers;

use App\Models\GuruMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PenugasanGuruImport;

class PenugasanGuruController extends Controller
{
    public function index()
    {
        $assignments = GuruMataPelajaran::with(['mataPelajaran.jurusan', 'guru.user'])->latest()->get();
        $mata_pelajarans = MataPelajaran::where('is_active', true)->orderBy('nama_pelajaran')->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();

        return view('admin.manage.penugasan_guru.index', compact('assignments', 'mata_pelajarans', 'gurus'));
    }

    public function create()
    {
        $mata_pelajarans = MataPelajaran::where('is_active', true)->orderBy('nama_pelajaran')->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();

        return view('admin.manage.penugasan_guru.create', compact('mata_pelajarans', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id',
                Rule::unique('guru_mata_pelajaran')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })
            ],
            'guru_id' => ['required', 'exists:gurus,id'],
        ], [
            'mata_pelajaran_id.unique' => 'Guru ini sudah ditugaskan untuk mata pelajaran yang dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
        ]);

        GuruMataPelajaran::create([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
            'admin_id' => auth()->user()->admin?->id,
        ]);

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil ditambahkan.');
    }

    public function edit(GuruMataPelajaran $penugasanGuru)
    {
        $mata_pelajarans = MataPelajaran::where('is_active', true)->orderBy('nama_pelajaran')->get();
        $gurus = Guru::with('user')->get()->sortBy(fn($g) => $g->user?->name ?? '')->values();

        return view('admin.manage.penugasan_guru.edit', compact('penugasanGuru', 'mata_pelajarans', 'gurus'));
    }

    public function update(Request $request, GuruMataPelajaran $penugasanGuru)
    {
        $request->validate([
            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id',
                Rule::unique('guru_mata_pelajaran')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id);
                })->ignore($penugasanGuru->id)
            ],
            'guru_id' => ['required', 'exists:gurus,id'],
        ], [
            'mata_pelajaran_id.unique' => 'Guru ini sudah ditugaskan untuk mata pelajaran yang dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
        ]);

        $penugasanGuru->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil diperbarui.');
    }

    public function destroy(GuruMataPelajaran $penugasanGuru)
    {
        $penugasanGuru->delete();

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Penugasan guru berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Column Headings
        $sheet->setCellValue('A1', 'nip');
        $sheet->setCellValue('B1', 'kode_pelajaran');

        // Sample Data Row
        $sheet->setCellValue('A2', '198501012010011002');
        $sheet->setCellValue('B2', 'BIN10');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_penugasan_guru.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            Excel::import(new PenugasanGuruImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.manage.penugasan-guru.index')
                ->with('import_errors', $errors);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $errors[] = $msg;
                }
            }
            return redirect()->route('admin.manage.penugasan-guru.index')
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('admin.manage.penugasan-guru.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('admin.manage.penugasan-guru.index')
            ->with('status', 'Data penugasan guru berhasil diimpor.');
    }
}
