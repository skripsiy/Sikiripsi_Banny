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
        Schema::create('murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nisn')->nullable();
            $table->string('namaLengkap')->nullable();
            $table->date('tanggalLahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('noTelpon')->nullable();
            $table->string('namaOrangTua')->nullable();
            $table->string('no_telepon_orang_tua')->nullable();
            $table->string('class_room')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murids');
    }
};
