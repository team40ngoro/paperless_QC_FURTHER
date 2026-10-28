<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Memberi tahu browser bahwa response download (PDF/Excel) sudah dikirim.
 *
 * form-submission-handler.js menyisipkan hidden input `download_token`.
 * Jika response punya header Content-Disposition (attachment/inline file),
 * token yang sama dikirim balik sebagai cookie `download_token`,
 * lalu JS membuka kembali tombol yang sedang loading.
 *
 * Simpan sebagai: app/Http/Middleware/DownloadTokenCookie.php
 */
class DownloadTokenCookie
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $token = (string) $request->input('download_token', '');
        
        if (
            preg_match('/^[A-Za-z0-9]{8,64}$/', $token)
            && $response->headers->has('Content-Disposition')
        ) {
            // name, value, menit, path, domain, secure, httpOnly (HARUS false agar terbaca JS)
            $response->headers->setCookie(
                cookie('download_token', $token, 1, '/', null, false, false)
            );
        }

        return $response;
    }
}
