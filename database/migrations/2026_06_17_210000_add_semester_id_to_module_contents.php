<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add start_date and end_date to semesters
        Schema::table('semesters', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('semester');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // 2. Add semester_id to learning_module_materis
        Schema::table('learning_module_materis', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('learning_module_id')->constrained('semesters')->cascadeOnDelete();
        });

        // 3. Add semester_id to learning_module_tugas
        Schema::table('learning_module_tugas', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('learning_module_id')->constrained('semesters')->cascadeOnDelete();
        });

        // 4. Add semester_id to learning_module_quizzes
        Schema::table('learning_module_quizzes', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('learning_module_id')->constrained('semesters')->cascadeOnDelete();
        });

        // 5. Add semester_id to learning_module_ujians
        Schema::table('learning_module_ujians', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('learning_module_id')->constrained('semesters')->cascadeOnDelete();
        });

        // 6. Add semester_id to learning_module_absensis
        Schema::table('learning_module_absensis', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('learning_module_id')->constrained('semesters')->cascadeOnDelete();
        });

        // 7. Update unique constraint on learning_module_absensis
        Schema::table('learning_module_absensis', function (Blueprint $table) {
            $table->unique(['learning_module_id', 'murid_id', 'date', 'semester_id'], 'lm_murid_date_semester_unique');
        });
        Schema::table('learning_module_absensis', function (Blueprint $table) {
            $table->dropUnique('lm_murid_date_unique');
        });

        // 8. Populate semester_id for existing records based on parent learning module's tahun_akademik_id
        $this->populateDefaultSemesters();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_module_absensis', function (Blueprint $table) {
            $table->unique(['learning_module_id', 'murid_id', 'date'], 'lm_murid_date_unique');
        });

        Schema::table('learning_module_absensis', function (Blueprint $table) {
            $table->dropUnique('lm_murid_date_semester_unique');
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('learning_module_ujians', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('learning_module_quizzes', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('learning_module_tugas', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('learning_module_materis', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('semesters', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    private function populateDefaultSemesters(): void
    {
        $tables = [
            'learning_module_materis',
            'learning_module_tugas',
            'learning_module_quizzes',
            'learning_module_ujians',
            'learning_module_absensis'
        ];

        foreach ($tables as $table) {
            $records = DB::table($table)->get();
            foreach ($records as $record) {
                $module = DB::table('learning_modules')->where('id', $record->learning_module_id)->first();
                if ($module) {
                    $semester = DB::table('semesters')
                        ->where('tahun_akademik_id', $module->tahun_akademik_id)
                        ->where('semester', 'ganjil')
                        ->first();
                    if ($semester) {
                        DB::table($table)
                            ->where('id', $record->id)
                            ->update(['semester_id' => $semester->id]);
                    }
                }
            }
        }
    }
};
