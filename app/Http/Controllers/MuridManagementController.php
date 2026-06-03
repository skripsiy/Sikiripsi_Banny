<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MuridsImport;

class MuridManagementController extends Controller
{
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Column Headings
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'nisn');
        $sheet->setCellValue('D1', 'class_room');

        // Sample Data Row
        $sheet->setCellValue('A2', 'Aji Pratama');
        $sheet->setCellValue('B2', 'aji@stovia.sch.id');
        $sheet->setCellValue('C2', '0054321098');
        $sheet->setCellValue('D2', 'XII RPL 1');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_murid.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            Excel::import(new MuridsImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.manage.murids.index')
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('admin.manage.murids.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('admin.manage.murids.index')
            ->with('status', 'Data murid berhasil diimpor.');
    }
    public function index()
    {
        $murids = User::where('role', 'murid')->with('murid')->latest()->get();
        return view('admin.manage.murids.index', compact('murids'));
    }

    public function create()
    {
        return view('admin.manage.murids.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nisn' => ['required', 'numeric', 'digits:10'],
            'class_room' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'murid',
        ]);

        Murid::create([
            'user_id' => $user->id,
            'nisn' => $request->nisn,
            'class_room' => $request->class_room,
        ]);

        return redirect()->route('admin.manage.murids.index')
            ->with('status', 'Murid account created successfully.');
    }

    public function edit(User $murid)
    {
        $murid->load('murid');
        return view('admin.manage.murids.edit', compact('murid'));
    }

    public function update(Request $request, User $murid)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$murid->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nisn' => ['required', 'numeric', 'digits:10'],
            'class_room' => ['required', 'string', 'max:255'],
        ]);

        $murid->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $murid->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $murid->murid()->updateOrCreate(
            ['user_id' => $murid->id],
            [
                'nisn' => $request->nisn,
                'class_room' => $request->class_room,
            ]
        );

        return redirect()->route('admin.manage.murids.index')
            ->with('status', 'Murid account updated successfully.');
    }

    public function destroy(User $murid)
    {
        $murid->delete(); // This will cascade delete their profile

        return redirect()->route('admin.manage.murids.index')
            ->with('status', 'Murid account deleted successfully.');
    }
}
