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
        Schema::table('submissions', function (Blueprint $table) {
            $table->index('nim');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['quiz_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['nim']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex(['quiz_id', 'started_at']);
        });
    }
};
