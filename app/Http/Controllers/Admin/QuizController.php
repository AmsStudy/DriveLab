<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->isSuperAdmin() ? Quiz::query() : Quiz::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('access_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('soal_id')) {
            $query->where('soal_id', $request->soal_id);
        }

        $quizzes = $query->with(['user', 'soal'])
            ->withCount(['questions', 'attempts'])
            ->withAvg('attempts as avg_score', 'score')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // High level stats
        $baseStatsQuery = $user->isSuperAdmin() ? Quiz::query() : Quiz::where('user_id', $user->id);
        $totalQuizzes = $baseStatsQuery->count();
        $quizIds = (clone $baseStatsQuery)->pluck('id');
        $totalQuestions = QuizQuestion::whereIn('quiz_id', $quizIds)->count();
        $totalAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $avgScore = QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('completed_at')->avg('score') ?? 0;

        $soals = $user->isSuperAdmin() ? Soal::select('id', 'title')->get() : Soal::where('user_id', $user->id)->select('id', 'title')->get();

        return view('admin.quizzes.index', compact(
            'quizzes',
            'totalQuizzes',
            'totalQuestions',
            'totalAttempts',
            'avgScore',
            'soals'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        $soals = $user->isSuperAdmin() ? Soal::select('id', 'title')->get() : Soal::where('user_id', $user->id)->select('id', 'title')->get();
        return view('admin.quizzes.create', compact('soals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string|max:1000',
            'soal_id'            => 'nullable|exists:soals,id',
            'time_limit_minutes' => 'nullable|integer|min:0|max:300',
            'time_per_question'  => 'nullable|integer|min:0|max:300',
            'shuffle_questions'  => 'nullable|boolean',
            'shuffle_options'    => 'nullable|boolean',
            'show_leaderboard'   => 'nullable|boolean',
            'show_review'        => 'nullable|boolean',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'is_active'          => 'nullable|boolean',
        ]);

        $quiz = new Quiz();
        $quiz->user_id            = auth()->id();
        $quiz->title              = $validated['title'];
        $quiz->description        = $validated['description'] ?? null;
        $quiz->soal_id            = $validated['soal_id'] ?? null;
        $quiz->time_limit_minutes = $validated['time_limit_minutes'] ?? 0;
        $quiz->time_per_question  = $validated['time_per_question'] ?? 0;
        $quiz->shuffle_questions  = $request->boolean('shuffle_questions', true);
        $quiz->shuffle_options    = $request->boolean('shuffle_options', true);
        $quiz->show_leaderboard   = $request->boolean('show_leaderboard', true);
        $quiz->show_review        = $request->boolean('show_review', true);
        $quiz->max_attempts       = $validated['max_attempts'] ?? 1;
        $quiz->is_active          = $request->boolean('is_active', true);
        $quiz->save();

        return redirect()->route('admin.quizzes.questions', $quiz)
            ->with('success', 'Kuis berhasil dibuat! Sekarang silakan tambahkan pertanyaan kuis.');
    }

    public function edit(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $user = auth()->user();
        $soals = $user->isSuperAdmin() ? Soal::select('id', 'title')->get() : Soal::where('user_id', $user->id)->select('id', 'title')->get();
        return view('admin.quizzes.edit', compact('quiz', 'soals'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string|max:1000',
            'soal_id'            => 'nullable|exists:soals,id',
            'time_limit_minutes' => 'nullable|integer|min:0|max:300',
            'time_per_question'  => 'nullable|integer|min:0|max:300',
            'shuffle_questions'  => 'nullable|boolean',
            'shuffle_options'    => 'nullable|boolean',
            'show_leaderboard'   => 'nullable|boolean',
            'show_review'        => 'nullable|boolean',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'is_active'          => 'nullable|boolean',
        ]);

        $quiz->title              = $validated['title'];
        $quiz->description        = $validated['description'] ?? null;
        $quiz->soal_id            = $validated['soal_id'] ?? null;
        $quiz->time_limit_minutes = $validated['time_limit_minutes'] ?? 0;
        $quiz->time_per_question  = $validated['time_per_question'] ?? 0;
        $quiz->shuffle_questions  = $request->boolean('shuffle_questions', false);
        $quiz->shuffle_options    = $request->boolean('shuffle_options', false);
        $quiz->show_leaderboard   = $request->boolean('show_leaderboard', false);
        $quiz->show_review        = $request->boolean('show_review', false);
        $quiz->max_attempts       = $validated['max_attempts'] ?? 1;
        $quiz->is_active          = $request->boolean('is_active', false);
        $quiz->save();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Pengaturan kuis berhasil diperbarui!');
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil dihapus!');
    }

    public function toggleActive(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->is_active = !$quiz->is_active;
        $quiz->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'is_active' => $quiz->is_active,
                'message'   => $quiz->is_active ? 'Kuis diaktifkan' : 'Kuis dinonaktifkan',
            ]);
        }

        return back()->with('success', $quiz->is_active ? 'Kuis berhasil diaktifkan!' : 'Kuis berhasil dinonaktifkan!');
    }

    public function questions(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->load(['questions.options', 'soal']);
        return view('admin.quizzes.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'points'        => 'required|integer|min:1|max:1000',
            'explanation'   => 'nullable|string',
            'options'       => 'required|array|min:2',
            'options.*'     => 'required|string',
            'correct_index' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($quiz, $validated) {
            $maxOrder = $quiz->questions()->max('order_num') ?? 0;
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points'        => $validated['points'],
                'explanation'   => $validated['explanation'] ?? null,
                'order_num'     => $maxOrder + 1,
            ]);

            foreach ($validated['options'] as $idx => $optText) {
                $question->options()->create([
                    'option_text' => $optText,
                    'is_correct'  => ((int)$validated['correct_index'] === (int)$idx),
                    'order_num'   => $idx + 1,
                ]);
            }
        });

        return back()->with('success', 'Pertanyaan berhasil ditambahkan!');
    }

    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $this->authorizeQuiz($quiz);
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'points'        => 'required|integer|min:1|max:1000',
            'explanation'   => 'nullable|string',
            'options'       => 'required|array|min:2',
            'options.*'     => 'required|string',
            'correct_index' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points'        => $validated['points'],
                'explanation'   => $validated['explanation'] ?? null,
            ]);

            $question->options()->delete();
            foreach ($validated['options'] as $idx => $optText) {
                $question->options()->create([
                    'option_text' => $optText,
                    'is_correct'  => ((int)$validated['correct_index'] === (int)$idx),
                    'order_num'   => $idx + 1,
                ]);
            }
        });

        return back()->with('success', 'Pertanyaan berhasil diperbarui!');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
    {
        $this->authorizeQuiz($quiz);
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $question->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus!');
    }

    public function results(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $query = $quiz->attempts()->whereNotNull('completed_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nim', 'like', "%{$s}%")
                  ->orWhere('student_name', 'like', "%{$s}%");
            });
        }

        $attempts = $query->with('answers.question')
            ->orderByDesc('score')
            ->orderBy('duration_seconds')
            ->paginate(20)
            ->withQueryString();

        // Calculate analytics
        $allCompleted = $quiz->attempts()->whereNotNull('completed_at')->get();
        $totalParticipants = $allCompleted->count();
        $highestScore = $allCompleted->max('score') ?? 0;
        $lowestScore = $allCompleted->min('score') ?? 0;
        $avgScore = $allCompleted->avg('score') ?? 0;
        $avgDuration = $allCompleted->avg('duration_seconds') ?? 0;

        return view('admin.quizzes.results', compact(
            'quiz',
            'attempts',
            'totalParticipants',
            'highestScore',
            'lowestScore',
            'avgScore',
            'avgDuration'
        ));
    }

    public function exportResultsCsv(Quiz $quiz): StreamedResponse
    {
        $this->authorizeQuiz($quiz);

        $filename = 'hasil_kuis_' . $quiz->slug . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($quiz) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($handle, [
                'Peringkat',
                'NIM',
                'Nama Mahasiswa',
                'Skor (0-100)',
                'Poin Diperoleh',
                'Total Poin Kuis',
                'Jawaban Benar',
                'Jawaban Salah',
                'Durasi (Detik)',
                'Pelanggaran Tab Switch',
                'Waktu Mulai',
                'Waktu Selesai',
            ]);

            $attempts = $quiz->attempts()
                ->whereNotNull('completed_at')
                ->orderByDesc('score')
                ->orderBy('duration_seconds')
                ->get();

            $rank = 1;
            foreach ($attempts as $att) {
                fputcsv($handle, [
                    $rank++,
                    $this->sanitizeCsvField($att->nim),
                    $this->sanitizeCsvField($att->student_name),
                    $att->score,
                    $att->total_points_earned,
                    $att->total_points_possible,
                    $att->correct_answers_count,
                    $att->wrong_answers_count,
                    $att->duration_seconds,
                    $att->tab_switch_count,
                    $att->started_at ? $att->started_at->format('Y-m-d H:i:s') : '-',
                    $att->completed_at ? $att->completed_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function destroyAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorizeQuiz($quiz);
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        $attempt->delete();
        return back()->with('success', 'Riwayat pengerjaan mahasiswa berhasil dihapus!');
    }

    public function resetAttempts(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->attempts()->delete();
        return back()->with('success', 'Semua riwayat pengerjaan kuis ini telah direset!');
    }

    private function authorizeQuiz(Quiz $quiz): void
    {
        if (!auth()->user()->isSuperAdmin() && $quiz->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }
    }

    private function sanitizeCsvField(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }
        return $value;
    }
}
