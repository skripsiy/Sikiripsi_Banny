<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\ValidationException;

class ClassroomMataPelajaranImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $namaKelas = trim($row['nama_kelas'] ?? '');
        $kodePelajaran = trim($row['kode_pelajaran'] ?? '');

        $classroom = Classroom::where('nama_kelas', $namaKelas)->first();
        if (!$classroom) {
            throw ValidationException::withMessages([
                'file' => ["Kelas '{$namaKelas}' tidak ditemukan."],
            ]);
        }

        $mataPelajaran = MataPelajaran::where('kode_pelajaran', $kodePelajaran)->first();
        if (!$mataPelajaran) {
            throw ValidationException::withMessages([
                'file' => ["Mata Pelajaran dengan kode '{$kodePelajaran}' tidak ditemukan."],
            ]);
        }

        $classroom->mataPelajarans()->syncWithoutDetaching([$mataPelajaran->id]);

        return null;
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => ['required', 'string'],
            'kode_pelajaran' => ['required', 'string'],
        ];
    }
}
