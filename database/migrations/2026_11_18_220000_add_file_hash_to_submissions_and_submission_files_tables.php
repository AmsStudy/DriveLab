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
        if (Schema::hasTable('submissions') && !Schema::hasColumn('submissions', 'file_hash')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('file_hash', 64)->nullable()->after('file_size')->index();
            });
        }

        if (Schema::hasTable('submission_files') && !Schema::hasColumn('submission_files', 'file_hash')) {
            Schema::table('submission_files', function (Blueprint $table) {
                $table->string('file_hash', 64)->nullable()->after('file_size')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('submissions') && Schema::hasColumn('submissions', 'file_hash')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropIndex(['file_hash']);
                $table->dropColumn('file_hash');
            });
        }

        if (Schema::hasTable('submission_files') && Schema::hasColumn('submission_files', 'file_hash')) {
            Schema::table('submission_files', function (Blueprint $table) {
                $table->dropIndex(['file_hash']);
                $table->dropColumn('file_hash');
            });
        }
    }
};
