<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $elapsedSeconds = now()->diffInSeconds($ongoing->started_at);
                $allowedSeconds = $quiz->time_limit_minutes * 60;
                if ($elapsedSeconds > $allowedSeconds + 30) {
                    // Mark as expired/completed
                    $this->finalizeAttempt($ongoing, []);
                } else {
                    return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $ongoing->id]);
                }
            } else {
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

        return redirect()->route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
    }

    public function play(string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();

        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
        }

        // Calculate time limit
        $timeRemaining = null;
        if ($quiz->time_limit_minutes > 0) {
            $elapsedSeconds = (int) now()->diffInSeconds($attempt->started_at);
            $totalSeconds = (int) ($quiz->time_limit_minutes * 60);
            $timeRemaining = (int) max(0, $totalSeconds - $elapsedSeconds);

            if ($timeRemaining <= 0) {
                $this->finalizeAttempt($attempt, []);
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

        return view('quiz.play', compact('quiz', 'attempt', 'clientQuestions', 'timeRemaining'));
    }

    public function recordTabSwitch(string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();

        if ($attempt->quiz_id !== $quiz->id || $attempt->isCompleted()) {
            return response()->json(['success' => false]);
        }

        $attempt->increment('tab_switch_count');

        return response()->json([
            'success'          => true,
            'tab_switch_count' => $attempt->tab_switch_count,
        ]);
    }

    public function submit(Request $request, string $token, QuizAttempt $attempt)
    {
        $quiz = Quiz::where('unique_token', $token)->firstOrFail();

        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        if ($attempt->isCompleted()) {
            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id])]);
            }
            return redirect()->route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]);
        }

        $userAnswers = $request->input('answers', []);
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

        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

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
            $durationSeconds = max(1, $completedAt->diffInSeconds($attempt->started_at));
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
