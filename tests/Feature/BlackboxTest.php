<?php

use App\Models\User;
use App\Models\Murid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validMuridPayload(array $overrides = []): array
{
    return array_merge([
        'username' => 'student_' . rand(1000, 9999),
        'email' => 'student' . rand(1000, 9999) . '@stovia.sch.id',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'nisn' => '1234567890',
        'no_telepon_orang_tua' => '628123456789',
        'namaLengkap' => 'Student Full Name',
        'tanggalLahir' => '2008-01-01',
        'alamat' => 'Jl. Pelajar No. 1',
        'noTelpon' => '628123456789',
        'namaOrangTua' => 'Student Parent Name',
    ], $overrides);
}

function validGuruPayload(array $overrides = []): array
{
    return array_merge([
        'username' => 'guru_' . rand(1000, 9999),
        'email' => 'guru' . rand(1000, 9999) . '@stovia.sch.id',
        'password' => 'ChangeMe@123',
        'password_confirmation' => 'ChangeMe@123',
        'nip' => '198501012010011002',
        'fullname' => 'Guru Baru Lengkap',
        'tanggalLahir' => '1980-01-01',
        'alamat' => 'Jl. Guru No. 1',
        'noWhatsapp' => '6281234567890',
        'gelar' => 'S.Pd',
    ], $overrides);
}

/*
|--------------------------------------------------------------------------
| 1. EQUIVALENCE PARTITIONING (EP)
|--------------------------------------------------------------------------
*/

describe('Equivalence Partitioning (EP) - Email & Required Fields Validation', function () {
    beforeEach(function () {
        // Create an admin to bypass role middleware for managing murids
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->academicYear = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $this->tahunAkademik = \App\Models\Semester::create([
            'tahun_akademik_id' => $this->academicYear->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $this->classroom = \App\Models\Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);
    });

    it('can load the murid management index page successfully', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.murids.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.manage.murids.index');
    });

    it('accepts valid email partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'email' => 'validemail@stovia.sch.id',
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'validemail@stovia.sch.id',
            'must_change_password' => true,
        ]);
    });

    it('rejects invalid email partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'email' => 'invalid-email-format',
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasErrors('email');
    });

    it('rejects empty required field partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'namaLengkap' => '',
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasErrors('namaLengkap');
    });

    it('sets must_change_password to true for manually created gurus', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.gurus.store'), validGuruPayload([
            'email' => 'gurubaru@stovia.sch.id',
            'nip' => '198501012010011002',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'gurubaru@stovia.sch.id',
            'role' => 'guru',
            'must_change_password' => true,
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| 2. BOUNDARY VALUE ANALYSIS (BVA)
|--------------------------------------------------------------------------
*/

describe('Boundary Value Analysis (BVA) - NISN Length & Password Length', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->academicYear = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $this->tahunAkademik = \App\Models\Semester::create([
            'tahun_akademik_id' => $this->academicYear->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $this->classroom = \App\Models\Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);
    });

    // NISN Boundary: exactly 10 digits
    it('rejects NISN with 9 digits (below boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'email' => 'student9@stovia.sch.id',
            'nisn' => '123456789', // 9 digits (invalid)
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasErrors('nisn');
    });

    it('accepts NISN with 10 digits (on boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'email' => 'student10@stovia.sch.id',
            'nisn' => '1234567890', // 10 digits (valid)
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasNoErrors();
    });

    it('rejects NISN with 11 digits (above boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), validMuridPayload([
            'email' => 'student11@stovia.sch.id',
            'nisn' => '12345678901', // 11 digits (invalid)
            'classroom_id' => $this->classroom->id,
        ]));

        $response->assertSessionHasErrors('nisn');
    });

    // Password Boundary: min 8 characters on Change Password form
    it('rejects password with 7 characters (below boundary)', function () {
        $user = User::factory()->create([
            'password' => Hash::make('ChangeMe@123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.update'), [
            'password' => 'abc1234', // 7 chars
            'password_confirmation' => 'abc1234',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('accepts password with 8 characters (on boundary)', function () {
        $user = User::factory()->create([
            'password' => Hash::make('ChangeMe@123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.update'), [
            'password' => 'abc12345', // 8 chars
            'password_confirmation' => 'abc12345',
        ]);

        $response->assertSessionHasNoErrors();
    });
});

/*
|--------------------------------------------------------------------------
| 3. DECISION TABLE TESTING
|--------------------------------------------------------------------------
*/

describe('Decision Table Testing - Login Combinations & Access', function () {
    // Rule 1: Incorrect credentials -> Redirect back with error
    it('Rule 1: rejects login with incorrect credentials', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'must_change_password' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    });

    // Rule 2: Correct credentials, must_change_password = true -> redirect to /change-password
    it('Rule 2: redirects to change password page if password is default', function () {
        $user = User::factory()->create([
            'password' => Hash::make('ChangeMe@123'),
            'must_change_password' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'ChangeMe@123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        // Try accessing dashboard, should be redirected to /change-password by middleware
        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertRedirect(route('password.change'));
    });

    // Rule 3: Correct credentials, must_change_password = false -> redirect to /dashboard
    it('Rule 3: grants access to dashboard if password changed', function () {
        $user = User::factory()->create([
            'password' => Hash::make('securepassword123'),
            'must_change_password' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'securepassword123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        // Try accessing dashboard, should load successfully
        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertOk();
    });

    // Rule 4: Correct credentials, User has been soft-deleted -> Redirect back with error (cannot log in)
    it('Rule 4: denies login if the user has been soft-deleted', function () {
        $user = User::factory()->create([
            'password' => Hash::make('securepassword123'),
            'must_change_password' => false,
        ]);

        // Soft delete the user
        $user->delete();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'securepassword123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    });
});

/*
|--------------------------------------------------------------------------
| 4. TAHUN AJARAN - BLACK BOX TESTING
|--------------------------------------------------------------------------
*/

describe('Tahun Ajaran - Black Box Testing (EP, BVA, Decision Table)', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    // 4.1 Equivalence Partitioning (EP)
    describe('Equivalence Partitioning (EP)', function () {
        it('accepts valid tahun ajaran format and sequence (2026/2027)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 2026,
                'tahun_akhir' => 2027,
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('tahun_akademiks', [
                'tahun_ajaran' => '2026/2027',
                'is_active' => true,
            ]);
        });

        it('rejects invalid format (wrong separator or non-numeric)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 'abc',
                'tahun_akhir' => 'def',
            ]);

            $response->assertSessionHasErrors(['tahun_awal', 'tahun_akhir']);
        });

        it('rejects invalid sequence (not +1 year, e.g., 2026/2028)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 2026,
                'tahun_akhir' => 2028,
            ]);

            $response->assertSessionHasErrors('tahun_akhir');
        });
    });

    // 4.2 Boundary Value Analysis (BVA)
    describe('Boundary Value Analysis (BVA)', function () {
        it('rejects tahun ajaran with 3 digits (below boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 202,
                'tahun_akhir' => 2027,
            ]);

            $response->assertSessionHasErrors('tahun_awal');
        });

        it('accepts tahun ajaran with 4 digits each (on boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 2026,
                'tahun_akhir' => 2027,
            ]);

            $response->assertSessionHasNoErrors();
        });

        it('rejects tahun ajaran with 5 digits (above boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 20265,
                'tahun_akhir' => 20266,
            ]);

            $response->assertSessionHasErrors('tahun_awal');
        });
    });

    // 4.3 Decision Table Testing
    describe('Decision Table Testing', function () {
        it('Rule 1: keeps existing active record active when new Year is added', function () {
            // Setup: create active record
            $activeTa = \App\Models\TahunAkademik::create([
                'tahun_ajaran' => '2026/2027',
                'is_active' => true,
            ]);

            // Action: create new year
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 2027,
                'tahun_akhir' => 2028,
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertTrue($activeTa->fresh()->is_active);
        });

        it('Rule 2: allows creating semesters under tahun akademik and validates fields', function () {
            $ta = \App\Models\TahunAkademik::create([
                'tahun_ajaran' => '2025/2026',
                'is_active' => true,
            ]);

            $response = $this->actingAs($this->admin)->post(route('admin.manage.semesters.store'), [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('semesters', [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2025-07-01 00:00:00',
                'end_date' => '2025-12-31 00:00:00',
                'is_active' => true,
            ]);
        });

        it('Rule 3: rejects creating duplicate active [tahun_akademik, semester] combination', function () {
            $ta = \App\Models\TahunAkademik::create([
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ]);

            // Setup: create active record
            \App\Models\Semester::create([
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'is_active' => true,
            ]);

            // Action: create duplicate combination
            $response = $this->actingAs($this->admin)->post(route('admin.manage.semesters.store'), [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'is_active' => '1',
            ]);

            $response->assertSessionHasErrors('semester');
        });

        it('Rule 4: allows creating duplicate combination if the existing one is soft-deleted', function () {
            $ta = \App\Models\TahunAkademik::create([
                'tahun_ajaran' => '2024/2025',
                'is_active' => true,
            ]);

            // Setup: create active record and soft delete it
            $taSemester = \App\Models\Semester::create([
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'is_active' => true,
            ]);
            $taSemester->delete();
            $ta->restore();

            // Action: create duplicate combination
            $response = $this->actingAs($this->admin)->post(route('admin.manage.semesters.store'), [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseCount('semesters', 2);
        });

        it('validates split fields tahun_awal and tahun_akhir with future year rules', function () {
            $currentYear = (int) date('Y');

            // Historical year (past year) should fail
            $resPast = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => 2020,
                'tahun_akhir' => 2021,
            ]);
            $resPast->assertSessionHasErrors('tahun_awal');

            // Non-consecutive year (2026/2028) should fail
            $resGap = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => $currentYear,
                'tahun_akhir' => $currentYear + 2,
            ]);
            $resGap->assertSessionHasErrors('tahun_akhir');

            // Valid future year (e.g., currentYear / currentYear+1) should succeed
            $resValid = $this->actingAs($this->admin)->post(route('admin.manage.tahun_akademiks.store'), [
                'tahun_awal' => $currentYear,
                'tahun_akhir' => $currentYear + 1,
            ]);
            $resValid->assertSessionHasNoErrors();
            $this->assertDatabaseHas('tahun_akademiks', [
                'tahun_ajaran' => $currentYear . '/' . ($currentYear + 1),
            ]);
        });

        it('validates semester dates to match chosen academic year range', function () {
            $ta = \App\Models\TahunAkademik::create([
                'tahun_ajaran' => '2026/2027',
                'is_active' => true,
            ]);

            // Date outside 2026-2027 (e.g. 2025) should fail
            $resOut = $this->actingAs($this->admin)->post(route('admin.manage.semesters.store'), [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
                'is_active' => '1',
            ]);
            $resOut->assertSessionHasErrors(['start_date', 'end_date']);

            // Date inside 2026-2027 range should pass
            $resIn = $this->actingAs($this->admin)->post(route('admin.manage.semesters.store'), [
                'tahun_akademik_id' => $ta->id,
                'semester' => 'ganjil',
                'start_date' => '2026-07-01',
                'end_date' => '2026-12-31',
                'is_active' => '1',
            ]);
            $resIn->assertSessionHasNoErrors();
        });
    });
});

