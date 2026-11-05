<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function applyDateRangeFilter(Request $request, $query, string $dateColumn = 'tanggal'): array
    {
        $rules = [
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_akhir' => ['nullable', 'date_format:Y-m-d'],
        ];

        if ($request->filled('tanggal_mulai')) {
            $rules['tanggal_akhir'][] = 'after_or_equal:tanggal_mulai';
        }

        $dates = $request->validate($rules);
        $tanggalMulai = $dates['tanggal_mulai'] ?? null;
        $tanggalAkhir = $dates['tanggal_akhir'] ?? null;

        if ($tanggalMulai) {
            $query->whereDate($dateColumn, '>=', $tanggalMulai);
        }
        if ($tanggalAkhir) {
            $query->whereDate($dateColumn, '<=', $tanggalAkhir);
        }

        return compact('tanggalMulai', 'tanggalAkhir');
    }

    /**
     * Tentukan id_plan saat menyimpan data.
     * Superadmin tidak memiliki id_plan, sehingga plan diturunkan dari produk
     * atau record induk yang dipilih.
     */
    protected function resolvePlanId($user, $produkId = null, $fallback = null)
    {
        if (! $user || $user->role !== 'superadmin') {
            return $user->id_plan;
        }

        $produkId = $produkId ?: request('id_produk');

        if ($produkId) {
            $planId = \App\Models\JenisProduk::whereKey($produkId)->value('id_plan');
            if ($planId) {
                return $planId;
            }
        }

        if ($fallback) {
            return $fallback;
        }

        return $user->id_plan;
    }

    protected function redirectToCanonicalAuditUrl(string $routeName, object $record): ?RedirectResponse
    {
        $isAuditMode = (bool) ($record->is_audit ?? false);
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            $route = app('router')->getRoutes()->getByName($routeName);
            if ($route === null) {
                throw new \InvalidArgumentException("Route [{$routeName}] is not defined.");
            }

            $routeParameters = $route->parameterNames();
            if (count($routeParameters) !== 1) {
                throw new \LogicException("Route [{$routeName}] must have exactly one record parameter.");
            }

            return redirect()->route(
                $routeName,
                array_merge([$routeParameters[0] => $record->uuid], $queryParameters)
            );
        }

        return null;
    }
}
