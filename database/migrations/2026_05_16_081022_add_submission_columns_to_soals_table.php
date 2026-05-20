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
            $table->boolean('is_submission_enabled')->default(false)->after('is_downloadable');
            $table->dateTime('deadline_at')->nullable()->after('is_submission_enabled');
            $table->text('submission_instructions')->nullable()->after('deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn(['is_submission_enabled', 'deadline_at', 'submission_instructions']);
        });
    }
};
