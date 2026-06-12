<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $adminUser = User::create([
            'name' => 'Admin SMKN 1 Jakarta',
            'email' => 'admin@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        Admin::create([
            'user_id' => $adminUser->id,
            'nip' => '198805122010011002',
        ]);

        // 2. Seed Guru
        $guruUser = User::create([
            'name' => 'Budi Handoko, S.Pd.',
            'email' => 'budi@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '9876543210987654',
        ]);

        // 3. Seed Academic Prerequisites
        $ay = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $taGanjil = \App\Models\Semester::create([
            'tahun_akademik_id' => $ay->id,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $taGenap = \App\Models\Semester::create([
            'tahun_akademik_id' => $ay->id,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $classroom = \App\Models\Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $jurusan->id,
            'tahun_akademik_id' => $ay->id,
            'is_active' => true,
        ]);

        // 4. Seed Murid
        $muridUser = User::create([
            'name' => 'Aji Pratama',
            'email' => 'aji@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        Murid::create([
            'user_id' => $muridUser->id,
            'nisn' => '0054321098',
            'classroom_id' => $classroom->id,
            'no_telepon_orang_tua' => '628123456789',
        ]);
    }
}
