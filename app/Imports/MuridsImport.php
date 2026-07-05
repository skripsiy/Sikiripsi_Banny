<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\Semester;
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
        $activeSemester = Semester::where('is_active', true)->first() ?? Semester::first();
        $activeTahunAkademikId = $activeSemester?->tahun_akademik_id;

        DB::transaction(function () use ($rows, $activeTahunAkademikId) {
            foreach ($rows as $row) {
                $classroom = Classroom::where('nama_kelas', $row['class_room'])
                    ->where('tahun_akademik_id', $activeTahunAkademikId)
                    ->first();

                $user = User::create([
                    'username'             => $row['username'] ?? null,
                    'email'                => $row['email'],
                    'password'             => Hash::make('ChangeMe@123'),
                    'role'                 => 'murid',
                    'must_change_password' => true,
                ]);

                Murid::create([
                    'user_id'      => $user->id,
                    'nisn'         => $row['nisn'],
                    'classroom_id' => $classroom->id,
                    'no_telepon_orang_tua' => $row['no_telepon_orang_tua'] ?? null,
                    'namaLengkap'  => $row['nama_lengkap'] ?? null,
                    'tanggalLahir' => $row['tanggal_lahir'] ?? null,
                    'alamat'       => $row['alamat'] ?? null,
                    'noTelpon'     => $row['no_telpon'] ?? null,
                    'namaOrangTua' => $row['nama_orang_tua'] ?? null,
                    'admin_id'     => auth()->user()?->admin?->id,
                ]);
            }
        });
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['nisn'])) {
            $data['nisn'] = (string)$data['nisn'];
        }
        if (isset($data['no_telepon_orang_tua'])) {
            $data['no_telepon_orang_tua'] = (string)$data['no_telepon_orang_tua'];
        }
        if (isset($data['no_telpon'])) {
            $data['no_telpon'] = (string)$data['no_telpon'];
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'nisn' => ['required', 'numeric', 'digits:10'],
            'no_telepon_orang_tua' => ['required', 'string', 'max:20'],
            'username' => ['required', 'string', 'max:25', 'unique:users,username'],
            'nama_lengkap' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'no_telpon' => ['required', 'string', 'max:20'],
            'nama_orang_tua' => ['required', 'string', 'max:50'],
            'class_room' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $activeSemester = Semester::where('is_active', true)->first() ?? Semester::first();
                    if (!$activeSemester) {
                        $fail('Tidak ada Semester yang tersedia.');
                        return;
                    }
                    $activeTahunAkademikId = $activeSemester->tahun_akademik_id;
                    $exists = Classroom::where('nama_kelas', $value)
                        ->where('tahun_akademik_id', $activeTahunAkademikId)
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
