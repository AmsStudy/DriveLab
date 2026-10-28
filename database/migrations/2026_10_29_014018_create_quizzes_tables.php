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
        // 1. Tabel Kuis
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('soal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('access_code', 20)->unique();
            $table->string('unique_token', 20)->unique();
            $table->integer('time_limit_minutes')->default(15); // 0 = tanpa batas
            $table->integer('time_per_question')->default(0);    // 0 = per-soal timer off
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_leaderboard')->default(true);
            $table->boolean('show_review')->default(true);
            $table->integer('max_attempts')->default(1);
            $table->timestamps();
        });

        // 2. Butir Soal Kuis
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type', 30)->default('multiple_choice'); // multiple_choice, true_false, multi_select, short_answer
            $table->integer('points')->default(10);
            $table->text('explanation')->nullable();
            $table->integer('order_num')->default(1);
            $table->timestamps();
        });

        // 3. Opsi Pilihan Jawaban
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order_num')->default(1);
            $table->timestamps();
        });

        // 4. Sesi Pengerjaan Mahasiswa
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('nim', 50);
            $table->string('student_name', 255);
            $table->decimal('score', 5, 2)->default(0); // 0.00 s/d 100.00
            $table->integer('total_points_earned')->default(0);
            $table->integer('total_points_possible')->default(0);
            $table->integer('correct_answers_count')->default(0);
            $table->integer('wrong_answers_count')->default(0);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('tab_switch_count')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'nim']);
            $table->index(['quiz_id', 'score']);
        });

        // 5. Detail Jawaban Mahasiswa
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('quiz_options')->nullOnDelete();
            $table->json('selected_option_ids')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('points_earned')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
