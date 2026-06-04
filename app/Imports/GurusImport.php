<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GurusImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $user = User::create([
                    'name'                 => $row['name'],
                    'email'                => $row['email'],
                    'password'             => Hash::make('ChangeMe@123'),
                    'role'                 => 'guru',
                    'must_change_password' => true,
                ]);

                Guru::create([
                    'user_id'           => $user->id,
                    'nuptk'             => $row['nuptk'],
                ]);
            }
        });
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['nuptk'])) {
            $data['nuptk'] = (string)$data['nuptk'];
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nuptk' => ['required', 'string', 'max:50'],
        ];
    }
}
