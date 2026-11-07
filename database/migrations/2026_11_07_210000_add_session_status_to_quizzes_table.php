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
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'session_status')) {
                // waiting: Mahasiswa di ruang tunggu, admin belum menekan Mulai
                // started: Kuis sedang berlangsung, soal terbuka, timer berjalan
                // ended: Kuis dihentikan/selesai oleh pengawas
                $table->string('session_status', 20)->default('waiting')->after('is_active');
                $table->index(['is_active', 'session_status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'session_status')) {
                $table->dropIndex(['is_active', 'session_status']);
                $table->dropColumn('session_status');
            }
        });
    }
};

