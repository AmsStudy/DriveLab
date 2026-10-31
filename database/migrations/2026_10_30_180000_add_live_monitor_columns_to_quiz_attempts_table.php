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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'extra_time_minutes')) {
                $table->integer('extra_time_minutes')->default(0)->after('duration_seconds');
            }
            if (!Schema::hasColumn('quiz_attempts', 'last_heartbeat_at')) {
                $table->timestamp('last_heartbeat_at')->nullable()->after('extra_time_minutes');
            }
            if (!Schema::hasColumn('quiz_attempts', 'is_force_submitted')) {
                $table->boolean('is_force_submitted')->default(false)->after('is_locked');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'extra_time_minutes')) {
                $table->dropColumn('extra_time_minutes');
            }
            if (Schema::hasColumn('quiz_attempts', 'last_heartbeat_at')) {
                $table->dropColumn('last_heartbeat_at');
            }
            if (Schema::hasColumn('quiz_attempts', 'is_force_submitted')) {
                $table->dropColumn('is_force_submitted');
            }
        });
    }
};
