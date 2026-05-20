<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('unique_token', 64)->unique(); // untuk link unik
            $table->string('password'); // bcrypt hashed
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // admin pemilik
            $table->string('file_path')->nullable();
            $table->string('file_original_name')->nullable();
            $table->string('file_type')->nullable(); // pdf, docx, jpg, etc.
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->enum('access_type', ['lifetime', 'scheduled'])->default('lifetime');
            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_until')->nullable();
            $table->boolean('is_active')->default(true); // manual toggle
            $table->unsignedInteger('access_count')->default(0); // jumlah berhasil diakses
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
