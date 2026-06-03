<?php

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Subject CRUD Management', function () {
    beforeEach(function () {
        // Create an admin user to perform actions
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create Jurusan
        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);
    });

    it('denies access to non-admin users', function () {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('admin.manage.subjects.index'));
        $response->assertStatus(403);
    });

    it('renders the index page for admins', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.subjects.index'));
        $response->assertOk();
        $response->assertViewIs('admin.manage.subjects.index');
    });

    it('creates a new subject successfully, forces it active by default, and converts code to uppercase', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'kode_pelajaran' => 'mp001', // lowercase to test uppercase
            'nama_pelajaran' => 'Matematika Peminatan',
            'jurusan_id' => $this->jurusan->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.subjects.index'));
        $response->assertSessionHas('status', 'Mata pelajaran berhasil ditambahkan.');

        $this->assertDatabaseHas('subjects', [
            'kode_pelajaran' => 'MP001', // verify auto-uppercase
            'nama_pelajaran' => 'Matematika Peminatan',
            'jurusan_id' => $this->jurusan->id,
            'is_active' => true,
        ]);
    });

    it('allows creating a general subject with null jurusan_id', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'kode_pelajaran' => 'BIN01',
            'nama_pelajaran' => 'Bahasa Indonesia',
            'jurusan_id' => '', // general
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('subjects', [
            'kode_pelajaran' => 'BIN01',
            'nama_pelajaran' => 'Bahasa Indonesia',
            'jurusan_id' => null,
        ]);
    });

    it('validates required fields when creating a subject', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'kode_pelajaran' => '',
            'nama_pelajaran' => '',
        ]);

        $response->assertSessionHasErrors(['kode_pelajaran', 'nama_pelajaran']);
    });

    it('updates an existing subject details and allows toggling status', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP002',
            'nama_pelajaran' => 'Fisika',
            'jurusan_id' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.subjects.update', $subject->id), [
            'kode_pelajaran' => 'mp002-new',
            'nama_pelajaran' => 'Fisika Terapan',
            'jurusan_id' => $this->jurusan->id,
            'is_active' => '0', // deactivate
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.subjects.index'));
        $response->assertSessionHas('status', 'Mata pelajaran berhasil diperbarui.');

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'kode_pelajaran' => 'MP002-NEW',
            'nama_pelajaran' => 'Fisika Terapan',
            'jurusan_id' => $this->jurusan->id,
            'is_active' => false,
        ]);
    });

    it('soft deletes a subject successfully', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP003',
            'nama_pelajaran' => 'Kimia',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.manage.subjects.destroy', $subject->id));

        $response->assertRedirect(route('admin.manage.subjects.index'));
        $response->assertSessionHas('status', 'Mata pelajaran berhasil dihapus.');

        $this->assertSoftDeleted('subjects', [
            'id' => $subject->id,
        ]);
    });

    it('rejects duplicate code in active subjects', function () {
        Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'kode_pelajaran' => 'mp001',
            'nama_pelajaran' => 'Matematika Dasar',
        ]);

        $response->assertSessionHasErrors('kode_pelajaran');
    });

    it('allows duplicate code if the previous subject was soft-deleted', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);
        $subject->delete();

        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'kode_pelajaran' => 'mp001',
            'nama_pelajaran' => 'Matematika Baru',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('subjects', 2);
    });

    it('denies teacher assignment access to non-admin users', function () {
        $user = User::factory()->create(['role' => 'guru']);
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.manage.subjects.assign-teachers', $subject->id), [
            'guru_ids' => []
        ]);
        $response->assertStatus(403);
    });

    it('assigns teachers to a subject successfully', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        // Create a teacher user and Guru model
        $guruUser1 = User::factory()->create(['role' => 'guru']);
        $guru1 = \App\Models\Guru::create([
            'user_id' => $guruUser1->id,
            'nuptk' => '1234567890123456',
            'subject_specialty' => 'Matematika',
        ]);

        $guruUser2 = User::factory()->create(['role' => 'guru']);
        $guru2 = \App\Models\Guru::create([
            'user_id' => $guruUser2->id,
            'nuptk' => '6543210987654321',
            'subject_specialty' => 'Fisika',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.assign-teachers', $subject->id), [
            'guru_ids' => [$guru1->id, $guru2->id]
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.subjects.index'));
        $response->assertSessionHas('status', 'Guru pengampu berhasil diperbarui.');

        $this->assertDatabaseHas('subject_guru', [
            'subject_id' => $subject->id,
            'guru_id' => $guru1->id,
        ]);

        $this->assertDatabaseHas('subject_guru', [
            'subject_id' => $subject->id,
            'guru_id' => $guru2->id,
        ]);
    });

    it('fails when assigning invalid teacher ids', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.assign-teachers', $subject->id), [
            'guru_ids' => [9999, 8888]
        ]);

        $response->assertSessionHasErrors(['guru_ids.0', 'guru_ids.1']);
    });
});
