<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PruneExpiredDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan sesi kadaluarsa, cache basi, dan token usang secara otomatis agar database tetap ramping dan cepat';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pembersihan data sementara & kadaluarsa...');

        // 1. Bersihkan Tabel Sessions (sesi yang sudah lewat dari session lifetime)
        $sessionDeleted = 0;
        if (Schema::hasTable('sessions')) {
            $lifetimeMinutes = (int) config('session.lifetime', 120);
            $cutoffTimestamp = now()->subMinutes($lifetimeMinutes)->timestamp;
            $sessionDeleted = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTimestamp)
                ->delete();
            $this->line("• Sesi kadaluarsa dibersihkan: {$sessionDeleted} baris");
        }

        // 2. Bersihkan Tabel Cache (data cache yang sudah expired)
        $cacheDeleted = 0;
        if (Schema::hasTable('cache')) {
            $nowTimestamp = now()->timestamp;
            $cacheDeleted = DB::table('cache')
                ->where('expiration', '<', $nowTimestamp)
                ->delete();
            $this->line("• Cache basi dibersihkan: {$cacheDeleted} baris");
        }

        // 3. Bersihkan Token Reset Password Usang (> 2 jam)
        $tokenDeleted = 0;
        if (Schema::hasTable('password_reset_tokens')) {
            $cutoffDate = now()->subHours(2);
            $tokenDeleted = DB::table('password_reset_tokens')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            $this->line("• Token reset usang dibersihkan: {$tokenDeleted} baris");
        }

        $total = $sessionDeleted + $cacheDeleted + $tokenDeleted;
        $this->info("Pembersihan selesai! Total {$total} baris sampah berhasil dihapus dari database.");

        return Command::SUCCESS;
    }
}
