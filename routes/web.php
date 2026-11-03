<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicQuizController;
use App\Http\Controllers\SoalAccessController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik ───────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/csrf-refresh', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.refresh');

Route::post('/akses-kode', [SoalAccessController::class, 'accessByCode'])->name('soal.accessByCode');

// Akses Soal (Mahasiswa) — tanpa login
Route::get('/soal/trackTask', [SoalAccessController::class, 'trackTask'])->name('soal.trackTask');
Route::get('/soal/{token}/check-submission', [SoalAccessController::class, 'checkSubmission'])->name('soal.checkSubmission');
Route::post('/soal/{token}/submitTask', [SoalAccessController::class, 'submitTask'])->name('soal.submitTask');

Route::prefix('soal')->name('soal.')->group(function () {
    Route::get('/{token}', [SoalAccessController::class, 'show'])->name('access');
    Route::post('/{token}', [SoalAccessController::class, 'verify'])->name('verify');
    Route::get('/{token}/view', [SoalAccessController::class, 'view'])->name('view');
    Route::get('/{token}/preview-wrapper/{attachment}', [SoalAccessController::class, 'previewWrapper'])->name('preview_wrapper');
    Route::get('/{token}/file/{attachment}', [SoalAccessController::class, 'previewFile'])->name('file');
    Route::get('/{token}/download/{attachment}', [SoalAccessController::class, 'downloadFile'])->name('download');
    Route::get('/{token}/status', [SoalAccessController::class, 'status'])->name('status');
});

// ─── Kuis Interaktif (Mahasiswa) ──────────────────────────────────────────────
Route::post('/quiz/join', [PublicQuizController::class, 'joinByCode'])
    ->middleware('throttle:120,1')
    ->name('quiz.join');

Route::prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/{token}', [PublicQuizController::class, 'lobby'])->name('lobby');
    Route::post('/{token}/start', [PublicQuizController::class, 'start'])
        ->middleware('throttle:120,1')
        ->name('start');
    Route::get('/{token}/play/{attempt}', [PublicQuizController::class, 'play'])->name('play');
    Route::post('/{token}/attempt/{attempt}/tab-switch', [PublicQuizController::class, 'recordTabSwitch'])
        ->middleware('throttle:180,1')
        ->name('tab-switch');
    Route::post('/{token}/attempt/{attempt}/unlock-password', [PublicQuizController::class, 'unlockWithPassword'])
        ->middleware('throttle:30,1')
        ->name('unlock-password');
    Route::post('/{token}/attempt/{attempt}/autosave', [PublicQuizController::class, 'autosave'])
        ->middleware('throttle:300,1')
        ->name('autosave');
    Route::post('/{token}/submit/{attempt}', [PublicQuizController::class, 'submit'])
        ->middleware('throttle:120,1')
        ->name('submit');
    Route::get('/{token}/results/{attempt}', [PublicQuizController::class, 'results'])->name('results');
});

// ─── Auth (Breeze) ────────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Profile (Breeze) ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Panel ─────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.active'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Soal CRUD
        Route::get('soal/trash', [SoalController::class, 'trash'])->name('soal.trash');
        Route::patch('soal/{id}/restore', [SoalController::class, 'restore'])->name('soal.restore');
        Route::delete('soal/{id}/force-delete', [SoalController::class, 'forceDelete'])->name('soal.force-delete');
        Route::resource('soal', SoalController::class);
        Route::patch('soal/{soal}/toggle', [SoalController::class, 'toggleActive'])->name('soal.toggle');
        Route::get('soal/{soal}/preview', [SoalController::class, 'preview'])->name('soal.preview');
        Route::get('soal/attachment/{attachment}/download', [SoalController::class, 'downloadAttachment'])->name('soal.download');
        Route::get('soal/{soal}/export-submissions', [SoalController::class, 'exportSubmissions'])->name('soal.export-submissions');
        Route::get('soal/{soal}/download-submissions-zip', [SoalController::class, 'downloadSubmissionsZip'])->name('soal.download-submissions-zip');
        Route::get('soal/submission/{id}/download', [SoalController::class, 'downloadSubmission'])->name('soal.download-submissions');
        Route::get('soal/submission/{id}/preview', [SoalController::class, 'previewSubmission'])->name('soal.submission.preview');
        Route::delete('soal/submission/{id}', [SoalController::class, 'destroySubmission'])->name('soal.submission.destroy');

        // Kategori CRUD
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Kuis Interaktif (CMS Quizizz)
        Route::resource('quizzes', QuizController::class);
        Route::post('quizzes/{quiz}/duplicate', [QuizController::class, 'duplicate'])->name('quizzes.duplicate');
        Route::patch('quizzes/{quiz}/toggle', [QuizController::class, 'toggleActive'])->name('quizzes.toggle');
        Route::get('quizzes/{quiz}/questions', [QuizController::class, 'questions'])->name('quizzes.questions');
        Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::put('quizzes/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
        Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
        Route::get('quizzes/{quiz}/questions/template', [QuizController::class, 'downloadQuestionsTemplate'])->name('quizzes.questions.template');
        Route::post('quizzes/{quiz}/questions/import', [QuizController::class, 'importQuestions'])->name('quizzes.questions.import');

        // Live Exam Monitoring & Proctoring
        Route::get('quizzes/{quiz}/monitor', [QuizController::class, 'liveMonitor'])->name('quizzes.monitor');
        Route::get('quizzes/{quiz}/monitor/data', [QuizController::class, 'liveMonitorData'])->name('quizzes.monitor.data');
        Route::post('quizzes/{quiz}/attempts/{attempt}/add-time', [QuizController::class, 'addTimeAttempt'])->name('quizzes.attempts.add-time');
        Route::post('quizzes/{quiz}/attempts/{attempt}/force-submit', [QuizController::class, 'forceSubmitAttempt'])->name('quizzes.attempts.force-submit');

        Route::get('quizzes/{quiz}/results', [QuizController::class, 'results'])->name('quizzes.results');
        Route::get('quizzes/{quiz}/export-csv', [QuizController::class, 'exportResultsCsv'])->name('quizzes.export-csv');
        Route::post('quizzes/{quiz}/attempts/{attempt}/unlock', [QuizController::class, 'remoteUnlockAttempt'])->name('quizzes.attempts.unlock');
        Route::delete('quizzes/{quiz}/attempts/{attempt}', [QuizController::class, 'destroyAttempt'])->name('quizzes.attempts.destroy');
        Route::delete('quizzes/{quiz}/reset-attempts', [QuizController::class, 'resetAttempts'])->name('quizzes.reset-attempts');

        // ── Super Admin Only ──────────────────────────────────────────────────
        Route::middleware('superadmin')->group(function () {
            Route::resource('users', UserController::class)->only(['index', 'store', 'show', 'destroy']);
            Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
            Route::patch('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
            Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        });
    });

// Redirect /dashboard ke admin dashboard
Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'admin.active'])
    ->name('dashboard');
