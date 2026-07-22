<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\GuruMataPelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PenugasanGuruImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $errors = [];
        DB::transaction(function () use ($rows, &$errors) {
            foreach ($rows as $index => $row) {
                // index is 0-based, row in Excel is index + 2 (because row 1 is header)
                $rowNum = $index + 2;

                $guru = Guru::where('nip', $row['nip'])->first();
                $mataPelajaran = MataPelajaran::where('kode_pelajaran', $row['kode_pelajaran'])
                    ->whereNull('deleted_at')
                    ->first();

                if ($guru && $mataPelajaran) {
                    $exists = GuruMataPelajaran::where('guru_id', $guru->id)
                        ->where('mata_pelajaran_id', $mataPelajaran->id)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Baris {$rowNum}: Guru dengan NIP '{$row['nip']}' sudah ditugaskan untuk mata pelajaran '{$row['kode_pelajaran']}'.";
                        continue;
                    }

                    GuruMataPelajaran::create([
                        'guru_id' => $guru->id,
                        'mata_pelajaran_id' => $mataPelajaran->id,
                        'admin_id' => auth()->user()?->admin?->id,
                    ]);
                }
            }
        });

        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'file' => $errors
            ]);
        }
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['nip'])) {
            $data['nip'] = trim((string)$data['nip']);
        }
        if (isset($data['kode_pelajaran'])) {
            $data['kode_pelajaran'] = strtoupper(trim((string)$data['kode_pelajaran']));
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            'nip' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Guru::where('nip', $value)->exists();
                    if (!$exists) {
                        $fail("Guru dengan NIP '{$value}' tidak terdaftar.");
                    }
                }
            ],
            'kode_pelajaran' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = MataPelajaran::where('kode_pelajaran', $value)
                        ->whereNull('deleted_at')
                        ->exists();
                    if (!$exists) {
                        $fail("Mata pelajaran dengan kode '{$value}' tidak terdaftar.");
                    }
                }
            ],
        ];
    }
}
