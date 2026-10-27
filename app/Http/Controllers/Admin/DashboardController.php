<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\SoalAttachment;
use App\Models\Submission;
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
                'total_soal'        => Soal::count(),
                'soal_aktif'        => Soal::where('is_active', true)->count(),
                'total_views'       => (int) Soal::sum('access_count'),
                'tugas_masuk'       => Submission::count(),
                'tugas_hari_ini'    => Submission::whereDate('created_at', today())->count(),
                'total_lampiran'    => SoalAttachment::count(),
                'total_admin'       => User::where('role', 'admin')->count(),
                'admin_pending'     => User::where('role', 'admin')->where('status', 'pending')->count(),
                'soal_expired'      => Soal::where('access_type', 'scheduled')
                    ->where('active_until', '<', now())
                    ->where('is_active', true)
                    ->count(),
            ];

            $recent_submissions = Submission::with('soal.category')
                ->latest()
                ->take(8)
                ->get();

            $top_soals = Soal::with('category', 'admin')
                ->withCount('submissions')
                ->orderByDesc('access_count')
                ->take(5)
                ->get();

            $recent_activities = Activity::latest()->take(6)->get();

            return view('admin.dashboard', compact('stats', 'recent_submissions', 'top_soals', 'recent_activities'));
        }

        // Admin / Asisten Biasa
        $stats = [
            'total_soal'     => Soal::where('user_id', $user->id)->count(),
            'soal_aktif'     => Soal::where('user_id', $user->id)->where('is_active', true)->count(),
            'total_views'    => (int) Soal::where('user_id', $user->id)->sum('access_count'),
            'tugas_masuk'    => Submission::whereHas('soal', fn($q) => $q->where('user_id', $user->id))->count(),
            'tugas_hari_ini' => Submission::whereHas('soal', fn($q) => $q->where('user_id', $user->id))
                ->whereDate('created_at', today())->count(),
            'total_lampiran' => SoalAttachment::whereHas('soal', fn($q) => $q->where('user_id', $user->id))->count(),
        ];

        $recent_submissions = Submission::whereHas('soal', fn($q) => $q->where('user_id', $user->id))
            ->with('soal.category')
            ->latest()
            ->take(8)
            ->get();

        $top_soals = Soal::where('user_id', $user->id)
            ->with('category')
            ->withCount('submissions')
            ->orderByDesc('access_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_submissions', 'top_soals'));
    }
}
