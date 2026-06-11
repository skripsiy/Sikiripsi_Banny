<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\Subject;
use App\Models\LearningModule;
use App\Models\TahunAjaran;
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
        $this->tahunAjaran = TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        // Create subjects
        $this->subject1 = Subject::create([
            'kode_pelajaran' => 'MTK01',
            'nama_pelajaran' => 'Matematika Peminatan',
            'is_active' => true,
        ]);

        $this->subject2 = Subject::create([
            'kode_pelajaran' => 'FIS01',
            'nama_pelajaran' => 'Fisika Dasar',
            'is_active' => true,
        ]);

        // Assign subject1 to guru1
        $this->subject1->gurus()->sync([$this->guru1->id]);
        
        // Assign subject2 to guru2
        $this->subject2->gurus()->sync([$this->guru2->id]);
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
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.learning-modules.index'));
        $response->assertSessionHas('status', 'Modul pembelajaran berhasil ditambahkan.');

        // Check in database
        $this->assertDatabaseHas('learning_modules', [
            'guru_id' => $this->guru1->id,
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);
    });

    it('fails to create a learning module for a subject not assigned to the teacher', function () {
        $response = $this->actingAs($this->guruUser1)->post(route('guru.learning-modules.store'), [
            'subject_id' => $this->subject2->id, // Subject assigned to guru2
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);

        $response->assertSessionHasErrors('subject_id');
        $this->assertDatabaseCount('learning_modules', 0);
    });

    it('updates a learning module details successfully', function () {
        Storage::fake('public');
        $module = LearningModule::create([
            'guru_id' => $this->guru1->id,
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Judul Lama',
            'description' => 'Deskripsi lama.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
            'subject_id' => $this->subject2->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Modul Guru 2',
            'description' => 'Materi guru 2.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
            'subject_id' => $this->subject1->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
            'subject_id' => $this->subject2->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
