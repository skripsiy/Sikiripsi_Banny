<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GurusImport;

class GuruManagementController extends Controller
{
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Column Headings
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'nuptk');

        // Sample Data Row
        $sheet->setCellValue('A2', 'Budi Handoko, S.Pd.');
        $sheet->setCellValue('B2', 'budi@smkn1jakarta.sch.id');
        $sheet->setCellValue('C2', '9876543210987654');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_guru.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            Excel::import(new GurusImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.manage.gurus.index')
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('admin.manage.gurus.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('admin.manage.gurus.index')
            ->with('status', 'Data guru berhasil diimpor.');
    }
    public function index()
    {
        $gurus = User::where('role', 'guru')->with('guru')->latest()->get();
        return view('admin.manage.gurus.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.manage.gurus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nuptk' => ['required', 'numeric', 'digits:16'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'must_change_password' => true,
        ]);

        Guru::create([
            'user_id' => $user->id,
            'nuptk' => $request->nuptk,
            'admin_id' => auth()->user()->admin?->id,
        ]);

        return redirect()->route('admin.manage.gurus.index')
            ->with('status', 'Guru account created successfully.');
    }

    public function edit(User $guru)
    {
        $guru->load('guru');
        return view('admin.manage.gurus.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$guru->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nuptk' => ['required', 'numeric', 'digits:16'],
        ]);

        $guru->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $guru->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $guru->guru()->updateOrCreate(
            ['user_id' => $guru->id],
            [
                'nuptk' => $request->nuptk,
            ]
        );

        return redirect()->route('admin.manage.gurus.index')
            ->with('status', 'Guru account updated successfully.');
    }

    public function destroy(User $guru)
    {
        $guru->delete(); // This will cascade delete their profile

        return redirect()->route('admin.manage.gurus.index')
            ->with('status', 'Guru account deleted successfully.');
    }
}
