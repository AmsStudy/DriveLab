<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Generate slugs for existing records
        $soals = DB::table('soals')->orderBy('id')->get();
        foreach ($soals as $soal) {
            $baseSlug = Str::slug($soal->title) ?: 'dokumen-' . Str::lower(Str::random(5));
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('soals')->where('slug', $slug)->where('id', '!=', $soal->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            DB::table('soals')->where('id', $soal->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

