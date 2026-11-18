<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (!Schema::hasColumn('soals', 'max_file_size_mb')) {
                $table->unsignedSmallInteger('max_file_size_mb')->default(3)->after('max_files');
            }
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'max_file_size_mb')) {
                $table->dropColumn('max_file_size_mb');
            }
        });
    }
};
