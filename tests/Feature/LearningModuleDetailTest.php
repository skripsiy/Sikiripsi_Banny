<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\MataPelajaran;
use App\Models\LearningModule;
use App\Models\LearningModuleMateri;
use App\Models\LearningModuleTugas;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\LearningModuleAbsensi;
use App\Models\Jurusan;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

describe('Learning Module Details and Sub-content', function () {
    beforeEach(function () {
        // Teacher 1
        $this->guruUser1 = User::factory()->create(['role' => 'guru']);
        $this->guru1 = Guru::create([
            'user_id' => $this->guruUser1->id,
            'nip' => '1111111111111111',
        ]);

        // Teacher 2
        $this->guruUser2 = User::factory()->create(['role' => 'guru']);
        $this->guru2 = Guru::create([
            'user_id' => $this->guruUser2->id,
            'nip' => '2222222222222222',
        ]);

        // Setup Jurusan & Tahun Ajaran
        $this->academicYear = \App\Models\TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $this->tahunAkademik = Semester::create([
            'tahun_akademik_id' => $this->academicYear->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        // Subject & Classroom setup
        $this->mataPelajaran = MataPelajaran::create([
            'kode_pelajaran' => 'MTK01',
            'nama_pelajaran' => 'Matematika Peminatan',
            'is_active' => true,
        ]);
        $this->mataPelajaran->gurus()->sync([$this->guru1->id]);

        $this->classroom = Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusan->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'is_active' => true,
        ]);

        // Student with parent phone number
        $this->muridUser = User::factory()->create(['role' => 'murid']);
        $this->murid = Murid::create([
            'user_id' => $this->muridUser->id,
            'nisn' => '1234567890',
            'classroom_id' => $this->classroom->id,
            'no_telepon_orang_tua' => '628123456789',
        ]);

        // Learning module owned by teacher 1
        $this->learningModule = LearningModule::create([
            'guru_id' => $this->guru1->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Aljabar Modul',
            'description' => 'Materi Aljabar.',
        ]);
    });

    it('renders materi create for teacher', function () {
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.materis.create', $this->learningModule->id));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.materis.create');
    });

    it('renders materi edit for teacher', function () {
        $materi = LearningModuleMateri::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Materi Edit',
            'content' => 'Konten Edit',
        ]);
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.materis.edit', [$this->learningModule->id, $materi->id]));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.materis.edit');
    });

    it('allows teacher to add a materi successfully', function () {
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.materis.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Pertemuan 1',
                'content' => 'Isi materi pengenalan aljabar.',
            ]);

        $response->assertRedirect(route('guru.learning-modules.show', [$this->learningModule->id, 'semester_id' => $this->tahunAkademik->id]));
        $this->assertDatabaseHas('learning_module_materis', [
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Pertemuan 1',
            'content' => 'Isi materi pengenalan aljabar.',
        ]);
    });

    it('denies teacher to add a materi to another teacher\'s learning module', function () {
        $response = $this->actingAs($this->guruUser2)
            ->post(route('guru.learning-modules.materis.store', $this->learningModule->id), [
                'title' => 'Materi Guru 2',
                'content' => 'Coba hack.',
            ]);

        $response->assertStatus(403);
    });

    it('allows teacher to update their own materi', function () {
        $materi = LearningModuleMateri::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Judul Lama',
            'content' => 'Konten Lama',
        ]);

        $response = $this->actingAs($this->guruUser1)
            ->put(route('guru.learning-modules.materis.update', [$this->learningModule->id, $materi->id]), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Judul Baru',
                'content' => 'Konten Baru',
            ]);

        $response->assertRedirect(route('guru.learning-modules.show', [$this->learningModule->id, 'semester_id' => $this->tahunAkademik->id]));
        $this->assertDatabaseHas('learning_module_materis', [
            'id' => $materi->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Judul Baru',
            'content' => 'Konten Baru',
        ]);
    });

    it('allows teacher to delete their own materi', function () {
        $materi = LearningModuleMateri::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Materi Hapus',
            'content' => 'Konten Hapus',
        ]);

        $response = $this->actingAs($this->guruUser1)
            ->delete(route('guru.learning-modules.materis.destroy', [$this->learningModule->id, $materi->id]));

        $response->assertRedirect(route('guru.learning-modules.show', [$this->learningModule->id, 'semester_id' => $this->tahunAkademik->id]));
        $this->assertSoftDeleted('learning_module_materis', [
            'id' => $materi->id,
        ]);
    });

    it('renders tugas create for teacher', function () {
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.tugas.create', $this->learningModule->id));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.tugas.create');
    });

    it('renders tugas edit for teacher', function () {
        $tugas = LearningModuleTugas::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Tugas Edit',
            'instructions' => 'Instruksi Edit',
            'due_date' => '2026-06-10 23:59:00',
        ]);
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.tugas.edit', [$this->learningModule->id, $tugas->id]));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.tugas.edit');
    });

    it('allows teacher to manage tugas successfully', function () {
        // Test Store
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.tugas.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Tugas 1',
                'instructions' => 'Kerjakan halaman 10.',
                'due_date' => '2026-06-10 23:59:00',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('learning_module_tugas', [
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Tugas 1',
        ]);

        $tugas = LearningModuleTugas::first();

        // Test Update
        $response = $this->actingAs($this->guruUser1)
            ->put(route('guru.learning-modules.tugas.update', [$this->learningModule->id, $tugas->id]), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Tugas 1 Baru',
                'instructions' => 'Kerjakan halaman 12.',
                'due_date' => '2026-06-12 23:59:00',
            ]);

        $this->assertDatabaseHas('learning_module_tugas', [
            'id' => $tugas->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Tugas 1 Baru',
        ]);

        // Test Destroy
        $response = $this->actingAs($this->guruUser1)
            ->delete(route('guru.learning-modules.tugas.destroy', [$this->learningModule->id, $tugas->id]));

        $this->assertSoftDeleted('learning_module_tugas', [
            'id' => $tugas->id,
        ]);
    });

    it('renders quizzes create for teacher', function () {
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.quizzes.create', $this->learningModule->id));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.quizzes.create');
    });

    it('renders quizzes edit for teacher', function () {
        $quiz = LearningModuleQuiz::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Quiz Edit',
            'instructions' => 'Instruksi Edit',
            'duration_minutes' => 15,
            'due_date' => '2026-06-10 23:59:00',
        ]);
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.quizzes.edit', [$this->learningModule->id, $quiz->id]));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.quizzes.edit');
    });

    it('allows teacher to manage quizzes successfully', function () {
        // Test Store
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.quizzes.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Quiz 1',
                'instructions' => 'Jawab jujur.',
                'duration_minutes' => 15,
                'due_date' => '2026-06-10 23:59:00',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('learning_module_quizzes', [
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Quiz 1',
        ]);

        $quiz = LearningModuleQuiz::first();

        // Test Update
        $response = $this->actingAs($this->guruUser1)
            ->put(route('guru.learning-modules.quizzes.update', [$this->learningModule->id, $quiz->id]), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Quiz 1 Baru',
                'instructions' => 'Jawab jujur baru.',
                'duration_minutes' => 20,
                'due_date' => '2026-06-12 23:59:00',
            ]);

        $this->assertDatabaseHas('learning_module_quizzes', [
            'id' => $quiz->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Quiz 1 Baru',
            'duration_minutes' => 20,
        ]);

        // Test Destroy
        $response = $this->actingAs($this->guruUser1)
            ->delete(route('guru.learning-modules.quizzes.destroy', [$this->learningModule->id, $quiz->id]));

        $this->assertSoftDeleted('learning_module_quizzes', [
            'id' => $quiz->id,
        ]);
    });

    it('renders ujian create for teacher', function () {
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.ujians.create', $this->learningModule->id));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.ujians.create');
    });

    it('renders ujian edit for teacher', function () {
        $ujian = LearningModuleUjian::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Ujian Edit',
            'instructions' => 'Instruksi Edit',
            'duration_minutes' => 90,
            'due_date' => '2026-06-10 23:59:00',
        ]);
        $response = $this->actingAs($this->guruUser1)
            ->get(route('guru.learning-modules.ujians.edit', [$this->learningModule->id, $ujian->id]));
        $response->assertOk();
        $response->assertViewIs('guru.learning_modules.ujians.edit');
    });

    it('allows teacher to manage ujian successfully', function () {
        // Test Store
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.ujians.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Ujian 1',
                'instructions' => 'Kamera wajib on.',
                'duration_minutes' => 90,
                'due_date' => '2026-06-10 23:59:00',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('learning_module_ujians', [
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Ujian 1',
        ]);

        $ujian = LearningModuleUjian::first();

        // Test Update
        $response = $this->actingAs($this->guruUser1)
            ->put(route('guru.learning-modules.ujians.update', [$this->learningModule->id, $ujian->id]), [
                'semester_id' => $this->tahunAkademik->id,
                'title' => 'Ujian 1 Baru',
                'instructions' => 'Kamera wajib on baru.',
                'duration_minutes' => 120,
                'due_date' => '2026-06-12 23:59:00',
            ]);

        $this->assertDatabaseHas('learning_module_ujians', [
            'id' => $ujian->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Ujian 1 Baru',
            'duration_minutes' => 120,
        ]);

        // Test Destroy
        $response = $this->actingAs($this->guruUser1)
            ->delete(route('guru.learning-modules.ujians.destroy', [$this->learningModule->id, $ujian->id]));

        $this->assertSoftDeleted('learning_module_ujians', [
            'id' => $ujian->id,
        ]);
    });

    it('saves attendance and triggers Fonnte WhatsApp notification when student is absent', function () {
        config(['services.fonnte.token' => 'valid-test-token']);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        // Save attendance as 'alpa'
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.absensi.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'date' => '2026-06-04',
                'status' => [
                    $this->murid->id => 'alpa',
                ],
            ]);

        $response->assertRedirect(route('guru.learning-modules.absensi.index', [
            'learning_module' => $this->learningModule->id,
            'date' => '2026-06-04',
            'semester_id' => $this->tahunAkademik->id,
        ]));

        $this->assertDatabaseHas('learning_module_absensis', [
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->tahunAkademik->id,
            'murid_id' => $this->murid->id,
            'date' => '2026-06-04 00:00:00',
            'status' => 'alpa',
        ]);

        // Verify Fonnte API call was sent
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send' &&
                $request['target'] === '628123456789' &&
                str_contains($request['message'], 'Alpa');
        });
    });

    it('logs warning when Fonnte token is missing', function () {
        config(['services.fonnte.token' => 'your-fonnte-token-here']);
        Log::shouldReceive('warning')
            ->once()
            ->with('Fonnte token is not set. WhatsApp message not sent.', \Mockery::any());

        // Save attendance as 'alpa'
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.absensi.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'date' => '2026-06-04',
                'status' => [
                    $this->murid->id => 'alpa',
                ],
            ]);
    });

    it('logs error when Fonnte API call fails', function () {
        config(['services.fonnte.token' => 'valid-test-token']);
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false], 500),
        ]);
        Log::shouldReceive('error')
            ->once()
            ->with('Fonnte WhatsApp notification failed.', \Mockery::any());

        // Save attendance as 'alpa'
        $response = $this->actingAs($this->guruUser1)
            ->post(route('guru.learning-modules.absensi.store', $this->learningModule->id), [
                'semester_id' => $this->tahunAkademik->id,
                'date' => '2026-06-04',
                'status' => [
                    $this->murid->id => 'alpa',
                ],
            ]);
    });
});
