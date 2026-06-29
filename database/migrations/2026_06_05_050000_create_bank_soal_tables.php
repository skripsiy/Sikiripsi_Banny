<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bank Soal Table
        Schema::create('bank_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->enum('tipe', ['pg', 'essay']);
            $table->text('pertanyaan');
            $table->string('gambar_path')->nullable();
            $table->text('pembahasan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Bank Soal Options Table (for PG options)
        Schema::create('bank_soal_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->cascadeOnDelete();
            $table->string('label', 10); // A, B, C, D
            $table->text('teks_opsi');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // 3. Quiz Soals Table (pivot between quiz & bank_soals)
        Schema::create('quiz_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_quiz_id')
                  ->constrained('learning_module_quizzes', 'id', 'qz_soals_quiz_fk')
                  ->cascadeOnDelete();
            $table->foreignId('bank_soal_id')
                  ->constrained('bank_soals', 'id', 'qz_soals_soal_fk')
                  ->cascadeOnDelete();
            $table->integer('urutan')->default(0);
            $table->integer('bobot')->default(1);
            $table->timestamps();

            $table->unique(['learning_module_quiz_id', 'bank_soal_id'], 'qz_soals_quiz_soal_unique');
        });

        // 4. Ujian Soals Table (pivot between ujian & bank_soals)
        Schema::create('ujian_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_ujian_id')
                  ->constrained('learning_module_ujians', 'id', 'uj_soals_ujian_fk')
                  ->cascadeOnDelete();
            $table->foreignId('bank_soal_id')
                  ->constrained('bank_soals', 'id', 'uj_soals_soal_fk')
                  ->cascadeOnDelete();
            $table->integer('urutan')->default(0);
            $table->integer('bobot')->default(1);
            $table->timestamps();

            $table->unique(['learning_module_ujian_id', 'bank_soal_id'], 'uj_soals_ujian_soal_unique');
        });

        // 5. Quiz Attempts Table
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_quiz_id')
                  ->constrained('learning_module_quizzes', 'id', 'qz_att_quiz_fk')
                  ->cascadeOnDelete();
            $table->foreignId('murid_id')
                  ->constrained('murids', 'id', 'qz_att_murid_fk')
                  ->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->decimal('skor', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->timestamps();

            $table->unique(['learning_module_quiz_id', 'murid_id'], 'qz_att_quiz_murid_unique');
        });

        // 6. Quiz Answers Table
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')
                  ->constrained('quiz_attempts', 'id', 'qz_ans_att_fk')
                  ->cascadeOnDelete();
            $table->foreignId('bank_soal_id')
                  ->constrained('bank_soals', 'id', 'qz_ans_soal_fk')
                  ->cascadeOnDelete();
            $table->string('jawaban_pg', 10)->nullable();
            $table->text('jawaban_essay')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('skor_manual', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'bank_soal_id'], 'qz_ans_att_soal_unique');
        });

        // 7. Ujian Attempts Table
        Schema::create('ujian_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_ujian_id')
                  ->constrained('learning_module_ujians', 'id', 'uj_att_ujian_fk')
                  ->cascadeOnDelete();
            $table->foreignId('murid_id')
                  ->constrained('murids', 'id', 'uj_att_murid_fk')
                  ->cascadeOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->decimal('skor', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->timestamps();

            $table->unique(['learning_module_ujian_id', 'murid_id'], 'uj_att_ujian_murid_unique');
        });

        // 8. Ujian Answers Table
        Schema::create('ujian_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_attempt_id')
                  ->constrained('ujian_attempts', 'id', 'uj_ans_att_fk')
                  ->cascadeOnDelete();
            $table->foreignId('bank_soal_id')
                  ->constrained('bank_soals', 'id', 'uj_ans_soal_fk')
                  ->cascadeOnDelete();
            $table->string('jawaban_pg', 10)->nullable();
            $table->text('jawaban_essay')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('skor_manual', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['ujian_attempt_id', 'bank_soal_id'], 'uj_ans_att_soal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_answers');
        Schema::dropIfExists('ujian_attempts');
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('ujian_soals');
        Schema::dropIfExists('quiz_soals');
        Schema::dropIfExists('bank_soal_options');
        Schema::dropIfExists('bank_soals');
    }
};
