<?php

use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\GuruMataPelajaran;
use App\Models\BankSoal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Bank Soal CRUD and Validation', function () {
    beforeEach(function () {
        // Create teacher user
        $this->guruUser = User::factory()->create(['role' => 'guru']);
        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nuptk' => '1234567890123456',
        ]);

        // Create subject
        $this->mataPelajaran = MataPelajaran::create([
            'kode_pelajaran' => 'MP001',
            'nama_pelajaran' => 'Matematika',
            'is_active' => true,
        ]);

        // Assign subject to teacher
        GuruMataPelajaran::create([
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
        ]);
    });

    it('denies access to non-guru users', function () {
        $user = User::factory()->create(['role' => 'murid']);
        $response = $this->actingAs($user)->get(route('guru.bank-soal.index'));
        $response->assertRedirect(route('dashboard'));
    });

    it('renders bank soal index for teacher', function () {
        $response = $this->actingAs($this->guruUser)->get(route('guru.bank-soal.index'));
        $response->assertOk();
    });

    it('validates and creates a new PG question successfully', function () {
        $response = $this->actingAs($this->guruUser)->post(route('guru.bank-soal.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Apa hasil dari 1 + 1?',
            'teks_opsi' => [
                'A' => '1',
                'B' => '2',
                'C' => '3',
                'D' => '4',
            ],
            'correct_option' => 'B',
            'pembahasan' => '1 + 1 = 2',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.bank-soal.index', ['mata_pelajaran_id' => $this->mataPelajaran->id]));

        $this->assertDatabaseHas('bank_soals', [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Apa hasil dari 1 + 1?',
            'pembahasan' => '1 + 1 = 2',
        ]);

        $soal = BankSoal::latest()->first();
        $this->assertCount(4, $soal->options);
        $this->assertTrue((bool)$soal->options->where('label', 'B')->first()->is_correct);
        $this->assertFalse((bool)$soal->options->where('label', 'A')->first()->is_correct);
    });

    it('validates and creates a new Essay question successfully with empty option fields', function () {
        // Submit options as empty/null which mirrors the frontend hidden form behaviour
        $response = $this->actingAs($this->guruUser)->post(route('guru.bank-soal.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Jelaskan teori relativitas secara singkat.',
            'teks_opsi' => [
                'A' => null,
                'B' => null,
                'C' => null,
                'D' => null,
            ],
            'correct_option' => null,
            'pembahasan' => 'Teori relativitas dikemukakan oleh Einstein.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('guru.bank-soal.index', ['mata_pelajaran_id' => $this->mataPelajaran->id]));

        $this->assertDatabaseHas('bank_soals', [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Jelaskan teori relativitas secara singkat.',
            'pembahasan' => 'Teori relativitas dikemukakan oleh Einstein.',
        ]);

        $soal = BankSoal::latest()->first();
        $this->assertCount(0, $soal->options);
    });

    it('fails validation when creating PG question without options', function () {
        $response = $this->actingAs($this->guruUser)->post(route('guru.bank-soal.store'), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Siapa presiden pertama Indonesia?',
            'teks_opsi' => [
                'A' => null,
                'B' => null,
                'C' => null,
                'D' => null,
            ],
            'correct_option' => null,
        ]);

        $response->assertSessionHasErrors(['teks_opsi.A', 'teks_opsi.B', 'teks_opsi.C', 'teks_opsi.D', 'correct_option']);
    });

    it('updates an existing PG question successfully', function () {
        $soal = BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Pertanyaan PG awal',
        ]);
        foreach (['A', 'B', 'C', 'D'] as $label) {
            $soal->options()->create([
                'label' => $label,
                'teks_opsi' => "Opsi $label awal",
                'is_correct' => $label === 'A',
            ]);
        }

        $response = $this->actingAs($this->guruUser)->put(route('guru.bank-soal.update', $soal->id), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'pertanyaan' => 'Pertanyaan PG baru',
            'teks_opsi' => [
                'A' => 'Opsi A baru',
                'B' => 'Opsi B baru',
                'C' => 'Opsi C baru',
                'D' => 'Opsi D baru',
            ],
            'correct_option' => 'C',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_soals', [
            'id' => $soal->id,
            'pertanyaan' => 'Pertanyaan PG baru',
        ]);

        $soal->refresh();
        $this->assertEquals('Opsi C baru', $soal->options->where('label', 'C')->first()->teks_opsi);
        $this->assertTrue((bool)$soal->options->where('label', 'C')->first()->is_correct);
        $this->assertFalse((bool)$soal->options->where('label', 'A')->first()->is_correct);
    });

    it('updates an existing Essay question successfully', function () {
        $soal = BankSoal::create([
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'guru_id' => $this->guru->id,
            'tipe' => 'essay',
            'pertanyaan' => 'Pertanyaan Essay awal',
        ]);

        $response = $this->actingAs($this->guruUser)->put(route('guru.bank-soal.update', $soal->id), [
            'mata_pelajaran_id' => $this->mataPelajaran->id,
            'pertanyaan' => 'Pertanyaan Essay baru',
            'teks_opsi' => [
                'A' => null,
                'B' => null,
                'C' => null,
                'D' => null,
            ],
            'correct_option' => null,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_soals', [
            'id' => $soal->id,
            'pertanyaan' => 'Pertanyaan Essay baru',
        ]);
    });
});
