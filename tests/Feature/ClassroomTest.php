<?php

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Semester;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Classroom CRUD Management', function () {
    beforeEach(function () {
        // Create an admin user to perform actions
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create initial Tahun Ajaran
        $this->tahunAkademik = Semester::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        // Create another Tahun Ajaran for testing cross-year duplicates
        $this->tahunAkademik2 = Semester::create([
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => false,
        ]);

        // Create Jurusan
        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);
    });

    it('denies access to non-admin users', function () {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('admin.manage.classrooms.index'));
        $response->assertRedirect(route('dashboard'));
    });

    it('renders the index page for admins', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.classrooms.index'));
        $response->assertOk();
        $response->assertViewIs('admin.manage.classrooms.index');
    });

    it('creates a new classroom successfully and forces it to be active by default', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.classrooms.store'), [
            'nama_kelas' => 'xii rpl 1', // test auto-uppercase
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.classrooms.index'));
        $response->assertSessionHas('status', 'Kelas berhasil ditambahkan.');

        $this->assertDatabaseHas('classrooms', [
            'nama_kelas' => 'XII RPL 1', // verify auto-uppercase
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);
    });

    it('validates required fields when creating a classroom', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.classrooms.store'), [
            'nama_kelas' => '',
            'jurusan_id' => '',
            'tahun_akademik_id' => '',
        ]);

        $response->assertSessionHasErrors(['nama_kelas', 'jurusan_id', 'tahun_akademik_id']);
    });

    it('updates an existing classroom details and allows toggling status', function () {
        $classroom = Classroom::create([
            'nama_kelas' => 'X RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.classrooms.update', $classroom->id), [
            'nama_kelas' => 'X RPL 2',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => '0', // deactivate
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.classrooms.index'));
        $response->assertSessionHas('status', 'Kelas berhasil diperbarui.');

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'nama_kelas' => 'X RPL 2',
            'is_active' => false,
        ]);
    });

    it('soft deletes a classroom successfully', function () {
        $classroom = Classroom::create([
            'nama_kelas' => 'XII RPL 2',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.manage.classrooms.destroy', $classroom->id));

        $response->assertRedirect(route('admin.manage.classrooms.index'));
        $response->assertSessionHas('status', 'Kelas berhasil dihapus.');

        $this->assertSoftDeleted('classrooms', [
            'id' => $classroom->id,
        ]);
    });

    it('rejects duplicate class name in the same academic year', function () {
        Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.classrooms.store'), [
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
        ]);

        $response->assertSessionHasErrors('nama_kelas');
    });

    it('allows duplicate class name in different academic years', function () {
        Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.manage.classrooms.store'), [
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik2->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('classrooms', [
            'nama_kelas' => 'XII RPL 1',
            'tahun_akademik_id' => $this->tahunAkademik2->id,
        ]);
    });

    it('allows duplicate class name in the same academic year if the previous one is soft-deleted', function () {
        $classroom = Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'is_active' => true,
        ]);
        $classroom->delete();

        $response = $this->actingAs($this->admin)->post(route('admin.manage.classrooms.store'), [
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->tahunAkademik->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('classrooms', 2);
    });
});
