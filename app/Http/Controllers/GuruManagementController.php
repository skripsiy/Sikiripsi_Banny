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
        $sheet->setCellValue('A1', 'email');
        $sheet->setCellValue('B1', 'nip');
        $sheet->setCellValue('C1', 'fullname');
        $sheet->setCellValue('D1', 'tanggal_lahir');
        $sheet->setCellValue('E1', 'alamat');
        $sheet->setCellValue('F1', 'no_whatsapp');
        $sheet->setCellValue('G1', 'gelar');
        $sheet->setCellValue('H1', 'username');

        // Sample Data Row
        $sheet->setCellValue('A2', 'budi@smkn1jakarta.sch.id');
        $sheet->setCellValue('B2', '198501012010011002');
        $sheet->setCellValue('C2', 'Budi Handoko');
        $sheet->setCellValue('D2', '1985-01-01');
        $sheet->setCellValue('E2', 'Jl. Merdeka No. 10, Jakarta');
        $sheet->setCellValue('F2', '6281234567890');
        $sheet->setCellValue('G2', 'S.Pd.');
        $sheet->setCellValue('H2', 'budi_handoko');

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
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => ['required', 'string', 'max:50', 'unique:gurus,nip'],
            'fullname' => ['required', 'string', 'max:255'],
            'tanggalLahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'noWhatsapp' => ['required', 'string', 'max:20'],
            'gelar' => ['required', 'string', 'max:50'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'must_change_password' => true,
        ]);

        Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'fullname' => $request->fullname,
            'tanggalLahir' => $request->tanggalLahir,
            'alamat' => $request->alamat,
            'noWhatsapp' => $request->noWhatsapp,
            'gelar' => $request->gelar,
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
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$guru->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$guru->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nip' => ['required', 'string', 'max:50', 'unique:gurus,nip,'.($guru->guru?->id ?? '')],
            'fullname' => ['required', 'string', 'max:255'],
            'tanggalLahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'noWhatsapp' => ['required', 'string', 'max:20'],
            'gelar' => ['required', 'string', 'max:50'],
        ]);

        $guru->update([
            'username' => $request->username,
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
                'nip' => $request->nip,
                'fullname' => $request->fullname,
                'tanggalLahir' => $request->tanggalLahir,
                'alamat' => $request->alamat,
                'noWhatsapp' => $request->noWhatsapp,
                'gelar' => $request->gelar,
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
