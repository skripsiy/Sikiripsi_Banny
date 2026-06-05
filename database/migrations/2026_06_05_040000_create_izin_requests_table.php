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
        Schema::create('izin_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')
                  ->constrained('learning_modules')
                  ->cascadeOnDelete();
            $table->foreignId('murid_id')
                  ->constrained('murids')
                  ->cascadeOnDelete();
            $table->date('date');
            $table->enum('jenis_izin', ['sakit', 'izin']);
            $table->text('alasan');
            $table->string('bukti_file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_guru')->nullable();
            $table->timestamps();

            $table->unique(['learning_module_id', 'murid_id', 'date'], 'izin_murid_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_requests');
    }
};
