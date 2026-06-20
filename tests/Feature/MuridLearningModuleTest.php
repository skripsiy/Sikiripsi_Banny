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

uses(RefreshDatabase::class);

describe('Murid Learning Module Access', function () {
    beforeEach(function () {
        // Teacher 1
        $this->guruUser = User::factory()->create(['role' => 'guru']);
        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nuptk' => '1111111111111111',
        ]);

        // Setup Jurusan & Tahun Ajaran
        $this->tahunAkademik = Semester::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
        $this->academicYear = $this->tahunAkademik->tahunAkademik;

        $this->jurusanRpl = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $this->jurusanTkj = Jurusan::create([
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer Jaringan',
            'is_active' => true,
        ]);

        // Subjects
        $this->mataPelajaranGeneral = MataPelajaran::create([
            'kode_pelajaran' => 'INDO01',
            'nama_pelajaran' => 'Bahasa Indonesia',
            'jurusan_id' => null, // General
            'is_active' => true,
        ]);

        $this->mataPelajaranRpl = MataPelajaran::create([
            'kode_pelajaran' => 'RPL01',
            'nama_pelajaran' => 'Pemrograman Web',
            'jurusan_id' => $this->jurusanRpl->id,
            'is_active' => true,
        ]);

        $this->mataPelajaranTkj = MataPelajaran::create([
            'kode_pelajaran' => 'TKJ01',
            'nama_pelajaran' => 'Jaringan Dasar',
            'jurusan_id' => $this->jurusanTkj->id,
            'is_active' => true,
        ]);

        // Classrooms
        $this->classroomRpl = Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusanRpl->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'is_active' => true,
        ]);

        // Student in RPL
        $this->muridUserRpl = User::factory()->create(['role' => 'murid']);
        $this->muridRpl = Murid::create([
            'user_id' => $this->muridUserRpl->id,
            'nisn' => '1234567890',
            'classroom_id' => $this->classroomRpl->id,
            'no_telepon_orang_tua' => '628123456789',
        ]);

        // Modules
        $this->moduleGeneral = LearningModule::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaranGeneral->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Bahasa Indonesia Modul',
            'description' => 'Materi umum.',
        ]);

        $this->moduleRpl = LearningModule::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaranRpl->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Web Dev Modul',
            'description' => 'Materi RPL.',
        ]);

        $this->moduleTkj = LearningModule::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaranTkj->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'title' => 'Cisco Jaringan Modul',
            'description' => 'Materi TKJ.',
        ]);
    });

    it('denies access to guest users', function () {
        $response = $this->get(route('murid.learning-modules.index'));
        $response->assertRedirect(route('login'));
    });

    it('denies access to non-murid users', function () {
        $response = $this->actingAs($this->guruUser)->get(route('murid.learning-modules.index'));
        $response->assertRedirect(route('dashboard'));
    });

    it('allows student to view list of matching learning modules', function () {
        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.index'));
        
        $response->assertOk();
        $response->assertViewIs('murid.learning_modules.index');
        
        // Student should see general and RPL modules
        $response->assertSee('Bahasa Indonesia Modul');
        $response->assertSee('Web Dev Modul');
        
        // Student should NOT see TKJ modules
        $response->assertDontSee('Cisco Jaringan Modul');
    });

    it('allows student to view module overview details', function () {
        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.show', $this->moduleRpl->id));
        
        $response->assertOk();
        $response->assertViewIs('murid.learning_modules.show');
        $response->assertSee('Web Dev Modul');
    });

    it('denies student to view modules of different jurusan classes', function () {
        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.show', $this->moduleTkj->id));
        
        $response->assertStatus(403);
    });

    it('allows student to view sub-resources lists', function () {
        // Create study contents
        $materi = LearningModuleMateri::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'HTML Dasar',
            'content' => 'Pelajari sintaks HTML.',
        ]);

        $tugas = LearningModuleTugas::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Tugas Flexbox',
            'instructions' => 'Buat layout navbar.',
            'due_date' => now()->addDays(2),
        ]);

        $quiz = LearningModuleQuiz::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Quiz Tag HTML',
            'instructions' => 'Selesaikan kuis.',
            'duration_minutes' => 10,
            'due_date' => now()->addDays(1),
        ]);

        $ujian = LearningModuleUjian::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Ujian Akhir Semester Web',
            'instructions' => 'Ujian teori web.',
            'duration_minutes' => 95,
            'due_date' => now()->addDays(5),
        ]);

        // Test Materi view
        $responseMateri = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.materis.index', $this->moduleRpl->id));
        $responseMateri->assertOk();
        $responseMateri->assertSee('HTML Dasar');

        // Test Tugas view
        $responseTugas = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.tugas.index', $this->moduleRpl->id));
        $responseTugas->assertOk();
        $responseTugas->assertSee('Tugas Flexbox');

        // Test Quiz view
        $responseQuiz = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.quizzes.index', $this->moduleRpl->id));
        $responseQuiz->assertOk();
        $responseQuiz->assertSee('Quiz Tag HTML');

        // Test Ujian view
        $responseUjian = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.ujians.index', $this->moduleRpl->id));
        $responseUjian->assertOk();
        $responseUjian->assertSee('Ujian Akhir Semester Web');
    });

    it('allows student to view their own attendance log', function () {
        // Create some attendance log
        LearningModuleAbsensi::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'murid_id' => $this->muridRpl->id,
            'date' => '2026-06-04',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.absensi.index', $this->moduleRpl->id));
        
        $response->assertOk();
        $response->assertViewIs('murid.learning_modules.absensi.index');
        $response->assertSee('Hadir');
    });

    it('autosaves quiz answers and pre-loads them on page refresh', function () {
        $quiz = LearningModuleQuiz::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Quiz Tag HTML',
            'instructions' => 'Selesaikan kuis.',
            'duration_minutes' => 10,
            'due_date' => now()->addDays(1),
        ]);

        $soal = \App\Models\BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaranRpl->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Apa tag untuk link?',
        ]);

        $soal->options()->create([
            'label' => 'A',
            'teks_opsi' => '<a>',
            'is_correct' => true,
        ]);
        $soal->options()->create([
            'label' => 'B',
            'teks_opsi' => '<p>',
            'is_correct' => false,
        ]);

        $quiz->soals()->attach($soal->id, ['urutan' => 1, 'bobot' => 5]);

        // Start Quiz
        $responseStart = $this->actingAs($this->muridUserRpl)->post(route('murid.learning-modules.quizzes.start', [$this->moduleRpl->id, $quiz->id]));
        $responseStart->assertRedirect(route('murid.learning-modules.quizzes.take', [$this->moduleRpl->id, $quiz->id]));

        // Autosave answer via AJAX
        $responseSave = $this->actingAs($this->muridUserRpl)->postJson(route('murid.learning-modules.quizzes.save-answer', [$this->moduleRpl->id, $quiz->id]), [
            'bank_soal_id' => $soal->id,
            'tipe' => 'pg',
            'nilai' => 'A',
        ]);
        $responseSave->assertOk();
        $responseSave->assertJson(['success' => true]);

        $this->assertDatabaseHas('quiz_answers', [
            'bank_soal_id' => $soal->id,
            'jawaban_pg' => 'A',
        ]);

        // Refresh take page and verify existingAnswers contains the saved answer
        $responseTake = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.quizzes.take', [$this->moduleRpl->id, $quiz->id]));
        $responseTake->assertOk();
        $responseTake->assertViewHas('existingAnswers', [
            $soal->id => 'A',
        ]);
    });

    it('autosaves exam answers and pre-loads them on page refresh', function () {
        $ujian = LearningModuleUjian::create([
            'learning_module_id' => $this->moduleRpl->id,
            'semester_id' => $this->tahunAkademik->id,
            'title' => 'Ujian Akhir',
            'instructions' => 'Selesaikan ujian.',
            'duration_minutes' => 90,
            'due_date' => now()->addDays(2),
        ]);

        $soal = \App\Models\BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaranRpl->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Jelaskan konsep OOP.',
        ]);

        $ujian->soals()->attach($soal->id, ['urutan' => 1, 'bobot' => 10]);

        // Start Ujian
        $responseStart = $this->actingAs($this->muridUserRpl)->post(route('murid.learning-modules.ujians.start', [$this->moduleRpl->id, $ujian->id]));
        $responseStart->assertRedirect(route('murid.learning-modules.ujians.take', [$this->moduleRpl->id, $ujian->id]));

        // Autosave answer via AJAX
        $responseSave = $this->actingAs($this->muridUserRpl)->postJson(route('murid.learning-modules.ujians.save-answer', [$this->moduleRpl->id, $ujian->id]), [
            'bank_soal_id' => $soal->id,
            'tipe' => 'essay',
            'nilai' => 'OOP adalah Pemrograman Berorientasi Objek.',
        ]);
        $responseSave->assertOk();
        $responseSave->assertJson(['success' => true]);

        $this->assertDatabaseHas('ujian_answers', [
            'bank_soal_id' => $soal->id,
            'jawaban_essay' => 'OOP adalah Pemrograman Berorientasi Objek.',
        ]);

        // Refresh take page and verify existingAnswers contains the saved answer
        $responseTake = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.ujians.take', [$this->moduleRpl->id, $ujian->id]));
        $responseTake->assertOk();
        $responseTake->assertViewHas('existingAnswers', [
            $soal->id => 'OOP adalah Pemrograman Berorientasi Objek.',
        ]);
    });

    it('allows student to view their permission requests index', function () {
        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.izin.index', $this->moduleRpl->id));
        $response->assertOk();
        $response->assertViewIs('murid.learning_modules.izin.index');
        $response->assertSee('Riwayat Permohonan Izin');
    });

    it('redirects the student from create route to index with open modal session', function () {
        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.izin.create', $this->moduleRpl->id));
        $response->assertRedirect(route('murid.learning-modules.izin.index', $this->moduleRpl->id));
        $response->assertSessionHas('open_create_modal', true);
    });

    it('saves a permission request successfully when valid data and file is provided', function () {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->muridUserRpl)->post(route('murid.learning-modules.izin.store', $this->moduleRpl->id), [
            'date' => '2026-06-15',
            'jenis_izin' => 'sakit',
            'alasan' => 'Demam tinggi.',
            'file' => $file,
        ]);

        $response->assertRedirect(route('murid.learning-modules.izin.index', $this->moduleRpl->id));
        $response->assertSessionHas('status', 'Permohonan izin berhasil dikirim.');

        $this->assertDatabaseHas('izin_requests', [
            'learning_module_id' => $this->moduleRpl->id,
            'murid_id' => $this->muridRpl->id,
            'date' => '2026-06-15 00:00:00',
            'jenis_izin' => 'sakit',
            'alasan' => 'Demam tinggi.',
            'status' => 'pending',
        ]);
    });

    it('fails validation when uploading invalid file type or large file', function () {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.txt', 100);

        $response = $this->actingAs($this->muridUserRpl)->post(route('murid.learning-modules.izin.store', $this->moduleRpl->id), [
            'date' => '2026-06-15',
            'jenis_izin' => 'sakit',
            'alasan' => 'Demam tinggi.',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
    });
});

