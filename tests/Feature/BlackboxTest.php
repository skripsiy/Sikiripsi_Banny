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
    });

    it('accepts valid email partition', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Valid Name',
            'email' => 'validemail@stovia.sch.id', // Valid partition
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890',
            'class_room' => 'XII RPL 1',
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
            'class_room' => 'XII RPL 1',
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
            'class_room' => 'XII RPL 1',
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
    });

    // NISN Boundary: exactly 10 digits
    it('rejects NISN with 9 digits (below boundary)', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.murids.store'), [
            'name' => 'Student Name',
            'email' => 'student9@stovia.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '123456789', // 9 digits (invalid)
            'class_room' => 'XII RPL 1',
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
            'class_room' => 'XII RPL 1',
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
            'class_room' => 'XII RPL 1',
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
