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
        Schema::create('soal_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_original_name');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        // Migrate existing data from soals table to soal_attachments
        $soals = DB::table('soals')->whereNotNull('file_path')->get();
        foreach ($soals as $soal) {
            DB::table('soal_attachments')->insert([
                'soal_id' => $soal->id,
                'file_path' => $soal->file_path,
                'file_original_name' => $soal->file_original_name,
                'file_type' => $soal->file_type,
                'file_size' => $soal->file_size,
                'created_at' => $soal->created_at,
                'updated_at' => $soal->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_attachments');
    }
};
