<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Soal;
use App\Services\QuizImageService;
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
            'access_password'    => 'nullable|string|max:50',
            'starts_at'          => 'nullable|date',
            'ends_at'            => 'nullable|date|after_or_equal:starts_at',
            'time_limit_minutes' => 'nullable|integer|min:0|max:300',
            'time_per_question'  => 'nullable|integer|min:0|max:300',
            'shuffle_questions'  => 'nullable|boolean',
            'shuffle_options'    => 'nullable|boolean',
            'show_leaderboard'   => 'nullable|boolean',
            'show_review'        => 'nullable|boolean',
            'allow_open_book'    => 'nullable|boolean',
            'admin_unlock_password' => 'nullable|string|max:50',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'is_active'          => 'nullable|boolean',
        ]);

        $quiz = new Quiz();
        $quiz->user_id               = auth()->id();
        $quiz->title                 = $validated['title'];
        $quiz->description           = $validated['description'] ?? null;
        $quiz->soal_id               = $validated['soal_id'] ?? null;
        $quiz->access_password       = $request->filled('access_password') ? trim($request->access_password) : null;
        $quiz->starts_at             = $request->filled('starts_at') ? $request->starts_at : null;
        $quiz->ends_at               = $request->filled('ends_at') ? $request->ends_at : null;
        $quiz->time_limit_minutes    = $validated['time_limit_minutes'] ?? 0;
        $quiz->time_per_question     = $validated['time_per_question'] ?? 0;
        $quiz->shuffle_questions     = $request->boolean('shuffle_questions', true);
        $quiz->shuffle_options       = $request->boolean('shuffle_options', true);
        $quiz->show_leaderboard      = $request->boolean('show_leaderboard', true);
        $quiz->show_review           = $request->boolean('show_review', true);
        $quiz->allow_open_book       = $request->boolean('allow_open_book', false);
        $quiz->admin_unlock_password = $request->filled('admin_unlock_password') ? trim($request->admin_unlock_password) : null;
        $quiz->max_attempts          = $validated['max_attempts'] ?? 1;
        $quiz->is_active             = $request->boolean('is_active', true);
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
            'access_password'    => 'nullable|string|max:50',
            'starts_at'          => 'nullable|date',
            'ends_at'            => 'nullable|date|after_or_equal:starts_at',
            'time_limit_minutes' => 'nullable|integer|min:0|max:300',
            'time_per_question'  => 'nullable|integer|min:0|max:300',
            'shuffle_questions'  => 'nullable|boolean',
            'shuffle_options'    => 'nullable|boolean',
            'show_leaderboard'   => 'nullable|boolean',
            'show_review'        => 'nullable|boolean',
            'allow_open_book'    => 'nullable|boolean',
            'admin_unlock_password' => 'nullable|string|max:50',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'is_active'          => 'nullable|boolean',
        ]);

        $quiz->title                 = $validated['title'];
        $quiz->description           = $validated['description'] ?? null;
        $quiz->soal_id               = $validated['soal_id'] ?? null;
        $quiz->access_password       = $request->filled('access_password') ? trim($request->access_password) : null;
        $quiz->starts_at             = $request->filled('starts_at') ? $request->starts_at : null;
        $quiz->ends_at               = $request->filled('ends_at') ? $request->ends_at : null;
        $quiz->time_limit_minutes    = $validated['time_limit_minutes'] ?? 0;
        $quiz->time_per_question     = $validated['time_per_question'] ?? 0;
        $quiz->shuffle_questions     = $request->boolean('shuffle_questions', false);
        $quiz->shuffle_options       = $request->boolean('shuffle_options', false);
        $quiz->show_leaderboard      = $request->boolean('show_leaderboard', false);
        $quiz->show_review           = $request->boolean('show_review', false);
        $quiz->allow_open_book       = $request->boolean('allow_open_book', false);
        if ($request->filled('admin_unlock_password')) {
            $quiz->admin_unlock_password = trim($request->admin_unlock_password);
        }
        $quiz->max_attempts          = $validated['max_attempts'] ?? 1;
        $quiz->is_active             = $request->boolean('is_active', false);
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
            'question_text'    => 'required|string',
            'question_type'    => 'nullable|in:multiple_choice,multi_select,true_false',
            'points'           => 'required|integer|min:1|max:1000',
            'explanation'      => 'nullable|string',
            'question_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'options'          => 'required|array|min:2',
            'options.*'        => 'nullable|string',
            'option_images.*'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'correct_indices'  => 'nullable|array|min:1',
            'correct_indices.*'=> 'integer|min:0',
            'correct_index'    => 'nullable|integer|min:0',
        ]);

        $correctIndices = [];
        if ($request->has('correct_indices') && is_array($request->correct_indices)) {
            $correctIndices = array_map('intval', $request->correct_indices);
        } elseif ($request->filled('correct_index')) {
            $correctIndices = [(int)$request->correct_index];
        } else {
            $correctIndices = [0];
        }

        $isMulti = count($correctIndices) > 1;
        $questionType = $isMulti ? 'multi_select' : ($request->input('question_type') ?: 'multiple_choice');

        $questionImagePath = null;
        if ($request->hasFile('question_image')) {
            $questionImagePath = QuizImageService::optimizeAndStore($request->file('question_image'), 'questions', 1200);
        }

        DB::transaction(function () use ($quiz, $validated, $questionImagePath, $questionType, $correctIndices, $request) {
            $maxOrder = $quiz->questions()->max('order_num') ?? 0;
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'image_path'    => $questionImagePath,
                'question_type' => $questionType,
                'points'        => $validated['points'],
                'explanation'   => $validated['explanation'] ?? null,
                'order_num'     => $maxOrder + 1,
            ]);

            foreach ($request->input('options', []) as $idx => $optText) {
                $optImagePath = null;
                if ($request->hasFile("option_images.{$idx}")) {
                    $optImagePath = QuizImageService::optimizeAndStore($request->file("option_images.{$idx}"), 'options', 650);
                }

                $question->options()->create([
                    'option_text' => $optText ?? '',
                    'image_path'  => $optImagePath,
                    'is_correct'  => in_array((int)$idx, $correctIndices, true),
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
            'question_text'            => 'required|string',
            'question_type'            => 'nullable|in:multiple_choice,multi_select,true_false',
            'points'                   => 'required|integer|min:1|max:1000',
            'explanation'              => 'nullable|string',
            'question_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'remove_question_image'    => 'nullable|boolean',
            'options'                  => 'required|array|min:2',
            'options.*'                => 'nullable|string',
            'option_images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'existing_option_images.*' => 'nullable|string',
            'remove_option_images.*'   => 'nullable|boolean',
            'correct_indices'          => 'nullable|array|min:1',
            'correct_indices.*'        => 'integer|min:0',
            'correct_index'            => 'nullable|integer|min:0',
        ]);

        $correctIndices = [];
        if ($request->has('correct_indices') && is_array($request->correct_indices)) {
            $correctIndices = array_map('intval', $request->correct_indices);
        } elseif ($request->filled('correct_index')) {
            $correctIndices = [(int)$request->correct_index];
        } else {
            $correctIndices = [0];
        }

        $isMulti = count($correctIndices) > 1;
        $questionType = $isMulti ? 'multi_select' : ($request->input('question_type') ?: 'multiple_choice');

        $questionImagePath = $question->image_path;
        if ($request->boolean('remove_question_image')) {
            QuizImageService::deleteImage($questionImagePath);
            $questionImagePath = null;
        }
        if ($request->hasFile('question_image')) {
            QuizImageService::deleteImage($questionImagePath);
            $questionImagePath = QuizImageService::optimizeAndStore($request->file('question_image'), 'questions', 1200);
        }

        DB::transaction(function () use ($question, $validated, $questionImagePath, $questionType, $correctIndices, $request) {
            $question->update([
                'question_text' => $validated['question_text'],
                'image_path'    => $questionImagePath,
                'question_type' => $questionType,
                'points'        => $validated['points'],
                'explanation'   => $validated['explanation'] ?? null,
            ]);

            // Recreate options, preserving or updating images
            $question->options()->delete();
            foreach ($request->input('options', []) as $idx => $optText) {
                $optImagePath = $request->input("existing_option_images.{$idx}");
                if ($request->boolean("remove_option_images.{$idx}")) {
                    QuizImageService::deleteImage($optImagePath);
                    $optImagePath = null;
                }
                if ($request->hasFile("option_images.{$idx}")) {
                    QuizImageService::deleteImage($optImagePath);
                    $optImagePath = QuizImageService::optimizeAndStore($request->file("option_images.{$idx}"), 'options', 650);
                }

                $question->options()->create([
                    'option_text' => $optText ?? '',
                    'image_path'  => $optImagePath,
                    'is_correct'  => in_array((int)$idx, $correctIndices, true),
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

        QuizImageService::deleteImage($question->image_path);
        foreach ($question->options as $opt) {
            QuizImageService::deleteImage($opt->image_path);
        }

        $question->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus!');
    }

    public function results(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $query = $quiz->attempts()->where(function ($q) {
            $q->whereNotNull('completed_at')
              ->orWhere('is_locked', true);
        });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nim', 'like', "%{$s}%")
                  ->orWhere('student_name', 'like', "%{$s}%");
            });
        }

        $attempts = $query->with(['answers.question.options', 'answers.selectedOption'])
            ->orderByDesc('is_locked')
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

    public function remoteUnlockAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorizeQuiz($quiz);
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        $logs = $attempt->violations_log ?? [];
        $logs[] = [
            'type'        => 'remote_unlocked_by_admin',
            'unlocked_by' => auth()->user()->name,
            'timestamp'   => now()->toIso8601String(),
        ];

        $attempt->update([
            'is_locked'        => false,
            'lock_reason'      => null,
            'penalty_end_time' => null,
            'tab_switch_count' => min(2, $attempt->tab_switch_count),
            'violations_log'   => $logs,
        ]);

        return back()->with('success', "Kunci pengerjaan mahasiswa {$attempt->student_name} ({$attempt->nim}) berhasil dibuka!");
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
