<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterStandarAudit;
use App\Models\InputArea;
use Illuminate\Support\Facades\Auth;

class MasterStandarAuditController extends Controller
{
    /**
     * Tampilkan daftar aturan Master Standar Audit
     */
    public function index(Request $request)
    {
        $selectedModule = $request->get('module');
        $perPage        = $request->get('per_page', 5);
        $modules        = $this->getQcModules();
        $user           = Auth::user();

        $standars = MasterStandarAudit::with('area')
            ->when($selectedModule, function ($q) use ($selectedModule) {
                $q->where('module_name', $selectedModule);
            })
            ->when($user->id_plan, function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            })
            ->orderBy('module_name', 'asc')
            ->paginate($perPage)
            ->appends($request->all());

        // Ambil area sesuai plan user yang login
        $areas = InputArea::when($user->id_plan, function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            })
            ->orderBy('area', 'asc')
            ->get();

        return view('master-standar-audit.index', compact('modules', 'standars', 'areas', 'selectedModule', 'perPage'));
    }

    /**
     * Simpan / Update aturan Master Standar Audit (Single / Multi Batch Smart Input)
     */
    public function store(Request $request)
    {
        // 1. Jika dikirim via Batch Multi-Item (Items Array)
        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'module_name'            => 'required|string',
                'items'                  => 'required|array|min:1',
                'items.*.column_name'    => 'required|string',
                'items.*.parameter_type' => 'required|in:suhu,suhu_ruang,suhu_kondisi,suhu_pusat,suhu_pemasakan,status_checklist',
            ]);

            $moduleName = $request->module_name;
            $savedCount = 0;

            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $moduleName, &$savedCount) {
                // Simpan items standar
                foreach ($request->items as $item) {
                    if (!isset($item['enabled']) || !$item['enabled']) {
                        continue;
                    }

                    $tableName = !empty($item['table_name']) ? $item['table_name'] : $moduleName;

                    MasterStandarAudit::updateOrCreate(
                        [
                            'module_name' => $moduleName,
                            'id_plan'     => Auth::user()->id_plan,
                            'area_id'     => null,
                            'column_name' => $item['column_name'],
                        ],
                        [
                            'table_name'      => $tableName,
                            'parameter_type'  => $item['parameter_type'],
                            'min_value'       => isset($item['min_value']) && $item['min_value'] !== '' ? $item['min_value'] : null,
                            'max_value'       => isset($item['max_value']) && $item['max_value'] !== '' ? $item['max_value'] : null,
                            'standar_value'   => isset($item['standar_value']) && $item['standar_value'] !== '' ? $item['standar_value'] : null,
                            'auto_fix_status' => !empty($item['auto_fix_status']),
                        ]
                    );
                    $savedCount++;
                }

                // Jika ada matriks area_items (seperti pada modul area_proses)
                if ($request->has('area_items') && is_array($request->area_items)) {
                    foreach ($request->area_items as $areaId => $areaData) {
                        if (empty($areaData['enabled'])) {
                            continue;
                        }

                        MasterStandarAudit::updateOrCreate(
                            [
                                'module_name' => $moduleName,
                                'id_plan'     => Auth::user()->id_plan,
                                'area_id'     => $areaId,
                                'column_name' => 'pemeriksaan_suhu_ruang',
                            ],
                            [
                                'table_name'      => 'area_proses',
                                'parameter_type'  => 'suhu_ruang',
                                'min_value'       => isset($areaData['min_value']) && $areaData['min_value'] !== '' ? $areaData['min_value'] : null,
                                'max_value'       => isset($areaData['max_value']) && $areaData['max_value'] !== '' ? $areaData['max_value'] : null,
                                'auto_fix_status' => !empty($areaData['auto_fix_status']),
                            ]
                        );
                        $savedCount++;
                    }
                }
            });

            return redirect()->back()->with('success', "Berhasil menyimpan {$savedCount} aturan audit sekaligus!");
        }

        // 2. Jika dikirim via Multi-Select Global Form
        $request->validate([
            'module_name'    => 'required|string',
            'area_id'        => 'nullable|exists:input_area,id',
            'column_name'    => 'required',
            'parameter_type' => 'required|in:suhu,suhu_ruang,suhu_kondisi,suhu_pusat,suhu_pemasakan,status_checklist',
            'min_value'      => 'nullable|numeric',
            'max_value'      => 'nullable|numeric',
            'standar_value'  => 'nullable|numeric',
        ]);

        $moduleName  = $request->module_name;
        $areaId      = $request->area_id ?: null;
        $columns     = is_array($request->column_name) ? $request->column_name : [$request->column_name];
        $columnTables= $request->input('column_tables', []);

        $savedCount = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $moduleName, $areaId, $columns, $columnTables, &$savedCount) {
            foreach ($columns as $col) {
                if (empty($col)) continue;

                $tableName = isset($columnTables[$col]) ? $columnTables[$col] : ($request->table_name ?: $moduleName);

                MasterStandarAudit::updateOrCreate(
                    [
                        'module_name' => $moduleName,
                        'id_plan'     => Auth::user()->id_plan,
                        'area_id'     => $areaId,
                        'column_name' => $col,
                    ],
                    [
                        'table_name'      => $tableName,
                        'parameter_type'  => $request->parameter_type,
                        'min_value'       => $request->min_value,
                        'max_value'       => $request->max_value,
                        'standar_value'   => $request->standar_value,
                        'auto_fix_status' => $request->has('auto_fix_status') ? true : false,
                    ]
                );
                $savedCount++;
            }
        });

        $msg = $savedCount > 1 
            ? "Berhasil menyimpan aturan untuk {$savedCount} kolom sekaligus!" 
            : 'Aturan Master Standar Audit berhasil disimpan!';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Update aturan Master Standar Audit tunggal (via modal edit)
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $item = MasterStandarAudit::where('uuid', $uuid)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->firstOrFail();

        $request->validate([
            'area_id'        => 'nullable|exists:input_area,id',
            'parameter_type' => 'required|in:suhu,suhu_ruang,suhu_kondisi,suhu_pusat,suhu_pemasakan,status_checklist',
            'min_value'      => 'nullable|numeric',
            'max_value'      => 'nullable|numeric',
        ]);

        $item->update([
            'area_id'         => $request->area_id ?: null,
            'parameter_type'  => $request->parameter_type,
            'min_value'       => $request->parameter_type !== 'status_checklist' && $request->min_value !== '' && $request->min_value !== null ? $request->min_value : null,
            'max_value'       => $request->parameter_type !== 'status_checklist' && $request->max_value !== '' && $request->max_value !== null ? $request->max_value : null,
            'auto_fix_status' => $request->has('auto_fix_status') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Aturan Master Standar Audit berhasil diperbarui!');
    }

    /**
     * Hapus aturan Master Standar Audit
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $item = MasterStandarAudit::where('uuid', $uuid)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->firstOrFail();
        $item->delete();

        return redirect()->back()->with('success', 'Aturan Master Standar Audit berhasil dihapus!');
    }

    /**
     * Ambil daftar modul QC yang valid sesuai menu Navbar QC System
     */
    /**
     * Ambil pemetaan modul resmi dari config master_audit
     */
    protected function getQcModules()
    {
        $configModules = config('master_audit.modules', []);
        $modules = [];

        foreach ($configModules as $id => $item) {
            $modules[] = [
                'id'    => $id,
                'label' => $item['label'] ?? ucwords(str_replace('_', ' ', $id)),
            ];
        }

        // Urutkan alfabetis berdasarkan label
        usort($modules, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $modules;
    }

    /**
     * Ambil daftar nama kolom auditable berdasarkan modul yang dipilih (AJAX)
     */
    public function getColumns(Request $request)
    {
        $moduleKey = $request->get('module');
        if (!$moduleKey) {
            return response()->json([]);
        }

        $configModules = config('master_audit.modules', []);
        $moduleConfig  = $configModules[$moduleKey] ?? null;

        $columnsList = [];

        if ($moduleConfig) {
            // 1. Ambil kolom dari main_table
            if (!empty($moduleConfig['fields'])) {
                foreach ($moduleConfig['fields'] as $colName => $meta) {
                    $columnsList[] = [
                        'name'       => $colName,
                        'table_name' => $moduleConfig['main_table'] ?? $moduleKey,
                        'label'      => $colName . ' (' . ($meta['label'] ?? $colName) . ')',
                        'type'       => $meta['type'] ?? 'suhu',
                    ];
                }
            }

            // 2. Ambil kolom dari tabel relasi (jika ada)
            if (!empty($moduleConfig['relations'])) {
                foreach ($moduleConfig['relations'] as $relKey => $relData) {
                    $relTable = $relData['table'] ?? $relKey;
                    if (!empty($relData['fields'])) {
                        foreach ($relData['fields'] as $colName => $meta) {
                            $columnsList[] = [
                                'name'       => $colName,
                                'table_name' => $relTable,
                                'label'      => $relTable . '.' . $colName . ' (' . ($meta['label'] ?? $colName) . ')',
                                'type'       => $meta['type'] ?? 'suhu',
                            ];
                        }
                    }
                }
            }
        }

        // Jika tidak ada di config, jalankan fallback baca schema database biasa
        if (empty($columnsList)) {
            $ignoredColumns = [
                'id', 'uuid', 'id_plan', 'id_user', 'user_id', 'created_at', 'updated_at', 
                'deleted_at', 'is_audit', 'is_draft', 'status_appr', 'appr_by', 'appr_at',
                'ttd_qc', 'ttd_spv', 'shift', 'tanggal', 'jam', 'source_uuid', 'waktu'
            ];
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable($moduleKey)) {
                    $dbCols = \Illuminate\Support\Facades\Schema::getColumnListing($moduleKey);
                    $filtered = array_values(array_diff($dbCols, $ignoredColumns));
                    foreach ($filtered as $c) {
                        $columnsList[] = [
                            'name'       => $c,
                            'table_name' => $moduleKey,
                            'label'      => $c,
                            'type'       => 'suhu',
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return response()->json($columnsList);
    }
}
