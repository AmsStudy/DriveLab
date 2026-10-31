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
        Schema::table('soals', function (Blueprint $table) {
            if (!Schema::hasColumn('soals', 'classes')) {
                $table->json('classes')->nullable()->after('allowed_extensions');
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'kelas')) {
                $table->string('kelas', 50)->nullable()->after('nama');
                $table->index(['soal_id', 'kelas']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'kelas')) {
                $table->dropIndex(['soal_id', 'kelas']);
                $table->dropColumn('kelas');
            }
        });

        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'classes')) {
                $table->dropColumn('classes');
            }
        });
    }
};
