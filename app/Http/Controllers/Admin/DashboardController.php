<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_soal'      => Soal::count(),
                'total_admin'     => User::where('role', 'admin')->count(),
                'total_akses'     => AccessLog::count(),
                'akses_hari_ini'  => AccessLog::whereDate('accessed_at', today())->count(),
                'akses_sukses'    => AccessLog::where('status', 'success')->count(),
                'soal_aktif'      => Soal::where('is_active', true)->count(),
                'soal_expired'    => Soal::where('access_type', 'scheduled')
                    ->where('active_until', '<', now())
                    ->where('is_active', true)
                    ->count(),
                'tugas_masuk'     => \App\Models\Submission::count(),
                'tugas_hari_ini'  => \App\Models\Submission::whereDate('created_at', today())->count(),
            ];

            $recent_logs    = AccessLog::with('soal')->latest('accessed_at')->take(10)->get();
            $recent_activities = Activity::latest()->take(10)->get();

            return view('admin.dashboard', compact('stats', 'recent_logs', 'recent_activities'));
        }

        // Admin biasa
        $stats = [
            'total_soal'     => Soal::where('user_id', $user->id)->count(),
            'soal_aktif'     => Soal::where('user_id', $user->id)->where('is_active', true)->count(),
            'total_akses'    => AccessLog::whereHas('soal', fn($q) => $q->where('user_id', $user->id))->count(),
            'akses_hari_ini' => AccessLog::whereHas('soal', fn($q) => $q->where('user_id', $user->id))
                ->whereDate('accessed_at', today())->count(),
            'tugas_masuk'    => \App\Models\Submission::whereHas('soal', fn($q) => $q->where('user_id', $user->id))->count(),
            'tugas_hari_ini' => \App\Models\Submission::whereHas('soal', fn($q) => $q->where('user_id', $user->id))
                ->whereDate('created_at', today())->count(),
        ];

        $recent_logs = AccessLog::whereHas('soal', fn($q) => $q->where('user_id', $user->id))
            ->with('soal')
            ->latest('accessed_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_logs'));
    }
}
