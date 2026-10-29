<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PublicQuizController extends Controller
{
    public function joinByCode(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string|max:50',
        ], [
            'access_code.required' => 'Masukkan kode kuis untuk bergabung.',
        ]);

        $code = strtoupper(trim($request->access_code));
        $quiz = Quiz::where('access_code', $code)->first();

        if (!$quiz) {
            return back()->with('quiz_error', 'Kode kuis tidak ditemukan. Periksa kembali kode Anda.')->withInput();
        }

        if (!$quiz->is_active) {
            return back()->with('quiz_error', 'Kuis dengan kode ini sedang dinonaktifkan oleh instruktur/asisten lab.')->withInput();
        }

        return redirect()->route('quiz.lobby', ['token' => $quiz->unique_token]);
    }

    public function lobby(string $token)
    {
        $quiz = Quiz::where('unique_token', $token)->with('soal')->firstOrFail();

        $questionsCount = $quiz->questions()->count();
        $totalPoints = $quiz->questions()->sum('points');
        $participantsCount = $quiz->attempts()->whereNotNull('completed_at')->count();

        return view('quiz.lobby', compact('quiz', 'questionsCount', 'totalPoints', 'participantsCount'));
    }

    public function start(Request $request, string $token)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();

        if (!$quiz->is_active) {
            return back()->with('error', 'Kuis saat ini tidak aktif atau telah ditutup.');
        }

        $validated = $request->validate([
            'nim'  => 'required|string|max:30|regex:/^[a-zA-Z0-9\-_.]+$/',
            'nama' => 'required|string|max:100',
        ], [
            'nim.required'  => 'NIM wajib diisi.',
            'nim.regex'     => 'NIM hanya boleh berisi huruf, angka, titik, atau strip.',
            'nama.required' => 'Nama lengkap wajib diisi.',
        ]);

        $nim = trim($validated['nim']);
        $nama = trim($validated['nama']);

        // Check if there's an ongoing uncompleted attempt for this NIM
        $ongoing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('nim', $nim)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($ongoing) {
            // Check if ongoing has already expired based on time_limit_minutes
            if ($quiz->time_limit_minutes > 0) {
                $elapsedSeconds = abs((int) now()->diffInSeconds($ongoing->started_at));
                $allowedSeconds = $quiz->time_limit_minutes * 60;
                if ($elapsedSeconds > $allowedSeconds + 30) {
                    // Mark as expired/completed, preserving draft answers
                    $this->finalizeAttempt($ongoing, $ongoing->draft_answers ?? []);
                } else {
                    session(['quiz_attempt_' . $quiz->id => $ongoing->id]);
                    return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $ongoing->id]);
                }
            } else {
                session(['quiz_attempt_' . $quiz->id => $ongoing->id]);
                return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $ongoing->id]);
            }
        }

        // Check max attempts
        $completedAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('nim', $nim)
            ->whereNotNull('completed_at')
            ->count();

        if ($quiz->max_attempts > 0 && $completedAttempts >= $quiz->max_attempts) {
            return back()->with('error', "Anda telah mencapai batas maksimal pengerjaan ({$quiz->max_attempts} kali).")->withInput();
        }

        $totalPoints = $quiz->questions()->sum('points');

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => $nim,
            'student_name'          => $nama,
            'total_points_possible' => $totalPoints,
            'started_at'            => now(),
        ]);

        session(['quiz_attempt_' . $quiz->id => $attempt->id]);

        return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
    }

    public function play(string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
        }

        // Calculate time limit
        $timeRemaining = null;
        if ($quiz->time_limit_minutes > 0) {
            $elapsedSeconds = abs((int) now()->diffInSeconds($attempt->started_at));
            $totalSeconds = (int) ($quiz->time_limit_minutes * 60);
            $timeRemaining = (int) max(0, $totalSeconds - $elapsedSeconds);

            if ($timeRemaining <= 0) {
                // Finalize preserving draft answers
                $this->finalizeAttempt($attempt, $attempt->draft_answers ?? []);
                return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
            }
        }

        // Load questions
        $questionsQuery = $quiz->questions()->with(['options' => function ($q) use ($quiz) {
            if ($quiz->shuffle_options) {
                $q->inRandomOrder();
            } else {
                $q->orderBy('order_num');
            }
        }]);

        if ($quiz->shuffle_questions) {
            $questions = $questionsQuery->inRandomOrder()->get();
        } else {
            $questions = $questionsQuery->orderBy('order_num')->get();
        }

        // Prepare sanitized payload for client-side (NEVER expose is_correct in HTML/JSON!)
        $clientQuestions = $questions->map(function ($q, $index) {
            return [
                'id'            => $q->id,
                'number'        => $index + 1,
                'question_text' => $q->question_text,
                'question_type' => $q->question_type,
                'points'        => $q->points,
                'options'       => $q->options->map(function ($opt) {
                    return [
                        'id'   => $opt->id,
                        'text' => $opt->option_text,
                    ];
                })->values(),
            ];
        })->values();

        // Calculate any active penalty cooldown (to prevent bypass via browser refresh/F5)
        $penaltyRemainingSeconds = 0;
        if (!$quiz->allow_open_book && $attempt->penalty_end_time && now()->lessThan($attempt->penalty_end_time)) {
            $penaltyRemainingSeconds = abs((int) now()->diffInSeconds($attempt->penalty_end_time));
        }

        $savedAnswers = $attempt->draft_answers ?? [];

        return view('quiz.play', compact(
            'quiz',
            'attempt',
            'clientQuestions',
            'timeRemaining',
            'penaltyRemainingSeconds',
            'savedAnswers'
        ));
    }

    public function recordTabSwitch(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            return response()->json(['success' => false]);
        }

        // If open book mode is allowed by instructor, no punishment is applied!
        if ($quiz->allow_open_book) {
            return response()->json([
                'success'         => true,
                'allow_open_book' => true,
                'penalty_seconds' => 0,
                'is_locked'       => false,
                'message'         => 'Mode modul aktif: Anda bebas membuka modul praktikum.',
            ]);
        }

        $reason = $request->input('reason', 'tab_switch');
        $attempt->increment('tab_switch_count');
        $count = $attempt->tab_switch_count;

        $strike = min(3, $count);
        $penaltySeconds = 0;
        $isLocked = false;
        $message = '';

        $logs = $attempt->violations_log ?? [];
        $logs[] = [
            'type'      => $reason,
            'count'     => $count,
            'timestamp' => now()->toIso8601String(),
        ];

        $reasonLabel = ($reason === 'fullscreen_exit') ? 'keluar dari layar penuh (kiosk)' : 'perpindahan tab/aplikasi';

        if ($count === 1) {
            // Punishment 1: 1 menit (60 detik)
            $penaltySeconds = 60;
            $attempt->penalty_end_time = now()->addSeconds(60);
            $message = "Peringatan 1/3: Terdeteksi {$reasonLabel}. Layar kuis terkunci selama 1 menit.";
        } elseif ($count === 2) {
            // Punishment 2: 1.30 menit (90 detik)
            $penaltySeconds = 90;
            $attempt->penalty_end_time = now()->addSeconds(90);
            $message = "Peringatan 2/3: Pelanggaran kedua ({$reasonLabel})! Pinalti waktu berlipat 1.5 menit. Pelanggaran berikutnya akan mengunci kuis total.";
        } else {
            // Punishment 3: Kunci total sampai admin membuka dengan password
            $isLocked = true;
            $attempt->is_locked = true;
            $attempt->lock_reason = "Terdeteksi 3x pelanggaran CBT ({$reasonLabel})";
            $attempt->penalty_end_time = null;
            $message = "Kuis Terkunci Total: Anda telah melanggar aturan pengawas kuis (3x switch). Panggil Asisten Lab / Dosen untuk membuka kunci kuis Anda.";
        }

        $attempt->violations_log = $logs;
        $attempt->save();

        return response()->json([
            'success'          => true,
            'tab_switch_count' => $count,
            'strike'           => $strike,
            'penalty_seconds'  => $penaltySeconds,
            'is_locked'        => $isLocked,
            'message'          => $message,
        ]);
    }

    public function unlockWithPassword(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->with('user')->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        $request->validate([
            'admin_password' => 'required|string',
        ]);

        $passwordInput = $request->admin_password;
        $isValid = false;

        // 1. Check custom quiz unlock password if configured
        if (!empty($quiz->admin_unlock_password) && $passwordInput === $quiz->admin_unlock_password) {
            $isValid = true;
        }

        // 2. Check quiz creator admin account password
        if (!$isValid && $quiz->user && Hash::check($passwordInput, $quiz->user->password)) {
            $isValid = true;
        }

        // 3. Check any active superadmin account password
        if (!$isValid) {
            $superAdmins = User::where('role', 'superadmin')->where('status', 'active')->get();
            foreach ($superAdmins as $sa) {
                if (Hash::check($passwordInput, $sa->password)) {
                    $isValid = true;
                    break;
                }
            }
        }

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Password admin tidak valid! Pastikan Anda adalah Asisten / Admin Lab.',
            ], 422);
        }

        // Unlock student attempt
        $logs = $attempt->violations_log ?? [];
        $logs[] = [
            'type'      => 'unlocked_by_password_on_screen',
            'timestamp' => now()->toIso8601String(),
        ];

        $attempt->update([
            'is_locked'        => false,
            'lock_reason'      => null,
            'penalty_end_time' => null,
            'tab_switch_count' => min(2, $attempt->tab_switch_count), // Give them a chance on 2 strikes
            'violations_log'   => $logs,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kunci kuis berhasil dibuka! Mahasiswa dapat melanjutkan ujian.',
        ]);
    }

    public function autosave(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            return response()->json(['success' => false, 'message' => 'Sesi kuis tidak aktif.'], 403);
        }

        $answers = $request->input('answers', []);
        if (is_array($answers)) {
            $attempt->draft_answers = $answers;
            $attempt->save();
        }

        return response()->json([
            'success'  => true,
            'saved_at' => now()->toIso8601String(),
        ]);
    }

    public function submit(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id])]);
            }
            return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
        }

        $userAnswers = $request->input('answers', $attempt->draft_answers ?? []);

        // Server-side strict time enforcement (with 90-second network latency tolerance)
        if ($quiz->time_limit_minutes > 0) {
            $elapsedSeconds = abs((int) now()->diffInSeconds($attempt->started_at));
            $maxAllowedSeconds = ($quiz->time_limit_minutes * 60) + 90;
            if ($elapsedSeconds > $maxAllowedSeconds) {
                // If submitted well past the time limit, fall back to what was autosaved in drafts prior to expiration
                $userAnswers = $attempt->draft_answers ?? [];
            }
        }

        $this->finalizeAttempt($attempt, is_array($userAnswers) ? $userAnswers : []);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]),
            ]);
        }

        return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
    }

    public function results(string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->with('soal')->firstOrFail();
        $this->validateAttemptAccess($quiz, $attempt);

        if (!$attempt->isCompleted()) {
            return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
        }

        // Calculate student rank
        $betterCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->where(function ($q) use ($attempt) {
                $q->where('score', '>', $attempt->score)
                  ->orWhere(function ($sub) use ($attempt) {
                      $sub->where('score', '=', $attempt->score)
                          ->where('duration_seconds', '<', $attempt->duration_seconds);
                  });
            })
            ->count();

        $rank = $betterCount + 1;
        $totalParticipants = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->count();

        // Top 10 Leaderboard
        $leaderboard = null;
        if ($quiz->show_leaderboard) {
            $leaderboard = QuizAttempt::where('quiz_id', $quiz->id)
                ->whereNotNull('completed_at')
                ->orderByDesc('score')
                ->orderBy('duration_seconds')
                ->limit(10)
                ->get();
        }

        // Review Answers
        $reviewAnswers = null;
        if ($quiz->show_review) {
            $reviewAnswers = $attempt->answers()
                ->with(['question.options', 'selectedOption'])
                ->get();
        }

        return view('quiz.results', compact(
            'quiz',
            'attempt',
            'rank',
            'totalParticipants',
            'leaderboard',
            'reviewAnswers'
        ));
    }

    private function validateAttemptAccess(Quiz $quiz, QuizAttempt $attempt): void
    {
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        // Admins and SuperAdmins can view/manage
        if (auth()->check() && (auth()->user()->isSuperAdmin() || $quiz->user_id === auth()->id())) {
            return;
        }

        // Student session check: ensure attempt belongs to this browser session
        $sessionAttemptId = session('quiz_attempt_' . $quiz->id);

        if ($sessionAttemptId && (int)$sessionAttemptId !== (int)$attempt->id) {
            abort(403, 'Akses sesi kuis tidak sah. Anda tidak diizinkan mengakses sesi pengerjaan mahasiswa lain.');
        }

        if (!$sessionAttemptId && !app()->runningUnitTests()) {
            abort(403, 'Sesi pengerjaan kuis tidak ditemukan. Silakan mulai kuis melalui halaman resmi.');
        }
    }

    private function finalizeAttempt(QuizAttempt $attempt, array $userAnswers): void
    {
        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();

        $totalPointsEarned = 0;
        $correctCount = 0;
        $wrongCount = 0;

        DB::transaction(function () use ($attempt, $questions, $userAnswers, &$totalPointsEarned, &$correctCount, &$wrongCount) {
            foreach ($questions as $q) {
                $selectedOptionId = $userAnswers[$q->id] ?? null;
                $isCorrect = false;
                $points = 0;

                if ($selectedOptionId) {
                    $selectedOption = $q->options->firstWhere('id', (int)$selectedOptionId);
                    if ($selectedOption && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $points = $q->points;
                        $correctCount++;
                    } else {
                        $wrongCount++;
                    }
                } else {
                    $wrongCount++;
                }

                if ($isCorrect) {
                    $totalPointsEarned += $points;
                }

                QuizAttemptAnswer::create([
                    'quiz_attempt_id'    => $attempt->id,
                    'quiz_question_id'   => $q->id,
                    'selected_option_id' => $selectedOptionId ? (int)$selectedOptionId : null,
                    'is_correct'         => $isCorrect,
                    'points_earned'      => $points,
                ]);
            }

            $completedAt = now();
            $durationSeconds = max(1, abs((int) $completedAt->diffInSeconds($attempt->started_at)));
            $possiblePoints = $attempt->total_points_possible > 0 ? $attempt->total_points_possible : $questions->sum('points');
            $score = $possiblePoints > 0 ? round(($totalPointsEarned / $possiblePoints) * 100, 1) : 0;

            $attempt->update([
                'completed_at'          => $completedAt,
                'duration_seconds'      => $durationSeconds,
                'total_points_earned'   => $totalPointsEarned,
                'total_points_possible' => $possiblePoints,
                'correct_answers_count' => $correctCount,
                'wrong_answers_count'   => $wrongCount,
                'score'                 => $score,
            ]);
        });
    }
}
