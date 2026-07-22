<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MataPelajaransImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $jurusanId = null;
                $kodeJurusan = isset($row['kode_jurusan']) ? trim($row['kode_jurusan']) : null;
                if (!empty($kodeJurusan)) {
                    $jurusan = Jurusan::where('kode_jurusan', $kodeJurusan)
                        ->where('is_active', true)
                        ->first();
                    if ($jurusan) {
                        $jurusanId = $jurusan->id;
                    }
                }

                MataPelajaran::create([
                    'kode_pelajaran' => strtoupper(trim($row['kode_pelajaran'])),
                    'nama_pelajaran' => trim($row['nama_pelajaran']),
                    'jurusan_id'     => $jurusanId,
                    'is_active'      => true,
                    'admin_id'       => auth()->user()?->admin?->id,
                ]);
            }
        });
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['kode_pelajaran'])) {
            $data['kode_pelajaran'] = strtoupper(trim((string)$data['kode_pelajaran']));
        }
        if (isset($data['nama_pelajaran'])) {
            $data['nama_pelajaran'] = trim((string)$data['nama_pelajaran']);
        }
        if (isset($data['kode_jurusan'])) {
            $data['kode_jurusan'] = trim((string)$data['kode_jurusan']);
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            'kode_pelajaran' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $exists = MataPelajaran::where('kode_pelajaran', $value)
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($exists) {
                        $fail("Kode mata pelajaran '{$value}' sudah terdaftar.");
                    }
                }
            ],
            'nama_pelajaran' => ['required', 'string', 'max:100'],
            'kode_jurusan' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $exists = Jurusan::where('kode_jurusan', $value)
                            ->where('is_active', true)
                            ->whereNull('deleted_at')
                            ->exists();
                        if (!$exists) {
                            $fail("Kode jurusan '{$value}' tidak terdaftar atau tidak aktif.");
                        }
                    }
                }
            ],
        ];
    }
}
