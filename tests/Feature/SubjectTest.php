<?php

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Subject;
use App\Models\Guru;
use App\Models\SubjectGuru;
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

    it('creates a new subject successfully, forces it active by default, and automatically generates a unique 6-character uppercase code', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'nama_pelajaran' => 'Matematika Peminatan',
            'jurusan_id' => $this->jurusan->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.subjects.index'));
        $response->assertSessionHas('status', 'Mata pelajaran berhasil ditambahkan.');

        $subject = Subject::where('nama_pelajaran', 'Matematika Peminatan')->first();
        $this->assertNotNull($subject);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $subject->kode_pelajaran);
        $this->assertEquals($this->jurusan->id, $subject->jurusan_id);
        $this->assertTrue((bool)$subject->is_active);
    });

    it('allows creating a general subject with null jurusan_id', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'nama_pelajaran' => 'Bahasa Indonesia',
            'jurusan_id' => '', // general
        ]);

        $response->assertSessionHasNoErrors();

        $subject = Subject::where('nama_pelajaran', 'Bahasa Indonesia')->first();
        $this->assertNotNull($subject);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $subject->kode_pelajaran);
        $this->assertNull($subject->jurusan_id);
    });

    it('validates required fields when creating a subject', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.subjects.store'), [
            'nama_pelajaran' => '',
        ]);

        $response->assertSessionHasErrors(['nama_pelajaran']);
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

    it('rejects duplicate code when updating active subjects', function () {
        $subject1 = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $subject2 = Subject::create([
            'kode_pelajaran' => 'MP002',
            'nama_pelajaran' => 'Fisika',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.subjects.update', $subject2->id), [
            'kode_pelajaran' => 'mp001', // tries to update to existing code
            'nama_pelajaran' => 'Fisika Baru',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('kode_pelajaran');
    });

    it('allows code if the conflicting subject was soft-deleted', function () {
        $subject1 = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika Old',
            'is_active' => true,
        ]);
        $subject1->delete();

        $subject2 = Subject::create([
            'kode_pelajaran' => 'MP002',
            'nama_pelajaran' => 'Matematika New',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.subjects.update', $subject2->id), [
            'kode_pelajaran' => 'mp001', // tries to update to the soft-deleted code
            'nama_pelajaran' => 'Matematika New',
            'is_active' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('subjects', [
            'id' => $subject2->id,
            'kode_pelajaran' => 'MP001',
        ]);
    });

    it('denies penugasan-guru access to non-admin users', function () {
        $user = User::factory()->create(['role' => 'guru']);
        
        $response = $this->actingAs($user)->get(route('admin.manage.penugasan-guru.index'));
        $response->assertStatus(403);
    });

    it('renders the assign index page for admins', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.penugasan-guru.index'));
        $response->assertOk();
        $response->assertViewIs('admin.manage.subjects.assign_index');
    });

    it('creates a new penugasan-guru successfully', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '1234567890123456',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.penugasan-guru.store'), [
            'subject_id' => $subject->id,
            'guru_id' => $guru->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.penugasan-guru.index'));
        $response->assertSessionHas('status', 'Penugasan guru berhasil ditambahkan.');

        $this->assertDatabaseHas('subject_guru', [
            'subject_id' => $subject->id,
            'guru_id' => $guru->id,
        ]);
    });

    it('rejects duplicate penugasan-guru combination', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '1234567890123456',
        ]);

        SubjectGuru::create([
            'subject_id' => $subject->id,
            'guru_id' => $guru->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.penugasan-guru.store'), [
            'subject_id' => $subject->id,
            'guru_id' => $guru->id,
        ]);

        $response->assertSessionHasErrors('subject_id');
    });

    it('updates a penugasan-guru successfully', function () {
        $subject1 = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);
        $subject2 = Subject::create([
            'kode_pelajaran' => 'FIS01',
            'nama_pelajaran' => 'Fisika',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '1234567890123456',
        ]);

        $assignment = SubjectGuru::create([
            'subject_id' => $subject1->id,
            'guru_id' => $guru->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.penugasan-guru.update', $assignment->id), [
            'subject_id' => $subject2->id,
            'guru_id' => $guru->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.penugasan-guru.index'));

        $this->assertDatabaseHas('subject_guru', [
            'id' => $assignment->id,
            'subject_id' => $subject2->id,
            'guru_id' => $guru->id,
        ]);
    });

    it('deletes a penugasan-guru successfully', function () {
        $subject = Subject::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['role' => 'guru']);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nuptk' => '1234567890123456',
        ]);

        $assignment = SubjectGuru::create([
            'subject_id' => $subject->id,
            'guru_id' => $guru->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.manage.penugasan-guru.destroy', $assignment->id));

        $response->assertRedirect(route('admin.manage.penugasan-guru.index'));
        $this->assertDatabaseMissing('subject_guru', [
            'id' => $assignment->id,
        ]);
    });
});
