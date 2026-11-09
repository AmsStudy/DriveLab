<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnnouncement;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Soal;
use App\Services\QuizImageService;
use App\Services\QuizImportService;
use App\Services\QuizScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
        // Auto-finalize any orphaned expired attempts before calculating stats
        $uncompletedAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->whereNull('completed_at')
            ->whereNotNull('started_at')
            ->with('quiz')
            ->get();

        foreach ($uncompletedAttempts as $att) {
            $quizTime = (int) ($att->quiz->time_limit_minutes ?? 0) + (int) ($att->extra_time_minutes ?? 0);
            if ($quizTime > 0) {
                $maxSec = ($quizTime * 60) + 120; // 2 minutes grace period
                if (now()->diffInSeconds($att->started_at) > $maxSec) {
                    \App\Services\QuizScoringService::finalize($att, $att->draft_answers ?? [], true);
                }
            }
        }

        $totalAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $avgScore = QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('completed_at')->avg('score') ?? 0;

        $soals = $user->isSuperAdmin() ? Soal::select('id', 'title')->get() : Soal::where('user_id', $user->id)->select('id', 'title')->get();

        $editQuizId = session('edit_quiz_id') ?? old('quiz_id');
        $extraEditQuiz = null;
        if ($editQuizId && !$quizzes->contains('id', (int)$editQuizId)) {
            $extraEditQuiz = Quiz::find($editQuizId);
        }

        return view('admin.quizzes.index', compact(
            'quizzes',
            'totalQuizzes',
            'totalQuestions',
            'totalAttempts',
            'avgScore',
            'soals',
            'extraEditQuiz'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.quizzes.index')->with('open_create_modal', true);
    }

    public function store(Request $request)
    {
        if ($request->filled('access_code')) {
            $request->merge(['access_code' => strtoupper(trim($request->access_code))]);
        }

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'access_code'        => 'nullable|string|min:3|max:20|regex:/^[A-Z0-9_-]+$/|unique:quizzes,access_code',
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
            'show_correct_answer' => 'nullable|boolean',
            'allow_open_book'    => 'nullable|boolean',
            'admin_unlock_password' => 'nullable|string|max:50',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'classes'            => 'nullable|string|max:500',
            'is_active'          => 'nullable|boolean',
        ], [
            'access_code.unique' => 'Kode akses kuis ini sudah digunakan oleh kuis lain. Silakan buat kode unik lain.',
            'access_code.regex'  => 'Kode akses hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'access_code.min'    => 'Kode akses kuis minimal 3 karakter.',
            'access_code.max'    => 'Kode akses kuis maksimal 20 karakter.',
        ]);

        $quiz = new Quiz();
        $quiz->user_id               = auth()->id();
        if ($quiz->title !== $validated['title'] || empty($quiz->slug)) {
            $quiz->slug = Quiz::generateUniqueSlug($validated['title'], $quiz->id);
        }
        $quiz->title                 = $validated['title'];
        if (!empty($validated['access_code'])) {
            $quiz->access_code       = $validated['access_code'];
        }
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
        $quiz->show_correct_answer   = $request->boolean('show_correct_answer', false);
        $quiz->allow_open_book       = $request->boolean('allow_open_book', false);
        $quiz->admin_unlock_password = $request->filled('admin_unlock_password') ? trim($request->admin_unlock_password) : null;
        $quiz->max_attempts          = $validated['max_attempts'] ?? 1;
        if (!empty($request->classes)) {
            $classesArray = array_values(array_filter(array_map('trim', explode(',', $request->classes))));
            $quiz->classes = !empty($classesArray) ? $classesArray : null;
        }
        $quiz->is_active             = $request->boolean('is_active', true);
        $quiz->save();

        return redirect()->route('admin.quizzes.questions', $quiz)
            ->with('success', 'Kuis berhasil dibuat! Sekarang silakan tambahkan pertanyaan kuis.');
    }

    public function edit(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        return redirect()->route('admin.quizzes.index')->with('edit_quiz_id', $quiz->id);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        if ($request->filled('access_code')) {
            $request->merge(['access_code' => strtoupper(trim($request->access_code))]);
        }

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'access_code'        => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('quizzes', 'access_code')->ignore($quiz->id),
            ],
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
            'show_correct_answer' => 'nullable|boolean',
            'allow_open_book'    => 'nullable|boolean',
            'admin_unlock_password' => 'nullable|string|max:50',
            'max_attempts'       => 'nullable|integer|min:1|max:20',
            'classes'            => 'nullable|string|max:500',
            'is_active'          => 'nullable|boolean',
        ], [
            'access_code.required' => 'Kode akses kuis tidak boleh kosong.',
            'access_code.unique'   => 'Kode akses kuis ini sudah digunakan oleh kuis lain. Silakan buat kode unik lain.',
            'access_code.regex'    => 'Kode akses hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'access_code.min'      => 'Kode akses kuis minimal 3 karakter.',
            'access_code.max'      => 'Kode akses kuis maksimal 20 karakter.',
        ]);

        if ($quiz->title !== $validated['title'] || empty($quiz->slug)) {
            $quiz->slug = Quiz::generateUniqueSlug($validated['title'], $quiz->id);
        }
        $quiz->title                 = $validated['title'];
        $quiz->access_code           = $validated['access_code'];
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
        $quiz->show_correct_answer   = $request->boolean('show_correct_answer', false);
        $quiz->allow_open_book       = $request->boolean('allow_open_book', false);
        $quiz->admin_unlock_password = $request->filled('admin_unlock_password') ? trim($request->admin_unlock_password) : null;
        $quiz->max_attempts          = $validated['max_attempts'] ?? 1;
        if ($request->has('classes')) {
            $classesArray = !empty($request->classes) ? array_values(array_filter(array_map('trim', explode(',', $request->classes)))) : null;
            $quiz->classes = $classesArray;
        }
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

    public function duplicate(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        return DB::transaction(function () use ($quiz) {
            do {
                $newCode = strtoupper(Str::random(6));
            } while (Quiz::where('access_code', $newCode)->exists());

            do {
                $newToken = Str::random(8);
            } while (Quiz::where('unique_token', $newToken)->exists());

            $newQuiz = $quiz->replicate([
                'access_code',
                'unique_token',
                'slug',
            ]);

            $newQuiz->user_id = auth()->id();
            $newQuiz->title = $quiz->title . ' (Salinan)';
            $newQuiz->slug = Str::slug($newQuiz->title) . '-' . strtolower(Str::random(5));
            $newQuiz->access_code = $newCode;
            $newQuiz->unique_token = $newToken;
            $newQuiz->is_active = false;
            $newQuiz->save();

            $questions = $quiz->questions()->with('options')->get();
            foreach ($questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->quiz_id = $newQuiz->id;
                $newQuestion->save();

                foreach ($question->options as $option) {
                    $newOption = $option->replicate();
                    $newOption->quiz_question_id = $newQuestion->id;
                    $newOption->save();
                }
            }

            return redirect()->route('admin.quizzes.index')
                ->with('success', "Kuis \"{$quiz->title}\" berhasil diduplikasi menjadi \"{$newQuiz->title}\" beserta {$questions->count()} butir soal. Kode akses baru: {$newCode}.");
        });
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

        // Re-index remaining questions order_num sequentially
        $remaining = $quiz->questions()->orderBy('order_num')->orderBy('id')->get();
        foreach ($remaining as $idx => $item) {
            $pos = $idx + 1;
            if ($item->order_num !== $pos) {
                $item->update(['order_num' => $pos]);
            }
        }

        return back()->with('success', 'Pertanyaan berhasil dihapus!');
    }

    public function reorderQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $this->authorizeQuiz($quiz);
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $validated = $request->validate([
            'order' => 'required|integer|min:1',
        ]);

        $newOrder = (int) $validated['order'];
        $totalQuestions = $quiz->questions()->count();
        if ($newOrder > $totalQuestions) {
            $newOrder = $totalQuestions;
        }

        DB::transaction(function () use ($quiz, $question, $newOrder) {
            $questions = $quiz->questions()->orderBy('order_num')->orderBy('id')->get();
            $filtered = $questions->reject(fn($q) => $q->id === $question->id)->values();

            $insertIndex = max(0, min($newOrder - 1, $filtered->count()));
            $filtered->splice($insertIndex, 0, [$question]);

            foreach ($filtered as $idx => $item) {
                $pos = $idx + 1;
                if ($item->order_num !== $pos) {
                    $item->update(['order_num' => $pos]);
                }
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Nomor urut pertanyaan berhasil diubah menjadi #{$newOrder}!",
            ]);
        }

        return back()->with('success', "Nomor urut pertanyaan berhasil diubah menjadi #{$newOrder}!");
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

        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->where('kelas', $request->kelas);
        }

        $attempts = $query->with(['answers.question.options', 'answers.selectedOption'])
            ->orderByDesc('is_locked')
            ->orderByDesc('score')
            ->orderBy('duration_seconds')
            ->paginate(20)
            ->withQueryString();

        // Ensure review answers exist for all attempts (fallback synthesis for legacy/draft-only attempts)
        $quiz->loadMissing('questions.options');
        foreach ($attempts as $att) {
            if ($att->answers->isEmpty() && $quiz->questions->isNotEmpty()) {
                $syntheticAnswers = collect();
                $drafts = is_array($att->draft_answers) ? $att->draft_answers : [];
                unset($drafts['__flagged']);

                $isPerfectScore = ((float)$att->score >= 99.9) || ($att->wrong_answers_count === 0 && (int)$att->correct_answers_count > 0);
                $correctRemaining = (int)($att->correct_answers_count ?? 0);

                foreach ($quiz->questions as $idx => $q) {
                    $userAns = $drafts[$q->id] ?? ($drafts[$idx + 1] ?? null);
                    $selectedIds = [];
                    $selectedId = null;
                    if (is_array($userAns)) {
                        $selectedIds = array_values(array_map('intval', $userAns));
                        $selectedId = $selectedIds[0] ?? null;
                    } elseif ($userAns) {
                        $selectedId = (int)$userAns;
                        $selectedIds = [$selectedId];
                    }

                    $isCorrect = false;
                    $pointsEarned = 0;

                    if ($userAns !== null) {
                        if ($q->isMultiSelect()) {
                            $correctOptionIds = $q->options->where('is_correct', true)->pluck('id')->values()->all();
                            $correctSelected = array_intersect($selectedIds, $correctOptionIds);
                            $wrongSelected = array_diff($selectedIds, $correctOptionIds);
                            if (count($wrongSelected) === 0 && count($correctSelected) === count($correctOptionIds) && count($correctOptionIds) > 0) {
                                $isCorrect = true;
                                $pointsEarned = $q->points;
                            }
                        } else {
                            $selectedOpt = $q->options->firstWhere('id', (int)$selectedId);
                            if ($selectedOpt && $selectedOpt->is_correct) {
                                $isCorrect = true;
                                $pointsEarned = $q->points;
                            }
                        }
                    } elseif ($isPerfectScore || $correctRemaining > 0) {
                        // Jika tidak ada draft tersimpan tapi attempt bernilai sempurna / memiliki jumlah benar, sintesiskan opsi kunci benar
                        $isCorrect = true;
                        $pointsEarned = $q->points;
                        if ($q->isMultiSelect()) {
                            $selectedIds = $q->options->where('is_correct', true)->pluck('id')->values()->all();
                            $selectedId = $selectedIds[0] ?? null;
                        } else {
                            $correctOpt = $q->options->firstWhere('is_correct', true);
                            $selectedId = $correctOpt ? $correctOpt->id : null;
                            $selectedIds = $selectedId ? [$selectedId] : [];
                        }
                        $correctRemaining--;
                    }

                    $mockAns = new \App\Models\QuizAttemptAnswer([
                        'quiz_attempt_id'     => $att->id,
                        'quiz_question_id'    => $q->id,
                        'selected_option_id'  => $selectedId,
                        'selected_option_ids' => $selectedIds,
                        'is_correct'          => $isCorrect,
                        'points_earned'       => $pointsEarned,
                    ]);
                    $mockAns->id = 'fallback_' . $q->id;
                    $mockAns->setRelation('question', $q);
                    $syntheticAnswers->push($mockAns);
                }
                $att->setRelation('answers', $syntheticAnswers);
            }
        }

        // Calculate analytics efficiently via SQL aggregation (prevents OOM on mini server & MySQL 1140 ONLY_FULL_GROUP_BY error)
        $analytics = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->selectRaw('
                COUNT(*) as total_participants,
                MAX(score) as highest_score,
                MIN(score) as lowest_score,
                AVG(score) as avg_score,
                AVG(duration_seconds) as avg_duration
            ')
            ->first();

        $totalParticipants = (int) ($analytics->total_participants ?? 0);
        $highestScore      = $totalParticipants > 0 ? (float) ($analytics->highest_score ?? 0) : 0;
        $lowestScore       = $totalParticipants > 0 ? (float) ($analytics->lowest_score ?? 0) : 0;
        $avgScore          = $totalParticipants > 0 ? round((float) ($analytics->avg_score ?? 0), 1) : 0;
        $avgDuration       = $totalParticipants > 0 ? round((float) ($analytics->avg_duration ?? 0)) : 0;

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

    public function exportResultsCsv(Request $request, Quiz $quiz): StreamedResponse
    {
        $this->authorizeQuiz($quiz);

        $selectedClass = $request->query('kelas');
        $isFilteredByClass = !empty($selectedClass) && $selectedClass !== 'all';

        $classSuffix = $isFilteredByClass ? '_kelas_' . Str::slug($selectedClass) : '';
        $filename = 'hasil_kuis_' . $quiz->slug . $classSuffix . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($quiz, $isFilteredByClass, $selectedClass) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputs($handle, "sep=;\r\n"); // Instruksikan Excel agar otomatis memisahkan kolom dengan delimiter titik-koma (;)

            // Header
            fputcsv($handle, [
                'Peringkat',
                'NIM',
                'Nama Mahasiswa',
                'Kelas',
                'Skor (0-100)',
                'Poin Diperoleh',
                'Total Poin Kuis',
                'Jawaban Benar',
                'Jawaban Salah',
                'Durasi (Detik)',
                'Pelanggaran Tab Switch',
                'Waktu Mulai',
                'Waktu Selesai',
            ], ';');

            $attemptsQuery = $quiz->attempts()
                ->whereNotNull('completed_at');

            if ($isFilteredByClass) {
                $attemptsQuery->where('kelas', $selectedClass);
            }

            $attempts = $attemptsQuery
                ->orderByDesc('score')
                ->orderBy('duration_seconds')
                ->get();

            $rank = 1;
            foreach ($attempts as $att) {
                fputcsv($handle, [
                    $rank++,
                    $this->sanitizeCsvField($att->nim),
                    $this->sanitizeCsvField($att->student_name),
                    $this->sanitizeCsvField($att->kelas ?? '-'),
                    $att->score,
                    $att->total_points_earned,
                    $att->total_points_possible,
                    $att->correct_answers_count,
                    $att->wrong_answers_count,
                    $att->duration_seconds,
                    $att->tab_switch_count,
                    $att->started_at ? $att->started_at->format('Y-m-d H:i:s') : '-',
                    $att->completed_at ? $att->completed_at->format('Y-m-d H:i:s') : '-',
                ], ';');
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

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kunci pengerjaan mahasiswa {$attempt->student_name} ({$attempt->nim}) berhasil dibuka!",
            ]);
        }

        return back()->with('success', "Kunci pengerjaan mahasiswa {$attempt->student_name} ({$attempt->nim}) berhasil dibuka!");
    }

    public function liveMonitor(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->loadCount(['questions', 'attempts']);
        return view('admin.quizzes.monitor', compact('quiz'));
    }

    public function liveMonitorData(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        // Preload counts to eliminate N+1 queries during 5s polling
        $quiz->loadCount('questions');
        $totalQuestions = (int) $quiz->questions_count;

        $attempts = $quiz->attempts()
            ->withCount('answers')
            ->latest('started_at')
            ->get();

        $activeAttempts = [];
        $completedAttempts = [];

        foreach ($attempts as $att) {
            $att->setRelation('quiz', $quiz);
            $isCompleted = $att->isCompleted();
            $isOnline = $att->isOnline();
            $answeredCount = $att->answeredQuestionsCount();
            $flaggedCount = $att->flaggedQuestionsCount();
            $timeRemainingSeconds = $att->timeRemainingSeconds($quiz);

            $data = [
                'id'                     => $att->id,
                'nim'                    => $att->nim,
                'student_name'           => $att->student_name,
                'kelas'                  => $att->kelas,
                'is_completed'           => $isCompleted,
                'is_online'              => $isOnline,
                'is_locked'              => (bool) $att->is_locked,
                'lock_reason'            => $att->lock_reason,
                'tab_switch_count'       => (int) $att->tab_switch_count,
                'answered_count'         => $answeredCount,
                'flagged_count'          => $flaggedCount,
                'total_questions'        => $totalQuestions,
                'progress_percent'       => $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0,
                'score'                  => $att->score,
                'extra_time_minutes'     => (int) ($att->extra_time_minutes ?? 0),
                'time_remaining_seconds' => $timeRemainingSeconds,
                'time_remaining_human'   => $timeRemainingSeconds !== null ? gmdate('i\m s\s', max(0, $timeRemainingSeconds)) : 'Tanpa Batas',
                'started_at_human'       => $att->started_at ? $att->started_at->format('H:i:s') : '-',
                'last_heartbeat_human'   => $att->last_heartbeat_at ? $att->last_heartbeat_at->diffForHumans() : 'Belum ada sinyal',
                'is_force_submitted'     => (bool) $att->is_force_submitted,
            ];

            if ($isCompleted) {
                $completedAttempts[] = $data;
            } else {
                $activeAttempts[] = $data;
            }
        }

        $announcements = $quiz->announcements()
            ->where('is_active', true)
            ->latest()
            ->take(15)
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'message'          => $a->message,
                'type'             => $a->type,
                'time'             => $a->created_at->format('H:i'),
                'created_at_human' => $a->created_at->diffForHumans(),
            ]);

        $isPaused = $quiz->isSessionPaused();
        $pauseDuration = ($isPaused && $quiz->paused_at) ? abs((int) now()->diffInSeconds($quiz->paused_at)) : 0;

        return response()->json([
            'success'                => true,
            'session_status'         => $quiz->session_status ?? 'waiting',
            'is_paused'              => $isPaused,
            'paused_at'              => $quiz->paused_at ? $quiz->paused_at->toIso8601String() : null,
            'pause_duration_seconds' => $pauseDuration,
            'summary'   => [
                'session_status'  => $quiz->session_status ?? 'waiting',
                'is_paused'       => $isPaused,
                'total_active'    => count($activeAttempts),
                'total_completed' => count($completedAttempts),
                'total_locked'    => count(array_filter($activeAttempts, fn($a) => $a['is_locked'])),
                'total_online'    => count(array_filter($activeAttempts, fn($a) => $a['is_online'])),
                'quiz_time_limit' => $quiz->time_limit_minutes,
            ],
            'announcements' => $announcements,
            'active'        => $activeAttempts,
            'completed'     => $completedAttempts,
        ]);
    }

    public function projectorLeaderboard(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $quiz->loadCount(['questions', 'attempts']);
        return view('admin.quizzes.projector', compact('quiz'));
    }

    public function projectorLeaderboardData(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $questions = $quiz->questions()->with('options')->get();
        $totalQuestions = $questions->count();

        // Index question configurations for fast in-memory scoring
        $questionLookup = [];
        foreach ($questions as $q) {
            $isMulti = $q->isMultiSelect();
            if ($isMulti) {
                $correctOptionIds = $q->options->where('is_correct', true)->pluck('id')->map(fn($id) => (int)$id)->sort()->values()->all();
            } else {
                $correctOption = $q->options->firstWhere('is_correct', true);
                $correctOptionIds = $correctOption ? (int) $correctOption->id : null;
            }
            $questionLookup[$q->id] = [
                'is_multi'    => $isMulti,
                'correct_ids' => $correctOptionIds,
                'points'      => (int) ($q->points ?? 1),
            ];
        }

        $attempts = $quiz->attempts()->latest('started_at')->get();
        $rankedList = [];

        foreach ($attempts as $att) {
            $isCompleted = $att->isCompleted();
            $points = 0;
            $correctCount = 0;
            $answeredCount = 0;

            if ($isCompleted) {
                $points = (int) ($att->total_points_earned ?? 0);
                $correctCount = (int) ($att->correct_answers_count ?? 0);
                $answeredCount = $totalQuestions;
                $duration = (int) ($att->duration_seconds ?? ($att->started_at && $att->completed_at ? abs((int)$att->completed_at->diffInSeconds($att->started_at)) : 999999));
            } else {
                $draft = $att->draft_answers ?? [];
                unset($draft['__flagged']);
                $answeredCount = count($draft);

                foreach ($draft as $qId => $userAns) {
                    if (isset($questionLookup[$qId])) {
                        $cfg = $questionLookup[$qId];
                        $isCorrect = false;

                        if ($cfg['is_multi']) {
                            $selected = is_array($userAns) ? array_map('intval', $userAns) : [];
                            sort($selected);
                            $isCorrect = ($selected === $cfg['correct_ids']);
                        } else {
                            $isCorrect = ($cfg['correct_ids'] !== null && (int) $userAns === (int) $cfg['correct_ids']);
                        }

                        if ($isCorrect) {
                            $points += $cfg['points'];
                            $correctCount++;
                        }
                    }
                }

                $duration = $att->started_at ? abs((int) now()->diffInSeconds($att->started_at)) : 0;
            }

            $progressPercent = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0;

            $rankedList[] = [
                'id'                      => $att->id,
                'nim'                     => $att->nim,
                'student_name'            => $att->student_name,
                'kelas'                   => $att->kelas ?? '-',
                'points'                  => $points, // Digunakan HANYA di server untuk perankingan!
                'duration_seconds'        => $duration,
                'answered_count'          => $answeredCount,
                'correct_count'           => $correctCount,
                'total_questions'         => $totalQuestions,
                'progress_percent'        => $progressPercent,
                'is_completed'            => $isCompleted,
                'is_online'               => $att->isOnline(),
            ];
        }

        // Sort participants (Selaras dengan Hasil Resmi & Export CSV):
        // 1. Poin benar tertinggi (points DESC)
        // 2. Durasi pengerjaan tersingkat / paling cepat (duration_seconds ASC)
        // 3. ID ASC (tie-breaker stabil)
        usort($rankedList, function ($a, $b) {
            if ($b['points'] !== $a['points']) {
                return $b['points'] <=> $a['points'];
            }
            if ($a['duration_seconds'] !== $b['duration_seconds']) {
                return $a['duration_seconds'] <=> $b['duration_seconds'];
            }
            return $a['id'] <=> $b['id'];
        });

        // Sanitasi payload: TIDAK menyertakan angka poin/skor demi privasi mahasiswa!
        $sanitizedLeaderboard = [];
        foreach ($rankedList as $idx => $item) {
            $rank = $idx + 1;
            $sanitizedLeaderboard[] = [
                'id'               => $item['id'],
                'rank'             => $rank,
                'nim'              => $item['nim'],
                'student_name'     => $item['student_name'],
                'kelas'            => $item['kelas'],
                'answered_count'   => $item['answered_count'],
                'total_questions'  => $item['total_questions'],
                'progress_percent' => $item['progress_percent'],
                'is_completed'     => $item['is_completed'],
                'is_online'        => $item['is_online'],
            ];
        }

        $latestAnnouncement = $quiz->announcements()
            ->where('is_active', true)
            ->latest()
            ->first();

        return response()->json([
            'success'             => true,
            'quiz_title'          => $quiz->title,
            'session_status'      => $quiz->session_status ?? 'waiting',
            'total_students'      => count($sanitizedLeaderboard),
            'total_completed'     => count(array_filter($sanitizedLeaderboard, fn($x) => $x['is_completed'])),
            'total_questions'     => $totalQuestions,
            'time_limit'          => $quiz->time_limit_minutes,
            'leaderboard'         => $sanitizedLeaderboard,
            'latest_announcement' => $latestAnnouncement ? [
                'id'      => $latestAnnouncement->id,
                'message' => $latestAnnouncement->message,
                'type'    => $latestAnnouncement->type,
                'time'    => $latestAnnouncement->created_at->format('H:i'),
            ] : null,
        ]);
    }

    public function startSession(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->update([
            'session_status' => 'started',
            'starts_at'      => $quiz->starts_at ?? now(),
        ]);

        // Atur started_at untuk seluruh attempt yang sedang menunggu agar timer mereka mulai dari detik ini
        $quiz->attempts()
            ->whereNull('completed_at')
            ->update([
                'started_at'        => now(),
                'last_heartbeat_at' => now(),
            ]);

        $msg = 'Kuis berhasil dimulai! Seluruh mahasiswa di ruang tunggu kini dapat mengerjakan soal secara serentak.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'session_status' => 'started',
                'message'        => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    public function pauseSession(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->update([
            'session_status' => 'paused',
            'paused_at'      => now(),
        ]);

        cache()->put("quiz_{$quiz->id}_status", 'paused', 120);

        $msg = 'Ujian berhasil dijeda! Hitungan mundur dan layar ujian seluruh mahasiswa telah dibekukan sementara.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'session_status' => 'paused',
                'paused_at'      => $quiz->paused_at->toIso8601String(),
                'message'        => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    public function resumeSession(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $pausedAt = $quiz->paused_at ?? now();
        $pauseDurationSeconds = max(0, (int) (now()->timestamp - $pausedAt->timestamp));

        // Shift started_at forward for all active attempts so paused duration does NOT eat into students' exam time!
        if ($pauseDurationSeconds > 0) {
            $activeAttempts = $quiz->attempts()->whereNull('completed_at')->get();
            foreach ($activeAttempts as $att) {
                if ($att->started_at) {
                    $att->update([
                        'started_at'        => $att->started_at->addSeconds($pauseDurationSeconds),
                        'last_heartbeat_at' => now(),
                    ]);
                }
            }
        }

        $quiz->update([
            'session_status' => 'started',
            'paused_at'      => null,
        ]);

        cache()->put("quiz_{$quiz->id}_status", 'started', 120);

        $minutes = floor($pauseDurationSeconds / 60);
        $seconds = $pauseDurationSeconds % 60;
        $durStr = $minutes > 0 ? "{$minutes} menit {$seconds} detik" : "{$seconds} detik";
        $msg = "Ujian berhasil dilanjutkan! Kompensasi jeda ({$durStr}) telah otomatis ditambahkan ke seluruh mahasiswa aktif.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'                => true,
                'session_status'         => 'started',
                'pause_duration_seconds' => $pauseDurationSeconds,
                'pause_duration_human'   => $durStr,
                'message'                => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    public function endSession(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->update([
            'session_status' => 'ended',
            'ends_at'        => now(),
        ]);

        // Segera perbarui cache status agar seluruh browser mahasiswa langsung mendeteksi seketika
        cache()->put("quiz_{$quiz->id}_status", 'ended', 120);
        cache()->forget("quiz_{$quiz->id}_waiting_count");

        // Kumpulkan dan finalisasi seluruh attempt aktif yang belum selesai
        $activeAttempts = $quiz->attempts()->whereNull('completed_at')->get();
        foreach ($activeAttempts as $attempt) {
            $attempt->update(['is_force_submitted' => true]);
            \App\Services\QuizScoringService::finalize($attempt, $attempt->draft_answers ?? []);
        }

        $msg = 'Kuis telah berhasil dihentikan. Seluruh lembar ujian mahasiswa yang sedang aktif telah dikumpulkan dan dinilai secara otomatis.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'         => true,
                'session_status'  => 'ended',
                'message'         => $msg,
                'finalized_count' => $activeAttempts->count(),
            ]);
        }

        return back()->with('success', $msg);
    }

    public function resetSession(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->update([
            'session_status' => 'waiting',
        ]);

        $msg = 'Sesi kuis berhasil direset kembali ke status Ruang Tunggu.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'session_status' => 'waiting',
                'message'        => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    public function broadcastAnnouncement(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $validated = $request->validate([
            'message' => 'required|string|min:2|max:1000',
            'type'    => ['required', 'string', Rule::in(['info', 'warning', 'urgent'])],
        ], [
            'message.required' => 'Pesan pengumuman tidak boleh kosong.',
            'message.min'      => 'Pesan pengumuman minimal 2 karakter.',
            'message.max'      => 'Pesan pengumuman maksimal 1000 karakter.',
            'type.in'          => 'Tipe pengumuman tidak valid.',
        ]);

        $announcement = $quiz->announcements()->create([
            'user_id'   => auth()->id(),
            'message'   => trim($validated['message']),
            'type'      => $validated['type'],
            'is_active' => true,
        ]);

        // Invalidate cache so all students receive it on their next 3-second heartbeat
        cache()->forget("quiz_{$quiz->id}_announcements");

        $data = [
            'id'               => $announcement->id,
            'message'          => $announcement->message,
            'type'             => $announcement->type,
            'time'             => $announcement->created_at->format('H:i'),
            'created_at_human' => $announcement->created_at->diffForHumans(),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Pengumuman berhasil disiarkan ke seluruh mahasiswa!',
                'announcement' => $data,
            ]);
        }

        return back()->with('success', 'Pengumuman berhasil disiarkan ke seluruh mahasiswa!');
    }

    public function destroyAnnouncement(Request $request, Quiz $quiz, QuizAnnouncement $announcement)
    {
        $this->authorizeQuiz($quiz);

        if ($announcement->quiz_id !== $quiz->id) {
            abort(404);
        }

        $announcement->delete();
        cache()->forget("quiz_{$quiz->id}_announcements");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function addTimeAttempt(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorizeQuiz($quiz);
        if ($attempt->quiz_id !== $quiz->id) abort(404);

        if ($attempt->isCompleted()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi mahasiswa ini telah selesai.'], 422);
            }
            return back()->with('error', 'Sesi mahasiswa ini telah selesai dan tidak dapat ditambah waktu.');
        }

        $request->validate([
            'minutes' => 'required|integer|min:1|max:60',
        ]);

        $addedMinutes = (int) $request->minutes;
        $attempt->increment('extra_time_minutes', $addedMinutes);

        $logs = $attempt->violations_log ?? [];
        $logs[] = [
            'type'        => 'extra_time_added_by_admin',
            'added'       => "+{$addedMinutes} menit",
            'by'          => auth()->user()->name,
            'timestamp'   => now()->toIso8601String(),
        ];
        $attempt->violations_log = $logs;
        $attempt->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Waktu ujian {$attempt->student_name} ({$attempt->nim}) berhasil ditambah {$addedMinutes} menit!",
                'extra_time_minutes' => $attempt->extra_time_minutes,
            ]);
        }

        return back()->with('success', "Waktu ujian {$attempt->student_name} berhasil ditambah {$addedMinutes} menit!");
    }

    public function forceSubmitAttempt(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorizeQuiz($quiz);
        if ($attempt->quiz_id !== $quiz->id) abort(404);

        if ($attempt->isCompleted()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi mahasiswa ini sudah selesai sebelumnya.'], 422);
            }
            return back()->with('error', 'Sesi mahasiswa ini sudah selesai sebelumnya.');
        }

        QuizScoringService::finalize($attempt, $attempt->draft_answers ?? [], true);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sesi ujian {$attempt->student_name} ({$attempt->nim}) telah dikumpulkan secara paksa (Force Submit). Skor berhasil dihitung!",
            ]);
        }

        return back()->with('success', "Sesi ujian {$attempt->student_name} ({$attempt->nim}) berhasil disubmit secara paksa!");
    }

    public function downloadQuestionsTemplate(Request $request, Quiz $quiz): StreamedResponse|BinaryFileResponse
    {
        $this->authorizeQuiz($quiz);
        $format = strtolower((string)$request->query('format', 'excel'));

        if ($format === 'excel' || $format === 'xlsx') {
            return QuizImportService::downloadXlsxTemplate();
        }

        $delimiter = ($format === 'csv' || $format === 'comma' || $format === 'standar') ? ',' : ';';
        return QuizImportService::downloadTemplate($delimiter);
    }

    public function importQuestions(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'file' => 'required|file|max:10240|mimes:csv,txt,xlsx',
        ], [
            'file.required' => 'Pilih file template CSV atau Excel (.xlsx) untuk diimpor.',
            'file.mimes'    => 'Format file harus berupa CSV (.csv) atau Excel (.xlsx).',
            'file.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $result = QuizImportService::import($quiz, $request->file('file'));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
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
