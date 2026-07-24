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
        Schema::table('learning_modules', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_modules', 'is_created_by_guru')) {
                $table->boolean('is_created_by_guru')->default(false)->after('description');
            }
        });

        // Set all existing learning modules to is_created_by_guru = true so legacy data remains active
        DB::table('learning_modules')->update(['is_created_by_guru' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            if (Schema::hasColumn('learning_modules', 'is_created_by_guru')) {
                $table->dropColumn('is_created_by_guru');
            }
        });
    }
};
