<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClassroomMataPelajaranImport;

class ClassroomMataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::with(['jurusan', 'tahunAkademik', 'mataPelajarans'])
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajarans = MataPelajaran::where('is_active', true)
            ->orderBy('nama_pelajaran')
            ->get();

        return view('admin.manage.kurikulum_kelas.index', compact('classrooms', 'mataPelajarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'mata_pelajaran_ids' => ['required', 'array', 'min:1'],
            'mata_pelajaran_ids.*' => ['exists:mata_pelajarans,id'],
        ], [
            'classroom_id.required' => 'Kelas wajib dipilih.',
            'classroom_id.exists' => 'Kelas tidak valid.',
            'mata_pelajaran_ids.required' => 'Minimal satu mata pelajaran wajib dipilih.',
            'mata_pelajaran_ids.min' => 'Minimal satu mata pelajaran wajib dipilih.',
            'mata_pelajaran_ids.*.exists' => 'Mata pelajaran tidak valid.',
        ]);

        $classroom = Classroom::findOrFail($request->classroom_id);
        $classroom->mataPelajarans()->sync($request->mata_pelajaran_ids);

        return redirect()->route('admin.manage.kurikulum-kelas.index')
            ->with('status', "Kurikulum mata pelajaran untuk kelas {$classroom->nama_kelas} berhasil diperbarui.");
    }

    public function update(Request $request, Classroom $kurikulum_kela)
    {
        $request->validate([
            'mata_pelajaran_ids' => ['nullable', 'array'],
            'mata_pelajaran_ids.*' => ['exists:mata_pelajarans,id'],
        ], [
            'mata_pelajaran_ids.*.exists' => 'Mata pelajaran tidak valid.',
        ]);

        $kurikulum_kela->mataPelajarans()->sync($request->mata_pelajaran_ids ?? []);

        return redirect()->route('admin.manage.kurikulum-kelas.index')
            ->with('status', "Kurikulum mata pelajaran untuk kelas {$kurikulum_kela->nama_kelas} berhasil diperbarui.");
    }

    public function destroy(Classroom $kurikulum_kela)
    {
        $kurikulum_kela->mataPelajarans()->detach();

        return redirect()->route('admin.manage.kurikulum-kelas.index')
            ->with('status', "Seluruh penugasan mata pelajaran untuk kelas {$kurikulum_kela->nama_kelas} berhasil dikosongkan.");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Column Headings
        $sheet->setCellValue('A1', 'nama_kelas');
        $sheet->setCellValue('B1', 'kode_pelajaran');

        // Sample Data Row
        $sheet->setCellValue('A2', 'X RPL 1');
        $sheet->setCellValue('B2', 'WEB10');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_kurikulum_kelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            Excel::import(new ClassroomMataPelajaranImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.manage.kurikulum-kelas.index')
                ->with('import_errors', $errors);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $errors[] = $msg;
                }
            }
            return redirect()->route('admin.manage.kurikulum-kelas.index')
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('admin.manage.kurikulum-kelas.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('admin.manage.kurikulum-kelas.index')
            ->with('status', 'Data kurikulum mata pelajaran kelas berhasil diimpor.');
    }
}
