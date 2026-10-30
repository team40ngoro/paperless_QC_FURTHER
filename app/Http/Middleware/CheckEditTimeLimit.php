<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckEditTimeLimit
{
    /**
     * Handle an incoming request.
     *
     * Middleware global untuk membatasi waktu edit berdasarkan role.
     * Cek config/edit_limit.php untuk konfigurasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Hanya cek untuk method PUT/PATCH (update) dan DELETE
        if (!in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        // Cek apakah role user ada di daftar yang dibatasi
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        $userRoleId = $user->id_role;

        if (!in_array($userRoleId, $restrictedRoles)) {
            // Role ini tidak dibatasi, lanjutkan
            return $next($request);
        }

        // Identifikasi modul dari URL segment
        $segment2 = $request->segment(2);
        $modules = config('edit_limit.modules', []);

        // Untuk wizard routes: /qc-sistem/wizard/proses-X/update/{uuid}
        // segment(2)=wizard, segment(3)=proses-X
        if ($segment2 === 'wizard' || $segment2 === 'wizards') {
            $module = $request->segment(3);
        } else {
            $module = $segment2;
        }

        // Cek apakah modul ini terdaftar di config
        if (!isset($modules[$module])) {
            return $next($request);
        }

        // Resolve model class dan uuid_segment dari config
        $moduleConfig = $modules[$module];
        if (is_array($moduleConfig)) {
            $modelClass = $moduleConfig['model'];
            $uuidSegment = $moduleConfig['uuid_segment'] ?? 3;
        } else {
            $modelClass = $moduleConfig;
            $uuidSegment = 3;
        }

        // Dapatkan UUID dari URL segment
        $uuid = $request->segment($uuidSegment);

        if (!$uuid) {
            return $next($request);
        }

        // Cari record berdasarkan UUID
        $record = $modelClass::where('uuid', $uuid)->first();

        if (!$record) {
            // Record tidak ditemukan, biarkan controller yang handle 404
            return $next($request);
        }

        // Hitung selisih waktu
        $timeLimitHours = config('edit_limit.time_limit_hours', 48);
        $createdAt = Carbon::parse($record->created_at);
        $hoursElapsed = $createdAt->diffInHours(now(), true);

        if ($hoursElapsed > $timeLimitHours) {
            $errorMessage = config('edit_limit.error_message', 'Waktu edit sudah habis.');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 403);
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }

        return $next($request);
    }
}
