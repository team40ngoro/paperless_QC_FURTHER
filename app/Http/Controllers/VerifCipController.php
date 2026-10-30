<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\Plan;
use App\Models\VerifCip;
use App\Services\BulkExport\VerifCipBulkExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class VerifCipController extends Controller
{
    use \App\Http\Controllers\Traits\HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return VerifCip::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'verif-cip';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.verif_cip';
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'tanggal';
    }

    protected function auditSearchColumns(): array
    {
        return [];
    }
    private function normalizeTanggal(string $rawTanggal, bool $isSpecialRole): string
    {
        if ($isSpecialRole) {
            $dateOnly = Carbon::parse($rawTanggal)->format('Y-m-d');
            $timeNow = now()->format('H:i:s');
            return $dateOnly . ' ' . $timeNow;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawTanggal)) {
            $timeNow = now()->format('H:i:s');
            return $rawTanggal . ' ' . $timeNow;
        }

        return Carbon::parse($rawTanggal)->format('Y-m-d H:i:s');
    }

    protected function getDistinctMouldrumList($idPlan = null): array
    {
        $query = VerifCip::query();
        if ($idPlan) {
            $query->where('id_plan', $idPlan);
        }
        $payloads = $query->pluck('payload');
        $types = [];
        foreach ($payloads as $p) {
            $data = is_array($p) ? $p : json_decode($p, true);
            foreach ($data['forms'] ?? [] as $f) {
                foreach ($f['details'] ?? [] as $d) {
                    $val = $d['jenis_mouldrum'] ?? $d['jenis_moldrum'] ?? null;
                    if (!empty($val) && is_string($val)) {
                        $types[] = trim($val);
                    }
                }
            }
        }
        $types = array_values(array_filter(array_unique($types)));
        sort($types, SORT_NATURAL | SORT_FLAG_CASE);
        return $types;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('verif-cip.audit');
        }

        $search  = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? $request->input('perPage') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        $query = VerifCip::with(['plan', 'user'])->operasional();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'LIKE', "%{$search}%")
                ->orWhere('uuid', 'LIKE', "%{$search}%")
                ->orWhere('payload', 'LIKE', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $this->applyDateRangeFilter($request, $query);

        $items = $query->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $mouldrumList = $this->getDistinctMouldrumList($user->role !== 'superadmin' ? $user->id_plan : null);

        return view('qc-sistem.verif_cip.index', compact('items', 'search', 'perPage', 'mouldrumList'));
    }

    public function auditIndex(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? $request->input('perPage') ?? 5);
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }

        $auditedSourceUuids = VerifCip::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

        $query = VerifCip::with(['plan', 'user'])
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $this->applyDateRangeFilter($request, $query);

        $items = $query->get();

        $rows = [];
        foreach ($items as $item) {
            $forms = $item->payload['forms'] ?? [];
            if (!is_array($forms) || count($forms) === 0) {
                $forms = [
                    [
                        'tanggal' => $item->tanggal,
                        'details' => [],
                    ],
                ];
            }

            foreach ($forms as $formIndex => $form) {
                $formTanggal = data_get($form, 'tanggal') ?: $item->tanggal;
                $details = data_get($form, 'details', []);
                if (!is_array($details) || count($details) === 0) {
                    $details = [null];
                }

                foreach ($details as $detailIndex => $detail) {
                    $rows[] = [
                        'item' => $item,
                        'formIndex' => $formIndex,
                        'detailIndex' => is_int($detailIndex) ? $detailIndex : 0,
                        'formTanggal' => $formTanggal,
                        'detail' => $detail,
                    ];
                }
            }
        }

        if ($search !== '') {
            $needle = $search;
            $needleUpper = strtoupper(trim($needle));
            $isSingleLetter = preg_match('/^[a-zA-Z]$/', trim($needle)) === 1;

            $rows = array_values(array_filter($rows, function ($row) use ($needle, $isSingleLetter, $needleUpper) {
                $item = $row['item'] ?? null;
                if (!$item) return false;

                $uuid = (string) ($item->uuid ?? '');
                $tanggal = (string) ($item->tanggal ?? '');
                $userName = (string) (optional($item->user)->name ?? '');
                $planNama = (string) (optional($item->plan)->nama_plan ?? '');

                $detail = $row['detail'] ?? [];
                $detailJenisMoldrum = (string) (data_get($detail, 'jenis_moldrum') ?? '');
                $detailJenisMouldrum = (string) (data_get($detail, 'jenis_mouldrum') ?? '');

                if ($isSingleLetter) {
                    return strtoupper(trim($detailJenisMoldrum)) === $needleUpper
                        || strtoupper(trim($detailJenisMouldrum)) === $needleUpper;
                }

                return stripos($uuid, $needle) !== false
                    || stripos($tanggal, $needle) !== false
                    || stripos($userName, $needle) !== false
                    || stripos($planNama, $needle) !== false
                    || stripos($detailJenisMoldrum, $needle) !== false
                    || stripos($detailJenisMouldrum, $needle) !== false;
            }));
        }

        // Satu baris per record (model unik) dari hasil pencarian
        $uniqueItems = collect($rows)
            ->pluck('item')
            ->filter()
            ->unique('uuid')
            ->values();

        $page = (int) $request->input('page', 1);
        $page = $page > 0 ? $page : 1;

        $total = $uniqueItems->count();
        $results = $uniqueItems->slice(($page - 1) * $perPage, $perPage)->values();

        $rowsPaginator = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $mouldrumList = $this->getDistinctMouldrumList($user->role !== 'superadmin' ? $user->id_plan : null);

        return view('qc-sistem.verif_cip.index_audit', ['items' => $rowsPaginator, 'search' => $search, 'perPage' => $perPage, 'mouldrumList' => $mouldrumList]);
    }


    public function create(Request $request)
    {
        $user = Auth::user();

        $plans = null;
        $selectedPlanId = $user->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            $selectedPlanId = $request->get('plan_id') ?: old('id_plan') ?: ($plans->first()->id ?? null);
        }

        if (!$selectedPlanId) {
            abort(404);
        }

        return view('qc-sistem.verif_cip.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ((int) ($user->id_role ?? 0) === 2 || (int) ($user->id_role ?? 0) === 3);

        $rules = [
            'payload' => 'required|string',
            'tanggal' => 'required|date',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $data['tanggal'] = $this->normalizeTanggal($data['tanggal'], $isSpecialRole);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        $payload = json_decode($data['payload'], true);
        if (!is_array($payload)) {
            return back()->withErrors(['payload' => 'Payload tidak valid.'])->withInput();
        }

        $item = VerifCip::create([
            'id_plan' => $planId,
            'user_id' => $user->id,
            'tanggal' => $data['tanggal'],
            'payload' => $payload,
        ]);

        return redirect()->route('verif-cip.show', $item->uuid)
            ->with('success', 'Data Verif CIP berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::with(['plan', 'user'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('verif-cip.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.verif_cip.show', compact('item'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::with(['plan', 'user'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $isAuditMode = (bool) $item->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'verif-cip.edit',
                array_merge(['uuid' => $item->uuid], $queryParameters)
            );
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;
        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
        }

        return view('qc-sistem.verif_cip.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $isSpecialRole = ((int) ($user->id_role ?? 0) === 2 || (int) ($user->id_role ?? 0) === 3);

        $item = VerifCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $data = $request->validate([
            'payload' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $data['tanggal'] = $this->normalizeTanggal($data['tanggal'], $isSpecialRole);

        $payload = json_decode($data['payload'], true);
        if (!is_array($payload)) {
            return back()->withErrors(['payload' => 'Payload tidak valid.'])->withInput();
        }

        $item->update([
            'tanggal' => $data['tanggal'],
            'payload' => $payload,
        ]);

        return redirect()->route($item->is_audit ? 'verif-cip.audit' : 'verif-cip.index')
            ->with('success', 'Data Verif CIP berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('verif-cip.index')
            ->with('success', 'Data Verif CIP berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = VerifCip::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            $userRole = $user->id_role ?? null;

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk data ini',
                ], 403);
            }

            $type = $request->type;
            switch ($type) {
                case 'qc':
                    if (!in_array($userRole, [1, 3, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC',
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if ($userRole != 2) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi',
                        ], 403);
                    }
                    if (!$item->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                        ], 400);
                    }
                    break;
                case 'spv':
                    if ($userRole != 4) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV',
                        ], 403);
                    }
                    if (!$item->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh Produksi terlebih dahulu',
                        ], 400);
                    }
                    break;
            }

            $approvalField = "approved_by_{$type}";
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya',
                ], 400);
            }

            $updateData = [
                $approvalField => true,
                "approved_by_{$type}_at" => now(),
                "approved_by_{$type}_user_id" => $user->id,
            ];

            $item->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui',
                'data' => $item->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = Auth::user();

            $query = VerifCip::with(['plan', 'user']);
            
            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }
                
            $query->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query->whereDate('tanggal', $request->tanggal_awal);
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];

                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                } elseif ($request->tanggal_awal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
                }

                $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Data Tidak Ditemukan</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .icon { font-size: 64px; color: #ffc107; margin-bottom: 20px; }
                        h1 { color: #495057; margin-bottom: 20px; }
                        .message { color: #6c757d; margin-bottom: 30px; font-size: 16px; }
                        .filter-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
                        .filter-info h4 { margin: 0 0 10px 0; color: #495057; }
                        .filter-info ul { margin: 0; padding-left: 20px; }
                        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
                        .btn:hover { background: #0056b3; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="icon">⚠️</div>
                        <h1>Data Tidak Ditemukan</h1>
                        <p class="message">' . $errorMessage . '</p>';

                if (!empty($filterInfo)) {
                    $html .= '
                        <div class="filter-info">
                            <h4>Filter yang digunakan:</h4>
                            <ul>';
                    foreach ($filterInfo as $info) {
                        $html .= '<li>' . $info . '</li>';
                    }
                    $html .= '
                            </ul>
                        </div>';
                }

                $html .= '
                        <p style="color: #6c757d; font-size: 14px;">
                            Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.
                        </p>
                        <a href="javascript:window.close()" class="btn">Tutup</a>
                    </div>
                </body>
                </html>';

                return response($html)->header('Content-Type', 'text/html');
            }

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.verif_cip.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A3', 'landscape');

            $filename = 'verif_cip_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdf($uuid)
    {
        try {
            $user = Auth::user();

            $item = VerifCip::with(['plan', 'user'])->where('uuid', $uuid)->firstOrFail();

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini');
            }

            $data = collect([$item]);
            $kode_form = null; // Single export typically doesn't need to pass a specific kode_form from request

            $pdf = \PDF::loadView('qc-sistem.verif_cip.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A3', 'landscape');

            $filename = 'verif_cip_' . ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'data') . '_' . substr($uuid, 0, 8) . '.pdf';
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = Auth::user();
            $userRole = $user->id_role ?? null;
            $type = $request->type;
            $tanggal = $request->tanggal;

            // Role Authorization Check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai ' . strtoupper($type),
                ], 403);
            }

            $query = VerifCip::whereDate('tanggal', $tanggal)->operasional();
            
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui.']);
            }

            foreach ($items as $item) {
                // Sequential validation
                if ($type === 'produksi' && !$item->approved_by_qc) continue;
                if ($type === 'spv' && !$item->approved_by_produksi) continue;

                $approvalField = "approved_by_{$type}";
                if (!$item->$approvalField) {
                    $item->update([
                        $approvalField => true,
                        "approved_by_{$type}_user_id" => $user->id,
                        "approved_by_{$type}_at" => now()
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui data secara massal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid'
            ]);

            $user = Auth::user();
            $type = $request->input('type');
            $uuids = $request->input('uuids', []);

            // Role-based permission check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
                ], 403);
            }

            // Find records - ONLY unapproved ones (smart auto-skip)
            $query = VerifCip::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update operasional records
            $updateData = [
                "approved_by_{$type}" => true,
                "approved_by_{$type}_user_id" => $user->id,
                "approved_by_{$type}_at" => now(),
            ];

            VerifCip::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            VerifCip::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Berhasil verifikasi {$count} data " . strtoupper($type)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkExportExcel(Request $request, VerifCipBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }
}