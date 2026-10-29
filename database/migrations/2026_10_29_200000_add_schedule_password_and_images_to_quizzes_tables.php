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
        // 1. Tambah access_password, starts_at, ends_at ke tabel quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('access_password')->nullable()->after('access_code');
            $table->timestamp('starts_at')->nullable()->after('time_limit_minutes');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
        });

        // 2. Tambah image_path ke quiz_questions
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('question_text');
        });

        // 3. Tambah image_path ke quiz_options
        Schema::table('quiz_options', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('option_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['access_password', 'starts_at', 'ends_at']);
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('quiz_options', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
