<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan header keamanan pada seluruh respons HTTP.
 *
 * Konten HTML memakai Content-Security-Policy longgar yang masih mengizinkan
 * inline script/style (dipakai Livewire/Filament dan template Blade), tetapi
 * membatasi frame, objek, dan koneksi hanya ke sumber tepercaya. Endpoint
 * non-HTML (JSON, gambar, PDF) hanya mendapat header anti-sniff dan frame.
 */
class SecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        // HSTS hanya bermakna bila koneksi memang HTTPS.
        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        if ($this->isHtml($response)) {
            $headers['Content-Security-Policy'] = $this->contentSecurityPolicy();
            $headers['Permissions-Policy'] = $this->permissionsPolicy();
        }

        foreach ($headers as $name => $value) {
            // Jangan menimpa header yang sudah diset secara spesifik oleh
            // controller (mis. bila suatu halaman butuh frame berbeda).
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }

    private function isHtml(Response $response): bool
    {
        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    /**
     * Catatan: 'unsafe-inline' diperlukan karena Blade dan Filament memakai
     * skrip/gaya inline. Jika nanti ada nonce, hapus 'unsafe-inline'.
     */
    private function contentSecurityPolicy(): string
    {
        $directives = [
            'default-src' => ["'self'"],
            'script-src' => array_merge(
                ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
                config('security.script_sources', []),
            ),
            'style-src' => array_merge(["'self'", "'unsafe-inline'"], config('security.style_sources', [])),
            'font-src' => array_merge(["'self'", 'data:'], config('security.font_sources', [])),
            'img-src' => array_merge(["'self'", 'data:', 'blob:'], config('security.img_sources', [])),
            'connect-src' => array_merge(["'self'"], config('security.connect_sources', [])),
            'frame-src' => config('security.frame_sources', []),
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $directive): string => trim($directive.' '.implode(' ', $sources)))
            ->implode('; ');
    }

    private function permissionsPolicy(): string
    {
        return 'camera=(), microphone=(), geolocation=(), payment=(), usb=()';
    }
}
