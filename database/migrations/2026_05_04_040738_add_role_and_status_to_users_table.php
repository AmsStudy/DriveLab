<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin'])->default('admin')->after('email');
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending')->after('role');
            $table->string('institution')->nullable()->after('status'); // nama institusi/lab
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'institution']);
        });
    }
};
