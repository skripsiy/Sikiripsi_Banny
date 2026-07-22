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
            'nip' => '1111111111111111',
        ]);

        // Create teacher 2
        $this->guruUser2 = User::factory()->create(['role' => 'guru']);
        $this->guru2 = Guru::create([
            'user_id' => $this->guruUser2->id,
            'nip' => '2222222222222222',
        ]);

        // Create TahunAjaran
        $this->academicYear = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $this->tahunAkademik = Semester::create([
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
            'tahun_akademik_id' => $this->academicYear->id,
            'is_active' => true,
        ]);

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

        // Assign subjects to classroom in Kurikulum Kelas
        $this->classroom->mataPelajarans()->sync([$this->mataPelajaran1->id, $this->mataPelajaran2->id]);
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

    it('renders the learning module create page for teachers', function () {
        $response = $this->actingAs($this->guruUser1)->get(route('guru.learning-modules.create'));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.create');
    });

    it('renders the learning module edit page for teachers', function () {
        $module = LearningModule::create([
            'guru_id' => $this->guru1->id,
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'title' => 'Modul Edit',
            'description' => 'Materi edit.',
        ]);

        $response = $this->actingAs($this->guruUser1)->get(route('guru.learning-modules.edit', $module->id));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.edit');
    });

    it('creates a learning module successfully', function () {
        $response = $this->actingAs($this->guruUser1)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
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
            'classroom_id' => $this->classroom->id,
            'title' => $this->mataPelajaran1->nama_pelajaran,
            'description' => 'Materi pengenalan aljabar dasar.',
        ]);
    });

    it('fails to create a learning module for a subject not assigned to the teacher', function () {
        $response = $this->actingAs($this->guruUser1)->post(route('guru.learning-modules.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran2->id, // Subject assigned to guru2
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
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
            'classroom_id' => $this->classroom->id,
            'title' => 'Judul Lama',
            'description' => 'Deskripsi lama.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'description' => 'Deskripsi baru.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.learning-modules.index'));

        $this->assertDatabaseHas('learning_modules', [
            'id' => $module->id,
            'classroom_id' => $this->classroom->id,
            'title' => $this->mataPelajaran1->nama_pelajaran,
            'description' => 'Deskripsi baru.',
        ]);
    });

    it('fails to update another teacher\'s learning module', function () {
        $module = LearningModule::create([
            'guru_id' => $this->guru2->id, // Owned by guru2
            'mata_pelajaran_id' => $this->mataPelajaran2->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'title' => 'Modul Guru 2',
            'description' => 'Materi guru 2.',
        ]);

        $response = $this->actingAs($this->guruUser1)->put(route('guru.learning-modules.update', $module->id), [
            'mata_pelajaran_id' => $this->mataPelajaran1->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
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
            'classroom_id' => $this->classroom->id,
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
            'classroom_id' => $this->classroom->id,
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
