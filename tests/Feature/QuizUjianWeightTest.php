<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\LearningModule;
use App\Models\LearningModuleQuiz;
use App\Models\LearningModuleUjian;
use App\Models\BankSoal;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Quiz and Ujian Weight Validation', function () {
    beforeEach(function () {
        // Teacher
        $this->guruUser = User::factory()->create(['role' => 'guru']);
        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nip' => '1111111111111111',
        ]);

        // Academic prerequisites
        $this->academicYear = TahunAkademik::create([
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_akademik_id' => $this->academicYear->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->mataPelajaran = MataPelajaran::create([
            'kode_pelajaran' => 'MTK01',
            'nama_pelajaran' => 'Matematika Peminatan',
            'is_active' => true,
        ]);
        $this->mataPelajaran->gurus()->sync([$this->guru->id]);

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

        $this->learningModule = LearningModule::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tahun_akademik_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'title' => 'Aljabar Modul',
            'description' => 'Materi Aljabar.',
        ]);

        // Quiz and Ujian setup
        $this->quiz = LearningModuleQuiz::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->semester->id,
            'title' => 'Quiz 1',
            'instructions' => 'Instructions',
            'duration_minutes' => 30,
            'due_date' => '2026-06-10 23:59:00',
        ]);

        $this->ujian = LearningModuleUjian::create([
            'learning_module_id' => $this->learningModule->id,
            'semester_id' => $this->semester->id,
            'title' => 'Ujian Akhir',
            'instructions' => 'Instructions',
            'duration_minutes' => 90,
            'due_date' => '2026-06-10 23:59:00',
        ]);

        // Create questions
        $this->soal1 = BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Pertanyaan 1',
        ]);

        $this->soal2 = BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Pertanyaan 2',
        ]);

        // Attach questions
        $this->quiz->soals()->attach($this->soal1->id, ['urutan' => 1, 'bobot' => 50]);
        $this->quiz->soals()->attach($this->soal2->id, ['urutan' => 2, 'bobot' => 50]);

        $this->ujian->soals()->attach($this->soal1->id, ['urutan' => 1, 'bobot' => 50]);
        $this->ujian->soals()->attach($this->soal2->id, ['urutan' => 2, 'bobot' => 50]);
    });

    it('rejects quiz weights that do not sum to 100', function () {
        $response = $this->actingAs($this->guruUser)
            ->post(route('guru.learning-modules.quizzes.soals.order', [$this->learningModule->id, $this->quiz->id]), [
                'soals' => [
                    $this->soal1->id => ['urutan' => 1, 'bobot' => 40],
                    $this->soal2->id => ['urutan' => 2, 'bobot' => 50], // total = 90
                ],
            ]);

        $response->assertSessionHasErrors(['total_bobot']);
        
        // Pivot weights should remain unchanged
        expect($this->quiz->soals()->find($this->soal1->id)->pivot->bobot)->toBe(50);
        expect($this->quiz->soals()->find($this->soal2->id)->pivot->bobot)->toBe(50);
    });

    it('accepts quiz weights that sum to exactly 100', function () {
        $response = $this->actingAs($this->guruUser)
            ->post(route('guru.learning-modules.quizzes.soals.order', [$this->learningModule->id, $this->quiz->id]), [
                'soals' => [
                    $this->soal1->id => ['urutan' => 1, 'bobot' => 30],
                    $this->soal2->id => ['urutan' => 2, 'bobot' => 70], // total = 100
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Pivot weights should be updated
        expect($this->quiz->soals()->find($this->soal1->id)->pivot->bobot)->toBe(30);
        expect($this->quiz->soals()->find($this->soal2->id)->pivot->bobot)->toBe(70);
    });

    it('rejects ujian weights that do not sum to 100', function () {
        $response = $this->actingAs($this->guruUser)
            ->post(route('guru.learning-modules.ujians.soals.order', [$this->learningModule->id, $this->ujian->id]), [
                'soals' => [
                    $this->soal1->id => ['urutan' => 1, 'bobot' => 60],
                    $this->soal2->id => ['urutan' => 2, 'bobot' => 50], // total = 110
                ],
            ]);

        $response->assertSessionHasErrors(['total_bobot']);
        
        // Pivot weights should remain unchanged
        expect($this->ujian->soals()->find($this->soal1->id)->pivot->bobot)->toBe(50);
        expect($this->ujian->soals()->find($this->soal2->id)->pivot->bobot)->toBe(50);
    });

    it('accepts ujian weights that sum to exactly 100', function () {
        $response = $this->actingAs($this->guruUser)
            ->post(route('guru.learning-modules.ujians.soals.order', [$this->learningModule->id, $this->ujian->id]), [
                'soals' => [
                    $this->soal1->id => ['urutan' => 1, 'bobot' => 45],
                    $this->soal2->id => ['urutan' => 2, 'bobot' => 55], // total = 100
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Pivot weights should be updated
        expect($this->ujian->soals()->find($this->soal1->id)->pivot->bobot)->toBe(45);
        expect($this->ujian->soals()->find($this->soal2->id)->pivot->bobot)->toBe(55);
    });
});
