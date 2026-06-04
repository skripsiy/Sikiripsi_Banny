<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\LearningModule;
use App\Models\LearningModuleMateri;
use App\Models\LearningModuleTugas;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\LearningModuleAbsensi;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
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
        $this->tahunAjaran = TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

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
        $this->subjectGeneral = Subject::create([
            'kode_pelajaran' => 'INDO01',
            'nama_pelajaran' => 'Bahasa Indonesia',
            'jurusan_id' => null, // General
            'is_active' => true,
        ]);

        $this->subjectRpl = Subject::create([
            'kode_pelajaran' => 'RPL01',
            'nama_pelajaran' => 'Pemrograman Web',
            'jurusan_id' => $this->jurusanRpl->id,
            'is_active' => true,
        ]);

        $this->subjectTkj = Subject::create([
            'kode_pelajaran' => 'TKJ01',
            'nama_pelajaran' => 'Jaringan Dasar',
            'jurusan_id' => $this->jurusanTkj->id,
            'is_active' => true,
        ]);

        // Classrooms
        $this->classroomRpl = Classroom::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan_id' => $this->jurusanRpl->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
            'subject_id' => $this->subjectGeneral->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Bahasa Indonesia Modul',
            'description' => 'Materi umum.',
        ]);

        $this->moduleRpl = LearningModule::create([
            'guru_id' => $this->guru->id,
            'subject_id' => $this->subjectRpl->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'title' => 'Web Dev Modul',
            'description' => 'Materi RPL.',
        ]);

        $this->moduleTkj = LearningModule::create([
            'guru_id' => $this->guru->id,
            'subject_id' => $this->subjectTkj->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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
        $response->assertStatus(403);
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
            'title' => 'HTML Dasar',
            'content' => 'Pelajari sintaks HTML.',
        ]);

        $tugas = LearningModuleTugas::create([
            'learning_module_id' => $this->moduleRpl->id,
            'title' => 'Tugas Flexbox',
            'instructions' => 'Buat layout navbar.',
            'due_date' => now()->addDays(2),
        ]);

        $quiz = LearningModuleQuiz::create([
            'learning_module_id' => $this->moduleRpl->id,
            'title' => 'Quiz Tag HTML',
            'instructions' => 'Selesaikan kuis.',
            'duration_minutes' => 10,
            'due_date' => now()->addDays(1),
        ]);

        $ujian = LearningModuleUjian::create([
            'learning_module_id' => $this->moduleRpl->id,
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
            'murid_id' => $this->muridRpl->id,
            'date' => '2026-06-04',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->muridUserRpl)->get(route('murid.learning-modules.absensi.index', $this->moduleRpl->id));
        
        $response->assertOk();
        $response->assertViewIs('murid.learning_modules.absensi');
        $response->assertSee('Hadir');
    });
});
