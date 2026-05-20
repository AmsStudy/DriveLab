<?php

use App\Models\Submission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateGroups = DB::table('submissions')
            ->select('soal_id', 'nim')
            ->groupBy('soal_id', 'nim')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $rows = Submission::where('soal_id', $group->soal_id)
                ->where('nim', $group->nim)
                ->orderByDesc('id')
                ->get();

            $rows->slice(1)->each(fn (Submission $submission) => $submission->delete());
        }

        Schema::table('submissions', function (Blueprint $table) {
            $table->unique(['soal_id', 'nim']);
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['soal_id', 'nim']);
        });
    }
};
