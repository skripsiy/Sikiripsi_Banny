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
        Schema::table('gurus', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('murids', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('semesters', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('tahun_akademik_id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('admins')->onDelete('set null');
        });

        Schema::table('guru_mata_pelajaran', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_mata_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('semesters', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('murids', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
