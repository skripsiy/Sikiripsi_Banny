<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\LearningModule;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('Learning Module Management', function () {
    beforeEach(function () {
        // Create teacher 1
        $this->guruUser1 = User::factory()->create(['role' => 'guru']);
        $this->guru1 = Guru::create([
            'user_id' => $this->guruUser1->id,
            'nuptk' => '1111111111111111',
        ]);

        // Create teacher 2
        $this->guruUser2 = User::factory()->create(['role' => 'guru']);
        $this->guru2 = Guru::create([
            'user_id' => $this->guruUser2->id,
            'nuptk' => '2222222222222222',
        ]);

        // Create TahunAjaran
        $this->tahunAkademik = Semester::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
        $this->academicYear = $this->tahunAkademik->tahunAkademik;

        // Create mata_pelajarans
        $this->mataPelajaran1 = MataPelajaran::create([
            'kode_pelajaran' => 'MTK01',
            'nama_pelajaran' => 'Matematika Peminatan',
            'is_active' => true,
        ]);

        $this->mataPelajaran2 = MataPelajaran::create([
            'kode_pelajaran' => 'FIS01',
            'nama_pelajaran' => 'Fisika Dasar',
            'is_active' => true,
        ]);

        // Assign subject1 to guru1
        $this->mataPelajaran1->gurus()->sync([$this->guru1->id]);
        
        // Assign subject2 to guru2
        $this->mataPelajaran2->gurus()->sync([$this->guru2->id]);
    });

    it('denies access to guests', function () {
        $response = $this->get(route('guru.learning-modules.index'));
        $response->assertRedirect(route('login'));
    });

    it('denies access to non-guru roles (admin)', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('guru.learning-modules.index'));
        $response->assertRedirect(route('dashboard'));
    });

    it('grants access to teachers', function () {
        $response = $this->actingAs($this->guruUser1)->get(route('guru.learning-modules.index'));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.index');
    });

    it('creates a learning module successfully', function () {
        $response = $this->actingAs($this->guruUser1)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.learning-modules.index'));
        $response->assertSessionHas('status', 'Modul pembelajaran berhasil ditambahkan.');

        // Check in database
        $this->assertDatabaseHas('learning_modules', [
            'guru_id' => $this->guru1->id,
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);
    });

    it('fails to create a learning module for a subject not assigned to the teacher', function () {
        $response = $this->actingAs($this->guruUser1)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran2->id, // Subject assigned to guru2
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);

        $response->assertSessionHasErrors('mata_pelajaran_id');
        $this->assertDatabaseCount('learning_modules', 0);
    });

    it('updates a learning module details successfully', function () {
        Storage::fake('public');
        $module = LearningModule::create([
            'guru_id' => $this->guru1->id,
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Judul Lama',
            'description' => 'Deskripsi lama.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.learning-modules.index'));

        $this->assertDatabaseHas('learning_modules', [
            'id' => $module->id,
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru.',
        ]);
    });

    it('fails to update another teacher\'s learning module', function () {
        $module = LearningModule::create([
            'guru_id' => $this->guru2->id, // Owned by guru2
            'mata_pelajaran_id' => $this->mataPelajaran2->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Modul Guru 2',
            'description' => 'Materi guru 2.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Diubah Guru 1',
            'description' => 'Mencoba meretas.',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('learning_modules', [
            'id' => $module->id,
            'title' => 'Modul Guru 2',
        ]);
    });

    it('deletes a learning module successfully', function () {
        $module = LearningModule::create([
            'guru_id' => $this->guru1->id,
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Modul Hapus',
            'description' => 'Materi hapus.',
        ]);

        $response = $this->actingAs($this->guruUser1)->delete(route('guru.learning-modules.destroy', $module->id));

        $response->assertRedirect(route('guru.learning-modules.index'));
        $this->assertSoftDeleted('learning_modules', [
            'id' => $module->id,
        ]);
    });

    it('fails to delete another teacher\'s learning module', function () {
        $module = LearningModule::create([
            'guru_id' => $this->guru2->id, // Owned by guru2
            'mata_pelajaran_id' => $this->mataPelajaran2->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Modul Guru 2',
            'description' => 'Materi guru 2.',
        ]);

        $response = $this->actingAs($this->guruUser1)->delete(route('guru.learning-modules.destroy', $module->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('learning_modules', [
            'id' => $module->id,
            'deleted_at' => null,
        ]);
    });
});
