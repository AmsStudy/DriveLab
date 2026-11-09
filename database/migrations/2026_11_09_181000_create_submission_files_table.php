<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('submission_files')) {
            Schema::create('submission_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
                $table->string('file_path');
                $table->string('file_original_name');
                $table->string('file_extension')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->timestamps();
            });

            // Migrasikan berkas fisik dari tabel submissions yang sudah ada sebelumnya
            if (Schema::hasColumn('submissions', 'file_path')) {
                $existingSubmissions = DB::table('submissions')
                    ->whereNotNull('file_path')
                    ->where('file_path', '!=', '')
                    ->get();

                foreach ($existingSubmissions as $sub) {
                    DB::table('submission_files')->insert([
                        'submission_id'      => $sub->id,
                        'file_path'          => $sub->file_path,
                        'file_original_name' => $sub->file_original_name ?: basename($sub->file_path),
                        'file_extension'     => $sub->file_extension ?: pathinfo($sub->file_path, PATHINFO_EXTENSION),
                        'file_size'          => $sub->file_size ?: 0,
                        'created_at'         => $sub->created_at ?: now(),
                        'updated_at'         => $sub->updated_at ?: now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
