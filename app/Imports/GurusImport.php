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
                    'username'             => $row['username'] ?? null,
                    'email'                => $row['email'],
                    'password'             => Hash::make('ChangeMe@123'),
                    'role'                 => 'guru',
                    'must_change_password' => true,
                ]);

                Guru::create([
                    'user_id'           => $user->id,
                    'nip'               => $row['nip'],
                    'fullname'          => $row['fullname'] ?? null,
                    'tanggalLahir'      => $row['tanggal_lahir'] ?? null,
                    'alamat'            => $row['alamat'] ?? null,
                    'noWhatsapp'        => $row['no_whatsapp'] ?? null,
                    'gelar'             => $row['gelar'] ?? null,
                    'admin_id'          => auth()->user()?->admin?->id,
                ]);
            }
        });
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['nip'])) {
            $data['nip'] = (string)$data['nip'];
        }
        if (isset($data['no_whatsapp'])) {
            $data['no_whatsapp'] = (string)$data['no_whatsapp'];
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:50', 'unique:gurus,nip'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'gelar' => ['required', 'string', 'max:50'],
        ];
    }
}
