<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
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
        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_soal'        => Soal::count(),
                'soal_aktif'        => Soal::where('is_active', true)->count(),
                'total_views'       => (int) Soal::sum('access_count'),
                'tugas_masuk'       => Submission::count(),
                'tugas_hari_ini'    => Submission::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'total_lampiran'    => SoalAttachment::count(),
                'total_admin'       => User::where('role', 'admin')->count(),
                'admin_pending'     => User::where('role', 'admin')->where('status', 'pending')->count(),
                'soal_expired'      => Soal::where('access_type', 'scheduled')
                    ->where('active_until', '<', now())
                    ->where('is_active', true)
                    ->count(),
                // Metrik Kuis CBT
                'total_quiz'        => Quiz::count(),
                'quiz_aktif'        => Quiz::where('is_active', true)->count(),
                'total_attempts'    => QuizAttempt::whereNotNull('completed_at')->count(),
                'avg_quiz_score'    => round((float) (QuizAttempt::whereNotNull('completed_at')->avg('score') ?? 0), 1),
            ];

            $recent_submissions = Submission::with('soal.category')
                ->latest()
                ->take(8)
                ->get();

            $recent_quiz_attempts = QuizAttempt::with('quiz')
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->take(6)
                ->get();

            $top_soals = Soal::with('category', 'admin')
                ->withCount('submissions')
                ->orderByDesc('access_count')
                ->take(5)
                ->get();

            $recent_activities = Activity::latest()->take(6)->get();

            return view('admin.dashboard', compact('stats', 'recent_submissions', 'recent_quiz_attempts', 'top_soals', 'recent_activities'));
        }

        // Admin / Asisten Biasa (Optimized using pre-fetched IDs instead of repeated whereHas subqueries)
        $soalIds = Soal::where('user_id', $user->id)->pluck('id');
        $quizIds = Quiz::where('user_id', $user->id)->pluck('id');

        $stats = [
            'total_soal'     => $soalIds->count(),
            'soal_aktif'     => Soal::where('user_id', $user->id)->where('is_active', true)->count(),
            'total_views'    => (int) Soal::where('user_id', $user->id)->sum('access_count'),
            'tugas_masuk'    => Submission::whereIn('soal_id', $soalIds)->count(),
            'tugas_hari_ini' => Submission::whereIn('soal_id', $soalIds)
                ->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'total_lampiran' => SoalAttachment::whereIn('soal_id', $soalIds)->count(),
            // Metrik Kuis CBT untuk Asisten ini
            'total_quiz'     => $quizIds->count(),
            'quiz_aktif'     => Quiz::where('user_id', $user->id)->where('is_active', true)->count(),
            'total_attempts' => QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('completed_at')->count(),
            'avg_quiz_score' => round((float) (QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('completed_at')->avg('score') ?? 0), 1),
        ];

        $recent_submissions = Submission::whereIn('soal_id', $soalIds)
            ->with('soal.category')
            ->latest()
            ->take(8)
            ->get();

        $recent_quiz_attempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->with('quiz')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take(6)
            ->get();

        $top_soals = Soal::where('user_id', $user->id)
            ->with('category')
            ->withCount('submissions')
            ->orderByDesc('access_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_submissions', 'recent_quiz_attempts', 'top_soals'));
    }
}
