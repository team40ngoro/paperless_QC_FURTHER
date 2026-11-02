<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SsoLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['ticket' => 'required|uuid']);

        // Verifikasi tiket ke Employee API (komunikasi server-ke-server)
        $response = Http::withToken(config('services.employee_api.sso_secret'))
            ->timeout(10)
            ->post(config('services.employee_api.url').'/sso/verify', [
                'ticket' => $request->ticket,
                'project_uuid' => config('services.employee_api.this_project_uuid'),
            ]);

        // Kasus: user memang tidak punya akses ke project ini
        if ($response->status() === 403) {
            return redirect('/login')->withErrors([
                'sso' => 'Anda tidak memiliki akses ke sistem ini.',
            ]);
        }

        // Kasus umum: tiket tidak valid / sudah kedaluwarsa / sudah dipakai
        if ($response->failed()) {
            return redirect('/login')->withErrors([
                'sso' => 'Sesi login otomatis tidak valid atau sudah kedaluwarsa. Silakan login manual.',
            ]);
        }

        $remoteUser = $response->json('user');

        // Cocokkan user berdasarkan uuid (database bersama)
        $user = User::where('uuid', $remoteUser['uuid'])->first();

        if (! $user) {
            return redirect('/login')->withErrors([
                'sso' => 'Akun tidak ditemukan di sistem ini.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function ssoLogout(Request $request)
    {
        $token = $request->bearerToken();
        $expected = config('services.employee_api.sso_secret');

        if (! $token || ! $expected || ! hash_equals($expected, $token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate(['user_uuid' => 'required|uuid']);

        User::where('uuid', $request->user_uuid)
            ->update(['force_logout_after' => now()]);

        return response()->json(['status' => 'ok']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            try {
                Http::withToken(config('services.employee_api.sso_secret'))
                    ->timeout(5)
                    ->post(config('services.employee_api.url').'/sso/report-logout', [
                        'user_uuid' => $user->uuid,
                        'project_uuid' => config('services.employee_api.this_project_uuid'),
                    ]);
            } catch (\Throwable $e) {
                // jangan blokir proses logout lokal walau central tidak terjangkau
            }
        }

        return redirect(config('services.employee_api.portal_url'));
    }
}
