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
            $table->boolean('allow_open_book')->default(false)->after('show_review');
            $table->string('admin_unlock_password')->nullable()->after('allow_open_book');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('tab_switch_count');
            $table->string('lock_reason')->nullable()->after('is_locked');
            $table->timestamp('penalty_end_time')->nullable()->after('lock_reason');
            $table->json('violations_log')->nullable()->after('penalty_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['allow_open_book', 'admin_unlock_password']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'lock_reason', 'penalty_end_time', 'violations_log']);
        });
    }
};
