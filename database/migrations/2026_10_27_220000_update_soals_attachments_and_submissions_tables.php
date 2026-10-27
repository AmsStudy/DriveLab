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
        // 1. Modifikasi tabel soals
        Schema::table('soals', function (Blueprint $table) {
            if (!Schema::hasColumn('soals', 'content_text')) {
                $table->longText('content_text')->nullable()->after('description');
            }
            if (!Schema::hasColumn('soals', 'submission_types')) {
                $table->json('submission_types')->nullable()->after('is_submission_enabled');
            }
        });

        // 2. Modifikasi tabel soal_attachments
        Schema::table('soal_attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('soal_attachments', 'type')) {
                $table->string('type', 20)->default('file')->after('soal_id'); // 'file', 'link', 'text'
            }
            if (!Schema::hasColumn('soal_attachments', 'link_url')) {
                $table->text('link_url')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('soal_attachments', 'content_text')) {
                $table->longText('content_text')->nullable()->after('link_url');
            }
            if (!Schema::hasColumn('soal_attachments', 'is_downloadable')) {
                $table->boolean('is_downloadable')->default(true)->after('content_text');
            }
            // Ubah file_path dan file_original_name agar nullable untuk tipe 'link' dan 'text'
            $table->string('file_path')->nullable()->change();
            $table->string('file_original_name')->nullable()->change();
        });

        // 3. Modifikasi tabel submissions
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'submission_type')) {
                $table->string('submission_type', 20)->default('file')->after('nama'); // 'file', 'link', 'text'
            }
            if (!Schema::hasColumn('submissions', 'link_url')) {
                $table->text('link_url')->nullable()->after('submission_type');
            }
            if (!Schema::hasColumn('submissions', 'text_content')) {
                $table->longText('text_content')->nullable()->after('link_url');
            }
            // Ubah file_path dan file_original_name agar nullable jika submission berupa link/text
            $table->string('file_path')->nullable()->change();
            $table->string('file_original_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'content_text')) {
                $table->dropColumn('content_text');
            }
            if (Schema::hasColumn('soals', 'submission_types')) {
                $table->dropColumn('submission_types');
            }
        });

        Schema::table('soal_attachments', function (Blueprint $table) {
            if (Schema::hasColumn('soal_attachments', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('soal_attachments', 'link_url')) {
                $table->dropColumn('link_url');
            }
            if (Schema::hasColumn('soal_attachments', 'content_text')) {
                $table->dropColumn('content_text');
            }
            if (Schema::hasColumn('soal_attachments', 'is_downloadable')) {
                $table->dropColumn('is_downloadable');
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'submission_type')) {
                $table->dropColumn('submission_type');
            }
            if (Schema::hasColumn('submissions', 'link_url')) {
                $table->dropColumn('link_url');
            }
            if (Schema::hasColumn('submissions', 'text_content')) {
                $table->dropColumn('text_content');
            }
        });
    }
};
