<?php

use App\Models\User;
use App\Models\Murid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| 1. EQUIVALENCE PARTITIONING (EP)
|--------------------------------------------------------------------------
*/

describe('Equivalence Partitioning (EP) - Email & Required Fields Validation', function () {
    beforeEach(function () {
        // Create an admin to bypass role middleware for managing murids
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->tahunAjaran = \App\Models\TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
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
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'is_active' => true,
        ]);
    });

    it('accepts valid email partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Valid Name',
            'email' => 'validemail@stovia.sch.id', // Valid partition
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890',
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'validemail@stovia.sch.id']);
    });

    it('rejects invalid email partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Valid Name',
            'email' => 'invalid-email-format', // Invalid partition
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890',
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('rejects empty required field partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => '', // Invalid partition (empty required field)
            'email' => 'validemail2@stovia.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890',
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasErrors('name');
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

        $this->tahunAjaran = \App\Models\TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
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
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'is_active' => true,
        ]);
    });

    // NISN Boundary: exactly 10 digits
    it('rejects NISN with 9 digits (below boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Student Name',
            'email' => 'student9@stovia.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '123456789', // 9 digits (invalid)
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasErrors('nisn');
    });

    it('accepts NISN with 10 digits (on boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Student Name',
            'email' => 'student10@stovia.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890', // 10 digits (valid)
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertSessionHasNoErrors();
    });

    it('rejects NISN with 11 digits (above boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Student Name',
            'email' => 'student11@stovia.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '12345678901', // 11 digits (invalid)
            'classroom_id' => $this->classroom->id,
        ]);

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
        it('accepts valid tahun ajaran format and sequence (2025/2026)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('tahun_ajarans', [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'is_active' => true,
            ]);
        });

        it('rejects invalid format (wrong separator, e.g., 2025-2026)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025-2026',
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasErrors('tahun_ajaran');
        });

        it('rejects invalid sequence (not +1 year, e.g., 2025/2027)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/2027',
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasErrors('tahun_ajaran');
        });
    });

    // 4.2 Boundary Value Analysis (BVA)
    describe('Boundary Value Analysis (BVA)', function () {
        it('rejects tahun ajaran with 8 characters (below boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/202', // 8 characters
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasErrors('tahun_ajaran');
        });

        it('accepts tahun ajaran with 9 characters (on boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/2026', // 9 characters
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
        });

        it('rejects tahun ajaran with 10 characters (above boundary)', function () {
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/20267', // 10 characters
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasErrors('tahun_ajaran');
        });
    });

    // 4.3 Decision Table Testing
    describe('Decision Table Testing', function () {
        it('Rule 1: keeps existing active record active when new record is inactive', function () {
            // Setup: create active record
            $activeTa = \App\Models\TahunAjaran::create([
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => true,
            ]);

            // Action: create inactive record
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'is_active' => '0',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertTrue($activeTa->fresh()->is_active);
        });

        it('Rule 2: auto-deactivates other active record when new record is active', function () {
            // Setup: create active record
            $activeTa = \App\Models\TahunAjaran::create([
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => true,
            ]);

            // Action: create active record
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
            // Assert old record is now inactive
            $this->assertFalse($activeTa->fresh()->is_active);
        });

        it('Rule 3: rejects creating duplicate active [tahun_ajaran, semester] combination', function () {
            // Setup: create active record
            \App\Models\TahunAjaran::create([
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => true,
            ]);

            // Action: create duplicate combination
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => '0',
            ]);

            $response->assertSessionHasErrors('tahun_ajaran');
        });

        it('Rule 4: allows creating duplicate combination if the existing one is soft-deleted', function () {
            // Setup: create active record and soft delete it
            $ta = \App\Models\TahunAjaran::create([
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => true,
            ]);
            $ta->delete();

            // Action: create duplicate combination
            $response = $this->actingAs($this->admin)->post(route('admin.manage.tahun-ajarans.store'), [
                'tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'is_active' => '1',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseCount('tahun_ajarans', 2);
        });
    });
});

