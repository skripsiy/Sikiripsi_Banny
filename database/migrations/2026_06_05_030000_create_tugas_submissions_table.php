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
        Schema::create('learning_module_tugas_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_module_tugas_id');
            $table->foreignId('murid_id')
                  ->constrained('murids')
                  ->cascadeOnDelete();
            $table->string('file_path');
            $table->text('catatan_murid')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();

            $table->foreign('learning_module_tugas_id', 'lmt_submissions_tugas_fk')
                  ->references('id')
                  ->on('learning_module_tugas')
                  ->cascadeOnDelete();

            $table->unique(['learning_module_tugas_id', 'murid_id'], 'tugas_murid_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_module_tugas_submissions');
    }
};
