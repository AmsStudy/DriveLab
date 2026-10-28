<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Terapkan header keamanan HTTP standar industri pada seluruh respons web.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah serangan Clickjacking (iframe embedding dari luar)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Mencegah browser mengeksekusi konten dengan MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Mengatur kebijakan referrer agar tidak membocorkan query parameter sensitif ke domain luar
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Membatasi akses browser API yang tidak diperlukan (kamera, mic, geolokasi)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Proteksi filter XSS untuk browser legacy
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
