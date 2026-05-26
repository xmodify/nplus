<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ✅ Anti-clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ✅ Anti-MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ✅ Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ✅ Permissions Policy (Restrict unused browser features)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // ✅ Content Security Policy
        // 'unsafe-inline' จำเป็นเพราะ Blade views ใช้ inline <script> และ <style> ทั่วทั้งโปรเจกต์
        $csp = implode('; ', [
            // Fallback สำหรับทุก resource type
            "default-src 'self'",

            // JavaScript: self + inline scripts ใน Blade
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",

            // CSS: self + Google Fonts + inline styles
            "style-src 'self' 'unsafe-inline' " .
                "fonts.googleapis.com",

            // Fonts: self + Google Fonts
            "font-src 'self' " .
                "fonts.gstatic.com",

            // Images: self + data: URI (ที่ Bootstrap ใช้)
            "img-src 'self' data:",

            // Connections (AJAX, Fetch): self เท่านั้น
            "connect-src 'self'",

            // ห้าม embed ในหน้าของคนอื่น (ซ้ำซ้อนกับ X-Frame-Options แต่ใช้ด้วยกันได้)
            "frame-ancestors 'self'",

            // Worker (jszip ใช้ Blob URL)
            "worker-src 'self' blob:",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
