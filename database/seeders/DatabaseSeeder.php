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
            'name' => 'Admin Stovia',
            'email' => 'admin@stovia.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        Admin::create([
            'user_id' => $adminUser->id,
            'nip' => '198805122010011002',
            'department' => 'IT & Kurikulum',
        ]);

        // 2. Seed Guru
        $guruUser = User::create([
            'name' => 'Budi Handoko, S.Pd.',
            'email' => 'budi@stovia.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '9876543210987654',
            'subject_specialty' => 'Matematika & Pemrograman Web',
        ]);

        // 3. Seed Murid
        $muridUser = User::create([
            'name' => 'Aji Pratama',
            'email' => 'aji@stovia.sch.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        Murid::create([
            'user_id' => $muridUser->id,
            'nisn' => '0054321098',
            'class_room' => 'XII RPL 1',
        ]);
    }
}
