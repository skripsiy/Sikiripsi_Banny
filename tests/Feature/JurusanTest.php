<?php

use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Jurusan CRUD Management', function () {
    beforeEach(function () {
        // Create an admin user to perform actions
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    it('denies access to non-admin users', function () {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('admin.manage.jurusans.index'));
        $response->assertRedirect(route('dashboard'));
    });

    it('renders the index page for admins', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.jurusans.index'));
        $response->assertOk();
        $response->assertViewIs('admin.manage.jurusans.index');
    });

    it('creates a new jurusan successfully and forces it to be active by default', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.jurusans.store'), [
            'kode_jurusan' => 'rpl', // lowercase to test auto-uppercase
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Jurusan software engineering',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.jurusans.index'));
        $response->assertSessionHas('status', 'Jurusan berhasil ditambahkan.');

        $this->assertDatabaseHas('jurusans', [
            'kode_jurusan' => 'RPL', // verify auto-uppercase
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Jurusan software engineering',
            'is_active' => true, // verify defaults to active
        ]);
    });

    it('validates required fields when creating a jurusan', function () {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.jurusans.store'), [
            'kode_jurusan' => '',
            'nama_jurusan' => '',
        ]);

        $response->assertSessionHasErrors(['kode_jurusan', 'nama_jurusan']);
    });

    it('updates an existing jurusan details and allows toggling status', function () {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer Jaringan',
            'deskripsi' => 'Old description',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.manage.jurusans.update', $jurusan->id), [
            'kode_jurusan' => 'tkj-new',
            'nama_jurusan' => 'Teknik Komputer Jaringan Baru',
            'deskripsi' => 'New description',
            'is_active' => '0', // deactivate
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.manage.jurusans.index'));
        $response->assertSessionHas('status', 'Jurusan berhasil diperbarui.');

        $this->assertDatabaseHas('jurusans', [
            'id' => $jurusan->id,
            'kode_jurusan' => 'TKJ-NEW',
            'nama_jurusan' => 'Teknik Komputer Jaringan Baru',
            'deskripsi' => 'New description',
            'is_active' => false,
        ]);
    });

    it('soft deletes a jurusan successfully', function () {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'MM',
            'nama_jurusan' => 'Multimedia',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.manage.jurusans.destroy', $jurusan->id));

        $response->assertRedirect(route('admin.manage.jurusans.index'));
        $response->assertSessionHas('status', 'Jurusan berhasil dihapus.');

        $this->assertSoftDeleted('jurusans', [
            'id' => $jurusan->id,
        ]);
    });

    it('allows creating a duplicate kode_jurusan if the previous one was soft-deleted', function () {
        // Create and soft delete
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);
        $jurusan->delete();

        // Create new with same code
        $response = $this->actingAs($this->admin)->post(route('admin.manage.jurusans.store'), [
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak 2',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jurusans', [
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak 2',
            'deleted_at' => null,
        ]);
    });
});
