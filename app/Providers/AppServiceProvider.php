<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // ─── Rate Limiter Terisolasi per Sesi Klien (Aman untuk 300+ Komputer Lab dalam 1 IP NAT) ───
        RateLimiter::for('quiz-join', function (Request $request) {
            $key = $request->session()->getId() ?: $request->ip();
            return Limit::perMinute(60)->by($key);
        });

        RateLimiter::for('quiz-start', function (Request $request) {
            $key = $request->session()->getId() ?: $request->ip();
            return Limit::perMinute(30)->by($key);
        });

        RateLimiter::for('quiz-autosave', function (Request $request) {
            $attemptParam = $request->route('attempt');
            $attemptId = is_object($attemptParam) ? ($attemptParam->id ?? null) : $attemptParam;
            $key = 'quiz_att_' . ($attemptId ?: ($request->session()->getId() ?: $request->ip()));
            return Limit::perMinute(120)->by($key);
        });

        RateLimiter::for('quiz-submit', function (Request $request) {
            $attemptParam = $request->route('attempt');
            $attemptId = is_object($attemptParam) ? ($attemptParam->id ?? null) : $attemptParam;
            $key = 'quiz_sub_' . ($attemptId ?: ($request->session()->getId() ?: $request->ip()));
            return Limit::perMinute(30)->by($key);
        });

        RateLimiter::for('quiz-tab-switch', function (Request $request) {
            $attemptParam = $request->route('attempt');
            $attemptId = is_object($attemptParam) ? ($attemptParam->id ?? null) : $attemptParam;
            $key = 'quiz_ts_' . ($attemptId ?: ($request->session()->getId() ?: $request->ip()));
            return Limit::perMinute(60)->by($key);
        });

        RateLimiter::for('quiz-unlock', function (Request $request) {
            $attemptParam = $request->route('attempt');
            $attemptId = is_object($attemptParam) ? ($attemptParam->id ?? null) : $attemptParam;
            $key = 'quiz_unl_' . ($attemptId ?: ($request->session()->getId() ?: $request->ip()));
            return Limit::perMinute(20)->by($key);
        });
    }
}
