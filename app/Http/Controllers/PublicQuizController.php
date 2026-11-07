<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    private function findQuizByIdentifier(string $identifier, array $with = []): Quiz
    {
        $query = Quiz::query();
        if (!empty($with)) {
            $query->with($with);
        }

        return $query->where(function ($q) use ($identifier) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('quizzes', 'slug')) {
                $q->where('slug', $identifier)
                  ->orWhere('unique_token', $identifier);
            } else {
                $q->where('unique_token', $identifier);
            }
        })->firstOrFail();
    }

    public function lobby(string $token)
    {
        $quiz = $this->findQuizByIdentifier($token, ['soal']);

        $questionsCount = $quiz->questions()->count();
        $totalPoints = $quiz->questions()->sum('points');
        $participantsCount = $quiz->attempts()->whereNotNull('completed_at')->count();

        $isUpcoming = $quiz->isScheduleUpcoming();
        $isExpired = $quiz->isScheduleExpired();
        $secondsUntilStart = $isUpcoming ? abs((int) now()->diffInSeconds($quiz->starts_at)) : 0;
        $hasPassword = $quiz->hasAccessPassword();

        return view('quiz.lobby', compact(
            'quiz',
            'questionsCount',
            'totalPoints',
            'participantsCount',
            'isUpcoming',
            'isExpired',
            'secondsUntilStart',
            'hasPassword'
        ));
    }

    public function start(Request $request, string $token)
    {
        $quiz = $this->findQuizByIdentifier($token);

        if (!$quiz->is_active) {
            return back()->with('error', 'Kuis saat ini tidak aktif atau telah ditutup.');
        }

        // Check if session has already ended by instructor
        if ($quiz->isSessionEnded()) {
            return back()->with('error', 'Sesi kuis ini telah dihentikan / selesai oleh pengawas laboratorium. Akses pengerjaan ditutup.')->withInput();
        }

        // 1. Check schedule: Expired
        if ($quiz->isScheduleExpired()) {
            return back()->with('error', 'Batas waktu akses kuis telah berakhir. Soal tidak dapat diakses lagi.')->withInput();
        }

        // 2. Check schedule: Upcoming
        if ($quiz->isScheduleUpcoming()) {
            return back()->with('error', 'Kuis belum dimulai! Silakan tunggu hitungan mundur hingga waktu mulai tiba.')->withInput();
        }

        // 3. Check access password
        if ($quiz->hasAccessPassword()) {
            $inputPassword = (string) $request->input('access_password', '');
            if (trim($inputPassword) !== $quiz->access_password) {
                return back()->with('error', 'Password akses kuis salah! Pastikan Anda memasukkan password yang diberikan asisten/dosen.')->withInput();
            }
        }

        $rules = [
            'nim'  => 'required|string|max:30|regex:/^[a-zA-Z0-9\-_.]+$/',
            'nama' => 'required|string|max:100',
        ];
        $messages = [
            'nim.required'  => 'NIM wajib diisi.',
            'nim.regex'     => 'NIM hanya boleh berisi huruf, angka, titik, atau strip.',
            'nama.required' => 'Nama lengkap wajib diisi.',
        ];

        if ($quiz->hasClasses()) {
            $rules['kelas'] = ['required', 'string', Rule::in($quiz->getClassesList())];
            $messages['kelas.required'] = 'Pilih kelas Anda terlebih dahulu.';
            $messages['kelas.in']       = 'Kelas yang dipilih tidak valid.';
        }

        $validated = $request->validate($rules, $messages);

        $nim = trim($validated['nim']);
        $nama = trim($validated['nama']);
        $kelas = $quiz->hasClasses() ? trim($validated['kelas']) : null;

        // Check if there's an ongoing uncompleted attempt for this NIM
        $ongoing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('nim', $nim)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($ongoing) {
            if ($kelas && empty($ongoing->kelas)) {
                $ongoing->update(['kelas' => $kelas]);
            }
            // Check if ongoing has already expired based on time_limit_minutes + extra_time_minutes
            $totalAllowedMinutes = $quiz->time_limit_minutes + ($ongoing->extra_time_minutes ?? 0);
            if ($totalAllowedMinutes > 0) {
                $elapsedSeconds = abs((int) now()->diffInSeconds($ongoing->started_at));
                $allowedSeconds = $totalAllowedMinutes * 60;
                if ($elapsedSeconds > $allowedSeconds + 30) {
                    // Mark as expired/completed, preserving draft answers
                    QuizScoringService::finalize($ongoing, $ongoing->draft_answers ?? []);
                } else {
                    session(['quiz_attempt_' . $quiz->id => $ongoing->id]);
                    if ($quiz->isSessionWaiting()) {
                        return redirect()->route('quiz.waiting', ['token' => $token, 'attempt' => $ongoing->id]);
                    }
                    return redirect()->route('quiz.play', ['token' => $token, 'attempt' => $ongoing->id]);
                }
            } else {
                session(['quiz_attempt_' . $quiz->id => $ongoing->id]);
                if ($quiz->isSessionWaiting()) {
                    return redirect()->route('quiz.waiting', ['token' => $token, 'attempt' => $ongoing->id]);
                }
                return redirect()->route('quiz.play', ['token' => $token, 'attempt' => $ongoing->id]);
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
            'kelas'                 => $kelas,
            'total_points_possible' => $totalPoints,
            'started_at'            => now(),
            'last_heartbeat_at'     => now(),
        ]);

        session(['quiz_attempt_' . $quiz->id => $attempt->id]);

        if ($quiz->isSessionWaiting()) {
            return redirect()->route('quiz.waiting', ['token' => $token, 'attempt' => $attempt->id]);
        }

        return redirect()->route('quiz.play', ['token' => $token, 'attempt' => $attempt->id]);
    }

    public function waitingRoom(string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]);
        }

        if ($quiz->isSessionStarted()) {
            return redirect()->route('quiz.play', ['token' => $token, 'attempt' => $attempt->id]);
        }

        if ($quiz->isSessionEnded()) {
            return redirect()->route('quiz.lobby', ['token' => $token])->with('error', 'Sesi kuis telah dihentikan / ditutup oleh pengawas.');
        }

        // Throttle heartbeat database write to once every 10 seconds per attempt to save database I/O
        if (!$attempt->last_heartbeat_at || now()->diffInSeconds($attempt->last_heartbeat_at) >= 10) {
            $attempt->update(['last_heartbeat_at' => now()]);
        }

        // Cache waiting count for 3 seconds so 300 polling users don't trigger 150 COUNT(*) queries per second
        $waitingCount = cache()->remember("quiz_{$quiz->id}_waiting_count", 3, function () use ($quiz) {
            return $quiz->attempts()->whereNull('completed_at')->count();
        });

        return view('quiz.waiting', compact('quiz', 'attempt', 'waitingCount'));
    }

    public function waitingStatus(string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        // Throttle heartbeat database write to once every 10 seconds per attempt to save database I/O
        if (!$attempt->last_heartbeat_at || now()->diffInSeconds($attempt->last_heartbeat_at) >= 10) {
            $attempt->update(['last_heartbeat_at' => now()]);
        }

        // Cache waiting count for 3 seconds so 300 polling users don't trigger 150 COUNT(*) queries per second
        $waitingCount = cache()->remember("quiz_{$quiz->id}_waiting_count", 3, function () use ($quiz) {
            return $quiz->attempts()->whereNull('completed_at')->count();
        });

        if ($quiz->isSessionStarted()) {
            return response()->json([
                'session_status' => 'started',
                'redirect'       => route('quiz.play', ['token' => $token, 'attempt' => $attempt->id]),
                'waiting_count'  => $waitingCount,
            ]);
        }

        if ($quiz->isSessionEnded()) {
            return response()->json([
                'session_status' => 'ended',
                'redirect'       => route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]),
                'waiting_count'  => 0,
            ]);
        }

        return response()->json([
            'session_status' => 'waiting',
            'redirect'       => null,
            'waiting_count'  => $waitingCount,
        ]);
    }

    public function sessionCheck(string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        // Throttle heartbeat database write to once every 10 seconds per attempt to save database I/O
        if (!$attempt->last_heartbeat_at || now()->diffInSeconds($attempt->last_heartbeat_at) >= 10) {
            $attempt->update(['last_heartbeat_at' => now()]);
        }

        // Cache session status for 2 seconds to eliminate database load for 300 polling students
        $sessionStatus = cache()->remember("quiz_{$quiz->id}_status", 2, function () use ($quiz) {
            return $quiz->fresh()->session_status;
        });

        if ($attempt->isCompleted() || $attempt->is_force_submitted || $sessionStatus === 'ended' || $quiz->isSessionEnded()) {
            if (!$attempt->isCompleted()) {
                $attempt->update(['is_force_submitted' => true]);
                QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
            }
            return response()->json([
                'status'       => 'ended',
                'is_ended'     => true,
                'redirect'     => route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]),
                'message'      => 'Sesi ujian telah diakhiri oleh pengawas lab.',
            ]);
        }

        // Calculate remaining time
        $totalAllowedMinutes = $quiz->time_limit_minutes + ($attempt->extra_time_minutes ?? 0);
        $timeRemaining = null;
        if ($totalAllowedMinutes > 0) {
            $elapsedSeconds = abs((int) now()->diffInSeconds($attempt->started_at));
            $totalSeconds = (int) ($totalAllowedMinutes * 60);
            $timeRemaining = (int) max(0, $totalSeconds - $elapsedSeconds);
            if ($timeRemaining <= 0) {
                if (!$attempt->isCompleted()) {
                    QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
                }
                return response()->json([
                    'status'       => 'ended',
                    'is_ended'     => true,
                    'redirect'     => route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]),
                    'message'      => 'Waktu pengerjaan kuis telah habis.',
                ]);
            }
        }

        return response()->json([
            'status'             => 'active',
            'is_ended'           => false,
            'is_locked'          => (bool) $attempt->is_locked,
            'time_remaining'     => $timeRemaining,
            'extra_time_minutes' => (int) ($attempt->extra_time_minutes ?? 0),
        ]);
    }

    public function play(string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]);
        }

        // If quiz is still in waiting room, redirect back to waiting room!
        if ($quiz->isSessionWaiting()) {
            return redirect()->route('quiz.waiting', ['token' => $token, 'attempt' => $attempt->id]);
        }

        // If quiz session was ended by instructor, finalize and redirect to results
        if ($quiz->isSessionEnded()) {
            if (!$attempt->isCompleted()) {
                $attempt->update(['is_force_submitted' => true]);
                QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
            }
            return redirect()->route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]);
        }

        // Calculate time limit including any instructor extra time
        $totalAllowedMinutes = $quiz->time_limit_minutes + ($attempt->extra_time_minutes ?? 0);
        $timeRemaining = null;
        if ($totalAllowedMinutes > 0) {
            $elapsedSeconds = abs((int) now()->diffInSeconds($attempt->started_at));
            $totalSeconds = (int) ($totalAllowedMinutes * 60);
            $timeRemaining = (int) max(0, $totalSeconds - $elapsedSeconds);

            if ($timeRemaining <= 0) {
                // Finalize preserving draft answers
                QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
                return redirect()->route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]);
            }
        }

        $attempt->update(['last_heartbeat_at' => now()]);

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
                'image_url'     => $q->image_url,
                'question_type' => $q->isMultiSelect() ? 'multi_select' : $q->question_type,
                'points'        => $q->points,
                'options'       => $q->options->map(function ($opt) {
                    return [
                        'id'        => $opt->id,
                        'text'      => $opt->option_text,
                        'image_url' => $opt->image_url,
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
        $quiz = $this->findQuizByIdentifier($token);
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
        $quiz = $this->findQuizByIdentifier($token, ['user']);
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
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        if ($attempt->isCompleted() || $attempt->is_force_submitted || $quiz->isSessionEnded()) {
            if (!$attempt->isCompleted()) {
                $attempt->update(['is_force_submitted' => true]);
                QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
            }
            return response()->json([
                'success'      => false,
                'is_ended'     => true,
                'is_completed' => true,
                'redirect'     => route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]),
                'message'      => $attempt->is_force_submitted ? 'Ujian telah dikumpulkan oleh pengawas lab.' : 'Sesi kuis telah selesai.',
            ]);
        }

        $answers = $request->input('answers', []);
        if (is_array($answers)) {
            $attempt->draft_answers = $answers;
        }
        $attempt->last_heartbeat_at = now();
        $attempt->save();

        $totalAllowedMinutes = $quiz->time_limit_minutes + ($attempt->extra_time_minutes ?? 0);
        $timeRemaining = null;
        if ($totalAllowedMinutes > 0) {
            $elapsedSeconds = abs((int) now()->diffInSeconds($attempt->started_at));
            $totalSeconds = (int) ($totalAllowedMinutes * 60);
            $timeRemaining = (int) max(0, $totalSeconds - $elapsedSeconds);
        }

        return response()->json([
            'success'            => true,
            'saved_at'           => now()->toIso8601String(),
            'time_remaining'     => $timeRemaining,
            'extra_time_minutes' => (int) ($attempt->extra_time_minutes ?? 0),
            'is_locked'          => (bool) $attempt->is_locked,
            'tab_switch_count'   => (int) $attempt->tab_switch_count,
        ]);
    }

    public function submit(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token);
        $this->validateAttemptAccess($quiz, $attempt);

        return DB::transaction(function () use ($request, $quiz, $attempt, $token) {
            // Pessimistic locking: lock the attempt record so concurrent requests wait and resolve idempotently
            /** @var QuizAttempt $lockedAttempt */
            $lockedAttempt = QuizAttempt::where('id', $attempt->id)->lockForUpdate()->first();

            if (!$lockedAttempt || $lockedAttempt->isCompleted()) {
                $redirectUrl = route('quiz.results', ['token' => $token, 'attempt' => $attempt->id]);
                if ($request->wantsJson()) {
                    return response()->json([
                        'success'  => true,
                        'redirect' => $redirectUrl,
                    ]);
                }
                return redirect()->to($redirectUrl);
            }

            $userAnswers = $request->input('answers', $lockedAttempt->draft_answers ?? []);

            // Server-side strict time enforcement (with 90-second network latency tolerance)
            $totalAllowedMinutes = $quiz->time_limit_minutes + ($lockedAttempt->extra_time_minutes ?? 0);
            if ($totalAllowedMinutes > 0) {
                $elapsedSeconds = abs((int) now()->diffInSeconds($lockedAttempt->started_at));
                $maxAllowedSeconds = ($totalAllowedMinutes * 60) + 90;
                if ($elapsedSeconds > $maxAllowedSeconds) {
                    // If submitted well past the time limit, fall back to what was autosaved in drafts prior to expiration
                    $userAnswers = $lockedAttempt->draft_answers ?? [];
                }
            }

            QuizScoringService::finalize($lockedAttempt, is_array($userAnswers) ? $userAnswers : []);

            $redirectUrl = route('quiz.results', ['token' => $token, 'attempt' => $lockedAttempt->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        });
    }

    public function results(string $token, QuizAttempt $attempt)
    {
        $quiz = $this->findQuizByIdentifier($token, ['soal']);
        $this->validateAttemptAccess($quiz, $attempt);

        if (!$attempt->isCompleted()) {
            return redirect()->route('quiz.play', ['token' => $token, 'attempt' => $attempt->id]);
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
        QuizScoringService::finalize($attempt, $userAnswers);
    }
}

