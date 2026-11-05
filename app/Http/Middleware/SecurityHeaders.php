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

        // HTTP Browser Cache Control untuk Aset Statis & Respons Publik
        if ($request->is('build/*') || $request->is('images/*') || $request->is('favicon.ico')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } elseif ($request->is('storage/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000'); // 30 hari untuk gambar/media soal
        } elseif (!$request->isMethodSafe() || $request->routeIs('login', 'logout', 'admin.*', 'profile.*')) {
            // Halaman dinamis & form admin tetap no-store agar data selalu up to date
            $response->headers->set('Cache-Control', 'no-cache, private');
        }

        return $response;
    }
}
