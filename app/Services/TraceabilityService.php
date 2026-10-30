<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TraceabilityService
{
    /**
     * Default jumlah form per halaman per modul.
     */
    public const PER_PAGE = 10;

    /**
     * Lakukan pencarian ringan berdasarkan token pencarian.
     * Hasilnya sudah dipaginasi (halaman 1, 10 per modul) dan dilengkapi
     * metadata process_flow serta summary.
     */
    public function traceByBatch(string $searchTerm, int $perPage = self::PER_PAGE): array
    {
        $tokens = $this->tokenize($searchTerm);
        $user = Auth::user();
        $userPlanId = $user ? ($methodExist = method_exists($user, 'getEffectivePlanId') ? $user->getEffectivePlanId() : ($user->id_plan ?? null)) : null;
        $isSuperAdmin = $user && ($user->id_role == 5 || $user->role === 'superadmin');

        $allDates = collect();

        $modules = collect(config('traceability.modules'))
            ->map(function (array $cfg, string $key) use ($tokens, $userPlanId, $isSuperAdmin, $searchTerm, $perPage, &$allDates) {
                if (!class_exists($cfg['model'])) {
                    return null;
                }

                $matched = $this->matchDetails($cfg, $tokens, $userPlanId, $isSuperAdmin);

                if ($matched->isEmpty()) {
                    return null;
                }

                $allForms = $this->buildFormsList($cfg, $matched, $searchTerm);

                if ($allForms->isEmpty()) {
                    return null;
                }

                $totalCount = $allForms->count();
                $paginatedForms = $allForms->slice(0, $perPage)->values();

                // Kumpulkan semua tanggal untuk summary
                $allDates = $allDates->merge($allForms->pluck('date')->filter());

                return [
                    'module'       => $key,
                    'label'        => $cfg['label'],
                    'forms'        => $paginatedForms,
                    'total_count'  => $totalCount,
                    'current_page' => 1,
                    'last_page'    => (int) ceil($totalCount / $perPage),
                    'per_page'     => $perPage,
                ];
            })
            ->filter()
            ->values();

        // Process Flow: daftar semua modul config dengan flag has_data
        $processFlow = collect(config('traceability.modules'))
            ->map(function (array $cfg, string $key) use ($modules) {
                $found = $modules->firstWhere('module', $key);
                return [
                    'module'     => $key,
                    'label'      => $cfg['label'],
                    'has_data'   => $found !== null,
                    'total'      => $found ? $found['total_count'] : 0,
                ];
            })
            ->values();

        // Summary
        $totalForms = $modules->sum('total_count');
        $modulesWithData = $modules->count();
        $totalModules = count(config('traceability.modules'));
        $sortedDates = $allDates->filter()->sort()->values();
        $summary = [
            'total_forms'       => $totalForms,
            'modules_with_data' => $modulesWithData,
            'total_modules'     => $totalModules,
            'date_from'         => $sortedDates->first(),
            'date_to'           => $sortedDates->last(),
        ];

        return [
            'modules'      => $modules,
            'process_flow' => $processFlow,
            'summary'      => $summary,
        ];
    }

    /**
     * Pagination AJAX untuk satu modul tertentu.
     * Digunakan saat user klik Next/Prev pada grup modul.
     */
    public function paginateModuleForms(string $moduleKey, string $searchTerm, int $page = 1, int $perPage = self::PER_PAGE): ?array
    {
        $cfg = config("traceability.modules.{$moduleKey}");
        if (!$cfg || !class_exists($cfg['model'])) {
            return null;
        }

        $tokens = $this->tokenize($searchTerm);
        $user = Auth::user();
        $userPlanId = $user ? (method_exists($user, 'getEffectivePlanId') ? $user->getEffectivePlanId() : ($user->id_plan ?? null)) : null;
        $isSuperAdmin = $user && ($user->id_role == 5 || $user->role === 'superadmin');

        $matched = $this->matchDetails($cfg, $tokens, $userPlanId, $isSuperAdmin);

        if ($matched->isEmpty()) {
            return [
                'forms'        => [],
                'total_count'  => 0,
                'current_page' => $page,
                'last_page'    => 1,
                'per_page'     => $perPage,
            ];
        }

        $allForms = $this->buildFormsList($cfg, $matched, $searchTerm);
        $totalCount = $allForms->count();
        $lastPage = max(1, (int) ceil($totalCount / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        return [
            'forms'        => $allForms->slice($offset, $perPage)->values(),
            'total_count'  => $totalCount,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Dari koleksi matched records, bangun daftar forms yang sudah
     * di-group dan di-sort. Dipakai oleh traceByBatch & paginateModuleForms.
     */
    protected function buildFormsList(array $cfg, Collection $matched, string $searchTerm): Collection
    {
        $routeKey = $cfg['route_key_column'] ?? 'uuid';
        $isSelfForm = ($cfg['form_relation'] ?? null) === 'self';

        return $matched
            ->groupBy(function ($d) use ($cfg, $routeKey, $isSelfForm) {
                if ($isSelfForm) {
                    return $d->{$routeKey} ?? $d->id;
                }
                $formObj = $d->{$cfg['form_relation']} ?? null;
                return $formObj ? ($formObj->{$routeKey} ?? $formObj->id) : null;
            })
            ->filter(fn($group, $key) => !empty($key))
            ->map(function ($group) use ($cfg, $routeKey, $isSelfForm, $searchTerm) {
                $firstItem = $group->first();
                $form = $isSelfForm ? $firstItem : ($firstItem->{$cfg['form_relation']} ?? null);

                if (!$form) {
                    return null;
                }

                $formKeyValue = $form->{$routeKey} ?? $form->id;
                
                $dateValue = $form->tanggal ?? $form->date ?? $firstItem->tanggal ?? $firstItem->created_at ?? null;
                $shiftValue = $form->datashift->shift ?? $form->shift->shift ?? $form->shift ?? $firstItem->datashift->shift ?? null;
                $shiftId = $form->shift_id ?? $firstItem->shift_id ?? null;

                // Generasi URL PDF jika route terdaftar
                $pdfUrl = '#';
                $pdfRouteName = $cfg['pdf_route'] ?? null;
                if (!empty($pdfRouteName) && \Route::has($pdfRouteName)) {
                    try {
                        $pdfUrl = route($pdfRouteName, $formKeyValue);
                    } catch (\Exception $e) {
                        $pdfUrl = route($pdfRouteName);
                    }
                }

                // Generasi URL Halaman Modul
                $moduleUrl = '#';
                $indexRouteName = $cfg['index_route'] ?? null;
                if (!empty($indexRouteName) && \Route::has($indexRouteName)) {
                    try {
                        $dateStr = $dateValue ? \Carbon\Carbon::parse($dateValue)->format('Y-m-d') : null;
                        $moduleUrl = route($indexRouteName, array_filter([
                            'search' => $searchTerm ?? null,
                            'tanggal' => $dateStr,
                        ]));
                    } catch (\Exception $e) {
                        try {
                            $moduleUrl = route($indexRouteName);
                        } catch (\Exception $ex) {
                            $moduleUrl = '#';
                        }
                    }
                }

                // Extract display_fields
                $displayFields = [];
                if (isset($cfg['display_fields']) && is_callable($cfg['display_fields'])) {
                    try {
                        $displayFields = ($cfg['display_fields'])($firstItem);
                    } catch (\Throwable $e) {
                        $displayFields = [];
                    }
                }

                return [
                    'form_key'       => $formKeyValue,
                    'date'           => $dateValue ? \Carbon\Carbon::parse($dateValue)->format('Y-m-d') : null,
                    'shift'          => $shiftValue,
                    'shift_id'       => $shiftId,
                    'pdf_url'        => $pdfUrl,
                    'pdf_route'      => $pdfRouteName,
                    'module_url'     => $moduleUrl,
                    'match_count'    => $group->count(),
                    'display_fields' => $displayFields,
                ];
            })
            ->filter()
            ->sortByDesc('date')
            ->values();
    }

    /**
     * Ambil detail lengkap untuk 1 form secara lazy-load.
     */
    public function getFormDetails(string $moduleKey, string $formKey, string $searchTerm): array
    {
        $cfg = config("traceability.modules.{$moduleKey}");
        if (!$cfg || !class_exists($cfg['model'])) {
            return ['details' => []];
        }

        $tokens = $this->tokenize($searchTerm);
        $user = Auth::user();
        $userPlanId = $user ? (method_exists($user, 'getEffectivePlanId') ? $user->getEffectivePlanId() : ($user->id_plan ?? null)) : null;
        $isSuperAdmin = $user && ($user->id_role == 5 || $user->role === 'superadmin');
        $routeKey = $cfg['route_key_column'] ?? 'uuid';
        $isSelfForm = ($cfg['form_relation'] ?? null) === 'self';

        $matched = $this->matchDetails($cfg, $tokens, $userPlanId, $isSuperAdmin)
            ->filter(function ($d) use ($cfg, $routeKey, $formKey, $isSelfForm) {
                if ($isSelfForm) {
                    return (string) ($d->{$routeKey} ?? $d->id) === (string) $formKey;
                }
                $form = $d->{$cfg['form_relation']} ?? null;
                return $form && (string) ($form->{$routeKey} ?? $form->id) === (string) $formKey;
            });

        if ($matched->isEmpty()) {
            return ['details' => []];
        }

        $keyName = (new $cfg['model'])->getKeyName();
        $ids = $matched->pluck($keyName);

        $withRelations = array_merge(
            collect($cfg['detail_with'] ?? [])->values()->all(),
            collect($cfg['related'] ?? [])->map(fn($r) => $r['relation'])->values()->all()
        );

        $details = $cfg['model']::with($withRelations)
            ->whereIn($keyName, $ids)
            ->get()
            ->map(function ($detail) use ($cfg) {
                $related = collect($cfg['related'] ?? [])
                    ->map(function ($rcfg) use ($detail) {
                        $rel = $detail->{$rcfg['relation']} ?? null;
                        if (!$rel) return null;
                        return [
                            'label'  => $rcfg['label'],
                            'fields' => is_callable($rcfg['display_fields'] ?? null) ? $rcfg['display_fields']($rel) : []
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'fields'  => is_callable($cfg['display_fields'] ?? null) ? $cfg['display_fields']($detail) : [],
                    'related' => $related
                ];
            })
            ->values();

        return ['details' => $details];
    }

    /**
     * Logika pencocokan query dasar dengan filter Plant (id_plan) & Multi-Token.
     */
    protected function matchDetails(array $cfg, Collection $tokens, ?int $userPlanId, bool $isSuperAdmin): Collection
    {
        $ownColumns = $cfg['search_columns'] ?? array_filter([$cfg['column'] ?? null]);
        $query = $cfg['model']::query();

        // 🔒 PEMBATASAN PLAN (id_plan)
        if (!$isSuperAdmin && $userPlanId) {
            $planCol = $cfg['plan_column'] ?? 'id_plan';
            $isSelfForm = ($cfg['form_relation'] ?? null) === 'self';

            if ($isSelfForm) {
                if (\Schema::hasColumn((new $cfg['model'])->getTable(), $planCol)) {
                    $query->where($planCol, $userPlanId);
                }
            } else {
                $formRelation = $cfg['form_relation'] ?? null;
                if ($formRelation) {
                    $query->whereHas($formRelation, function ($q) use ($planCol, $userPlanId) {
                        $q->where($planCol, $userPlanId);
                    });
                }
            }
        }

        // Searching multi-token
        $table = (new $cfg['model'])->getTable();
        foreach ($tokens as $token) {
            $query->where(function ($q) use ($token, $cfg, $ownColumns, $table) {
                $hasQuery = false;
                foreach ($ownColumns as $col) {
                    if (\Schema::hasColumn($table, $col)) {
                        $q->orWhere($col, 'LIKE', "%{$token}%");
                        $hasQuery = true;
                    }
                }
                foreach ($cfg['search_relations'] ?? [] as $relation => $col) {
                    if (method_exists((new $cfg['model']), $relation)) {
                        $q->orWhereHas($relation, fn($rq) => $rq->where($col, 'LIKE', "%{$token}%"));
                        $hasQuery = true;
                    }
                }
                if (!$hasQuery) {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        $withRelations = [];
        if (($cfg['form_relation'] ?? 'self') !== 'self') {
            $withRelations[] = $cfg['form_relation'];
        }

        $searchRelKeys = collect($cfg['search_relations'] ?? [])
            ->keys()
            ->filter(fn($r) => method_exists((new $cfg['model']), $r))
            ->values()
            ->all();

        $withRelations = array_merge($withRelations, $searchRelKeys);

        return $query->with($withRelations)
            ->get()
            ->filter(function ($detail) use ($cfg, $tokens, $ownColumns) {
                $ownValues = collect($ownColumns)->map(fn($col) => $detail->{$col} ?? null);
                
                $relValues = collect($cfg['search_relations'] ?? [])
                    ->map(function ($col, $relation) use ($detail) {
                        if (!method_exists($detail, $relation)) return null;
                        $relObj = $detail->{$relation} ?? null;
                        return $relObj ? ($relObj->{$col} ?? null) : null;
                    });

                $haystack = strtolower(
                    $ownValues->merge($relValues)
                        ->filter()
                        ->implode(' ')
                );

                return $tokens->every(fn($t) => str_contains($haystack, strtolower($t)));
            })
            ->values();
    }

    /**
     * Memecah kata kunci menjadi token terpisah.
     */
    protected function tokenize(string $term): Collection
    {
        return collect(preg_split('/\s+/', trim($term)))->filter()->values();
    }
}
