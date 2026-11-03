<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckQcPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 1. Get the current route prefix or segment that identifies the form module
        // Example path: qc-sistem/bahan-baku-roasting/create -> segment(2) is bahan-baku-roasting
        $module = $request->segment(2);

        // Bypass permission check for Berat Produk module
        // - GET create is needed for navigation from other modules
        // - POST store is needed so saving data is not blocked by permission checks
        if ($module === 'berat-produk') {
            if ($request->method() === 'GET' && $request->segment(3) === 'create') {
                return $next($request);
            }

            if ($request->method() === 'POST') {
                return $next($request);
            }
        }

        // Verifikasi QC hanya untuk role QC; otorisasi dilakukan di controller,
        // sehingga tidak boleh memakai permission create-chillroom.
        if (
            str_contains($request->path(), 'qc-verification') ||
            str_ends_with($request->path(), '/verifikasi-bahan-kemas') ||
            str_ends_with($request->path(), '/verifikasi-kedatangan') ||
            str_ends_with($request->path(), 'bulk-export-excel') ||
            str_ends_with($request->path(), '/approve') ||
            str_ends_with($request->path(), '/bulk-approve') ||
            str_ends_with($request->path(), '/bulk-approve-selected')
        ) {
            return $next($request);
        }

        // Allow AJAX requests bypassing the strict structure Check
        if ($request->segment(1) === 'ajax' || $module === 'ajax' || $request->ajax() || $module === 'get-emulsi-by-produk') {
            return $next($request);
        }

        // Allow all bulk-export-pdf routes
        if (str_ends_with($request->path(), 'bulk-export-pdf')) {
            return $next($request);
        }

        // Bypass for Wizard Process (wizard singular and wizards plural)
        if ($request->segment(2) === 'wizard' || $request->segment(2) === 'wizards') {
            // Determine action first
            $method = $request->method();
            $segment3 = $request->segment(3);
            $segment4 = $request->segment(4);
            $action = 'view';
            if ($method === 'POST') {
                $action = 'create';
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'edit';
            } elseif ($method === 'DELETE') {
                $action = 'delete';
            } elseif ($method === 'GET') {
                if ($segment3 === 'create') {
                    $action = 'create';
                } elseif ($segment4 === 'edit') {
                    $action = 'edit';
                }
            }

            // DEBUG: Log the values to understand what's happening
            Log::info('CheckQcPermission Wizard Debug', [
                'url' => $request->fullUrl(),
                'segment2' => $request->segment(2),
                'segment3' => $segment3,
                'segment4' => $segment4,
                'action' => $action,
                'method' => $method,
            ]);

            // If it's a 'view' action, allow it (will be filtered later by Blade or controller)
            // Also allow audit pages of wizard without strict permission check
            if (
                $action === 'view' ||
                ($segment4 === 'audit-data') ||
                ($segment4 === 'audit') ||
                (in_array($segment3, ['proses1', 'proses-1'], true) && in_array($action, ['create', 'edit'], true) && in_array((int) ($user->id_role ?? 0), [1, 2, 5], true))
            ) {
                Log::info('CheckQcPermission: Allowing wizard view/audit', ['action' => $action, 'segment4' => $segment4]);

                return $next($request);
            }

            // For create/edit/delete in Wizard, use the entry gate check or specific permissions
            if (! $user->hasPermissionTo('create-proses-penggorengan') && ! $user->hasPermissionTo('edit-proses-penggorengan')) {
                // If the user has specific wizard permissions, allow them too
                $isWizardAction = false;
                $normalizedSegment3 = str_replace('-', '', $segment3 ?? '');

                // Build all permission candidates to check
                $permCandidates = [];

                if ($segment3 === 'packing') {
                    // wizards/packing → check "create-wizard-packing", "edit-wizard-packing", etc.
                    $permCandidates[] = $action.'-wizard-packing';
                } elseif ($segment3 && preg_match('/^proses[1-4]$/', $normalizedSegment3)) {
                    // wizard/proses-1..4 → check both "delete-wizard-proses-4" and "delete-wizard-proses4"
                    $permCandidates[] = $action.'-wizard-'.$segment3;
                    $permCandidates[] = $action.'-wizard-'.$normalizedSegment3;
                } elseif ($segment3) {
                    // fallback
                    $permCandidates[] = $action.'-wizard-'.$segment3;
                    $permCandidates[] = $action.'-wizard-'.$normalizedSegment3;
                }

                $lastPermChecked = 'none';
                foreach ($permCandidates as $permCandidate) {
                    $lastPermChecked = $permCandidate;
                    if ($user->hasPermissionTo($permCandidate)) {
                        $isWizardAction = true;
                        break;
                    }
                }

                if (! $isWizardAction) {
                    Log::warning('CheckQcPermission: Denying wizard action', [
                        'user_id' => $user->id,
                        'user_role' => $user->id_role,
                        'action' => $action,
                        'segment3' => $segment3,
                        'segment4' => $segment4,
                        'perm_candidates' => $permCandidates,
                        'last_perm_checked' => $lastPermChecked,
                    ]);
                    if ($request->wantsJson()) {
                        return response()->json(['message' => 'Anda tidak memiliki hak akses untuk melakukan aksi ini.'], 403);
                    }

                    return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
                }
            }

            return $next($request);
        }

        if ($user && $module) {
            // Determine action based on HTTP method and path
            $action = 'view'; // Default is view (index, show)

            $method = $request->method();
            $segment3 = $request->segment(3);
            $segment4 = $request->segment(4); // e.g. /qc-sistem/module/{id}/edit

            if ($method === 'POST') {
                $action = 'create'; // store
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'edit'; // update
            } elseif ($method === 'DELETE') {
                $action = 'delete'; // destroy
            } elseif ($method === 'GET') {
                if ($segment3 === 'create') {
                    $action = 'create';
                } elseif ($segment4 === 'edit') {
                    $action = 'edit';
                }
            }

            // Pengecualian nama module untuk URL yang berbeda dengan nama Permission
            $moduleMap = [
                'penggorengan' => 'proses-penggorengan',
                'berat-produk' => 'berat_produk_bag_box',
            ];

            $permissionModule = $moduleMap[$module] ?? $module;

            // Determine permission string based on CRUD
            $permissionRequired = $action.'-'.$permissionModule;

            // Terapkan perlindungan HANYA untuk aksi Create, Edit, dan Delete (Action != view)
            // Sehingga user bebas 'Melihat' halaman, namun tombol aksinya akan diatur lewat Blade
            if ($action !== 'view' && ! $user->hasPermissionTo($permissionRequired)) {

                // Jika request adalah JSON/AJAX, kembalikan JSON 403
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Anda tidak memiliki hak akses untuk melakukan aksi ini.'], 403);
                }

                // Teruskan kemana dia berasal atau ke dashboard
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
            }
        }

        return $next($request);
    }
}
