<?php

use App\Http\Controllers\Admin\AccessLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SoalAccessController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik ───────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/akses-kode', [SoalAccessController::class, 'accessByCode'])->name('soal.accessByCode');

// Akses Soal (Mahasiswa) — tanpa login
Route::get('/soal/trackTask', [SoalAccessController::class, 'trackTask'])->name('soal.trackTask');
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

        // Kategori CRUD
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Log Akses
        Route::get('/logs', [AccessLogController::class, 'index'])->name('logs.index');

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
