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

        if (isset($data['tanggal_lahir'])) {
            $val = $data['tanggal_lahir'];
            if ($val instanceof \DateTimeInterface) {
                $data['tanggal_lahir'] = $val->format('Y-m-d');
            } elseif (is_numeric($val)) {
                try {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                    $data['tanggal_lahir'] = $date->format('Y-m-d');
                } catch (\Exception $e) {
                }
            } elseif (is_string($val) && trim($val) !== '') {
                $val = trim($val);
                $parsed = false;
                $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y', 'm-d-Y'];
                foreach ($formats as $format) {
                    $d = \DateTime::createFromFormat($format, $val);
                    if ($d && $d->format($format) === $val) {
                        $data['tanggal_lahir'] = $d->format('Y-m-d');
                        $parsed = true;
                        break;
                    }
                }
                if (!$parsed) {
                    $timestamp = strtotime(str_replace('/', '-', $val));
                    if ($timestamp !== false) {
                        $data['tanggal_lahir'] = date('Y-m-d', $timestamp);
                    }
                }
            }
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'nip' => ['required', 'numeric', 'digits:18', 'unique:gurus,nip'],
            'username' => ['required', 'string', 'max:25', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'gelar' => ['required', 'string', 'max:50'],
        ];
    }
}
