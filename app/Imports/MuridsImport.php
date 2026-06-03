<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MuridsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $activeTa = TahunAjaran::where('is_active', true)->first();

        DB::transaction(function () use ($rows, $activeTa) {
            foreach ($rows as $row) {
                $classroom = Classroom::where('nama_kelas', $row['class_room'])
                    ->where('tahun_ajaran_id', $activeTa->id)
                    ->first();

                $user = User::create([
                    'name'                 => $row['name'],
                    'email'                => $row['email'],
                    'password'             => Hash::make('ChangeMe@123'),
                    'role'                 => 'murid',
                    'must_change_password' => true,
                ]);

                Murid::create([
                    'user_id'      => $user->id,
                    'nisn'         => $row['nisn'],
                    'classroom_id' => $classroom->id,
                ]);
            }
        });
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nisn' => ['required', 'string', 'max:50'],
            'class_room' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $activeTa = TahunAjaran::where('is_active', true)->first();
                    if (!$activeTa) {
                        $fail('Tidak ada Tahun Ajaran aktif saat ini.');
                        return;
                    }
                    $exists = Classroom::where('nama_kelas', $value)
                        ->where('tahun_ajaran_id', $activeTa->id)
                        ->whereNull('deleted_at')
                        ->exists();
                    if (!$exists) {
                        $fail("Kelas '{$value}' tidak terdaftar atau tidak aktif di Tahun Ajaran saat ini.");
                    }
                }
            ],
        ];
    }
}
