<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda belum diaktifkan atau telah dinonaktifkan. Hubungi Super Admin.',
            ]);
        }

        return $next($request);
    }
}
