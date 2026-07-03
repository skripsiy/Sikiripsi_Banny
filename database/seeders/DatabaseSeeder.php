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
            'username' => 'admin_smk',
            'email' => 'admin@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        Admin::create([
            'user_id' => $adminUser->id,
            'nip' => '198805122010011002',
            'fullname' => 'Admin SMKN 1 Jakarta',
        ]);

        // 2. Seed Guru
        $guruUser = User::create([
            'username' => 'budi_handoko',
            'email' => 'budi@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '198501012010011002',
            'fullname' => 'Budi Handoko',
            'tanggalLahir' => '1985-01-01',
            'alamat' => 'Jl. Merdeka No. 10',
            'noWhatsapp' => '6281234567890',
            'gelar' => 'S.Pd.',
        ]);

        // 3. Seed Academic Prerequisites
        $ay = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $taGanjil = \App\Models\Semester::create([
            'tahun_akademik_id' => $ay->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $taGenap = \App\Models\Semester::create([
            'tahun_akademik_id' => $ay->id,
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
            'username' => 'aji_pratama',
            'email' => 'aji@smkn1jakarta.sch.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        Murid::create([
            'user_id' => $muridUser->id,
            'nisn' => '0054321098',
            'classroom_id' => $classroom->id,
            'no_telepon_orang_tua' => '628123456789',
            'namaLengkap' => 'Aji Pratama',
            'tanggalLahir' => '2008-05-15',
            'alamat' => 'Jl. Pemuda No. 5',
            'noTelpon' => '628111222333',
            'namaOrangTua' => 'Bambang Pratama',
        ]);
    }
}
