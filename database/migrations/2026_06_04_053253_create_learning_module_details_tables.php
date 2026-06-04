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
        // 1. Materi Table
        Schema::create('learning_module_materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tugas Table
        Schema::create('learning_module_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions');
            $table->dateTime('due_date');
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Quizzes Table
        Schema::create('learning_module_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions');
            $table->integer('duration_minutes');
            $table->dateTime('due_date');
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Ujians Table
        Schema::create('learning_module_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions');
            $table->integer('duration_minutes');
            $table->dateTime('due_date');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Absensis Table
        Schema::create('learning_module_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murids')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa']);
            $table->timestamps();
            
            $table->unique(['learning_module_id', 'murid_id', 'date'], 'lm_murid_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_module_absensis');
        Schema::dropIfExists('learning_module_ujians');
        Schema::dropIfExists('learning_module_quizzes');
        Schema::dropIfExists('learning_module_tugas');
        Schema::dropIfExists('learning_module_materis');
    }
};
