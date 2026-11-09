<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (!Schema::hasColumn('soals', 'max_files')) {
                $table->unsignedSmallInteger('max_files')->default(1)->after('submission_types');
            }
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'max_files')) {
                $table->dropColumn('max_files');
            }
        });
    }
};
