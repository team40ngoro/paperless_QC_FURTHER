<?php

namespace App\Http\Controllers;

use App\Models\LabelStartUp;
use App\Models\LabelStartUpDetail;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BulkExport\LabelStartUpBulkExportService;
use App\Http\Controllers\Traits\HasExcelImport;

class LabelStartUpController extends Controller
{
    use HasExcelImport;


    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('label-pemeriksaan.audit');
        }
        $search = trim((string) $request->get('search', ''));

        $query = LabelStartUp::with(['plan', 'shift', 'user', 'details.produk'])
            ->where('is_audit', false);

        if ($user->role_id != 1) {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->whereHas('details.produk', function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Pagination on records (not groups)
        $this->applyDateRangeFilter(request(), $query);
        $data = $query->orderBy('tanggal', 'desc')
            ->orderBy('id_shift', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Load shifts and produks untuk filter modal
        $shifts = ($user->role_id == 1) ? DataShift::all() : DataShift::where('id_plan', $user->id_plan)->get();
        $produks = ($user->role_id == 1) ? JenisProduk::all() : JenisProduk::where('id_plan', $user->id_plan)->get();

        return view('qc-sistem.label_pemeriksaan.index', compact('data', 'search', 'perPage', 'shifts', 'produks'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Filter shift dan produk berdasarkan plant user login
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        
        // Jika super-admin login (atau butuh akses semua plant)
        if ($user->role_id == 1) {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        }

        return view('qc-sistem.label_pemeriksaan.create', compact('shifts', 'produks', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_shift' => 'required',
            'id_produk' => 'required|array',
            'id_produk.*' => 'required',
            'label_produk' => 'required|array',
            'label_produk.*' => 'required|in:PL,MC',
            'jam' => 'required|array',
            'foto_kode_produksi' => 'nullable|array',
            'foto_kode_produksi.*' => 'nullable',
            'hasil_pengecekan' => 'required|array',
            'nama_operator' => 'required|array',
            'ttd_operator' => 'required|array',
            'nama_qc' => 'required|array',
            'ttd_qc' => 'required|array',
        ]);

        $user = Auth::user();

        // 1. Simpan Header
        $header = LabelStartUp::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $user->id_plan,
            'id_shift' => $request->id_shift,
            'user_id' => $user->id,
            'tanggal' => $request->tanggal,
        ]);

        // 2. Simpan Detail (Looping)
        foreach ($request->id_produk as $index => $id_produk) {
            $fotoPath = $this->handleUploadFoto($request->foto_kode_produksi[$index]);

            LabelStartUpDetail::create([
                'label_start_up_id' => $header->id,
                'id_produk' => $id_produk,
                'berat' => $request->berat[$index] ?? null,
                'label_produk' => $request->label_produk[$index],
                'jam' => $request->jam[$index],
                'foto_kode_produksi' => $fotoPath,
                'hasil_pengecekan' => $request->hasil_pengecekan[$index],
                'nama_operator' => $request->nama_operator[$index],
                'ttd_operator' => $request->ttd_operator[$index], // Langsung Base64 dari Signature Pad
                'nama_qc' => $request->nama_qc[$index],
                'ttd_qc' => $request->ttd_qc[$index], // Langsung Base64 dari Signature Pad
                'tindakan_koreksi' => $request->tindakan_koreksi[$index] ?? null,
            ]);
        }

        return redirect()->route('label-pemeriksaan.index')->with('success', 'Data Pemeriksaan Label berhasil disimpan.');
    }

    public function show($uuid)
    {
        $item = LabelStartUp::with(['details.produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('label-pemeriksaan.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.label_pemeriksaan.show', compact('item'));
    }

    public function edit($uuid)
    {
        $item = LabelStartUp::where('uuid', $uuid)->firstOrFail();
        
        $user = auth()->user();
        $isAuditMode = (bool) $item->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'label-pemeriksaan.edit',
                array_merge(['uuid' => $item->uuid], $queryParameters)
            );
        }

        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('label-pemeriksaan.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $item->load('details');
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        
        if ($user->role_id == 1) {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        }

        return view('qc-sistem.label_pemeriksaan.edit', compact('item', 'shifts', 'produks', 'user'));
    }

    public function update(Request $request, $id)
    {
        $header = LabelStartUp::findOrFail($id);

        $user = auth()->user();
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($header->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('label-pemeriksaan.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'id_shift' => 'required',
            'id_produk' => 'required|array',
            'id_produk.*' => 'required',
            'label_produk' => 'required|array',
            'label_produk.*' => 'required|in:PL,MC',
            'foto_kode_produksi' => 'nullable|array',
            'foto_kode_produksi.*' => 'nullable',
        ]);

        $header->update([
            'id_shift' => $request->id_shift,
            'tanggal' => $request->tanggal,
        ]);

        // Hapus detail lama untuk diganti yang baru (Simple Sync)
        // Catatan: Di sistem nyata kita sebaiknya menghapus file lama jika diganti, 
        // tapi untuk kemudahan edit dinamis kita timpa datanya.
        $header->details()->delete();

        foreach ($request->id_produk as $index => $id_produk) {
            $fotoPath = $request->old_foto[$index]; // Gunakan foto lama sebagai default
            
            // Jika ada foto baru yang diupload/diambil
            if (isset($request->foto_kode_produksi[$index])) {
                $fotoPath = $this->handleUploadFoto($request->foto_kode_produksi[$index]);
            }

            LabelStartUpDetail::create([
                'label_start_up_id' => $header->id,
                'id_produk' => $id_produk,
                'berat' => $request->berat[$index] ?? null,
                'label_produk' => $request->label_produk[$index] ?? null,
                'jam' => $request->jam[$index],
                'foto_kode_produksi' => $fotoPath,
                'hasil_pengecekan' => $request->hasil_pengecekan[$index],
                'nama_operator' => $request->nama_operator[$index],
                'ttd_operator' => $request->ttd_operator[$index],
                'nama_qc' => $request->nama_qc[$index],
                'ttd_qc' => $request->ttd_qc[$index],
                'tindakan_koreksi' => $request->tindakan_koreksi[$index] ?? null,
            ]);
        }

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $header->is_audit ? 'label-pemeriksaan.audit' : 'label-pemeriksaan.index';

        return redirect()->route($redirectRoute)->with('success', 'Data Pemeriksaan Label berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = LabelStartUp::findOrFail($id);
        $item->delete();
        return redirect()->route('label-pemeriksaan.index')->with('success', 'Data berhasil dihapus.');
    }

    private function handleUploadFoto($file)
    {
        if (!$file) return null;

        // Jika filenya adalah Base64 (setelah dikompres di client)
        if (strpos($file, 'data:image') === 0) {
            $image_64 = $file;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            
            Storage::disk('public')->put('uploads/label_start_up/' . $imageName, base64_decode($image));
            return 'uploads/label_start_up/' . $imageName;
        }

        // Jika filenya adalah Upload standar (jarang terjadi karena ada kompresi client)
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/label_start_up', $filename, 'public');
        return $path;
    }

    public function pdf($uuid)
    {
        try {
            $item = LabelStartUp::with(['details.produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
            
            $pdf = Pdf::loadView('qc-sistem.label_pemeriksaan.export_pdf', compact('item'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('label_pemeriksaan_' . $item->tanggal . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cetak PDF: ' . $e->getMessage());
        }
    }
    public function approve(Request $request, $uuid)
    {
        try {
            $item = LabelStartUp::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            $type = $request->input('type');

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [2],
                'spv' => [4]
            ];

            if (!isset($allowedRoles[$type]) || !in_array($user->id_role, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin.'], 403);
            }

            // Validasi sequential approval
            if ($type === 'produksi' && !$item->approved_by_qc) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh QC terlebih dahulu.'], 400);
            }
            if ($type === 'spv' && !$item->approved_by_produksi) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'], 400);
            }

            $approvalField = "approved_by_{$type}";
            if ($item->$approvalField) {
                return response()->json(['success' => false, 'message' => 'Data sudah disetujui sebelumnya.'], 400);
            }

            $item->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Data berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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
            $query = LabelStartUp::whereIn('uuid', $uuids)
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
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now(),
            ];

            LabelStartUp::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            LabelStartUp::where('is_audit', true)
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

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'produk_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50',
                'is_audit' => 'nullable|boolean'
            ]);

            $user = auth()->user();
            $isAudit = $request->boolean('is_audit');
            $produkId = $request->input('produk_id');

            $query = LabelStartUp::with(['details' => function($query) use ($produkId) {
                    if ($produkId) {
                        $query->where('id_produk', $produkId);
                    }
                    $query->with('produk');
                }, 'plan', 'shift', 'user'])
                ->where('is_audit', $isAudit)
                ->when($user->role_id != 1, function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
            }
            if ($request->filled('shift_id')) {
                $query->where('id_shift', $request->shift_id);
            }
            if ($produkId) {
                $query->whereHas('details', function($q) use ($produkId) {
                    $q->where('id_produk', $produkId);
                });
            }

            $data = $query->orderBy('tanggal', 'asc')->orderBy('id_shift', 'asc')->get();

            if ($data->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk diekspor sesuai filter yang dipilih.');
            }

            foreach($data as $item) {
                $item->update(['kode_form' => $request->kode_form]);
            }

            $kode_form = $request->kode_form;
            $filename = 'label_pemeriksaan_bulk_' . date('Y-m-d_H-i-s') . '.pdf';
            
            $filterInfo = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift' => $request->shift_id ? \App\Models\DataShift::find($request->shift_id)->shift : 'Semua',
                'produk' => $produkId ? \App\Models\JenisProduk::find($produkId)->nama_produk : 'Semua',
                'is_audit' => $isAudit
            ];

            $pdf = Pdf::loadView('qc-sistem.label_pemeriksaan.export_pdf', compact('data', 'kode_form', 'filterInfo'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Gagal membuat PDF bulk: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // ===================== AUDIT METHODS =====================

    public function copyToAudit($uuid)
    {
        $user = Auth::user();
        
        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-label-pemeriksaan')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = LabelStartUp::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('label-pemeriksaan.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('label-pemeriksaan.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('label-pemeriksaan.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $auditedSourceUuids = LabelStartUp::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = LabelStartUp::with(['plan', 'shift', 'user', 'details.produk'])
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->when($user->role_id != 1, function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->whereHas('details.produk', function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%");
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('id_shift', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Load shifts and produks for the filter modal
        if ($user->role_id == 1) { // Superadmin can see all
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.label_pemeriksaan.index_audit', compact('records', 'search', 'perPage', 'shifts', 'produks'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = LabelStartUp::with(['plan', 'shift', 'user', 'details.produk'])
                ->where('is_audit', true)
                ->when($user->role_id != 1, fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->whereHas('details.produk', function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('id_shift', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'html' => view('qc-sistem.label_pemeriksaan._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($records->total() / $perPage),
                    'per_page' => $perPage,
                    'total' => $records->total(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $records->total() > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkExportExcel(Request $request, LabelStartUpBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }

    /**
     * Download template untuk import (Global Standard via HasExcelImport trait)
     * Implementasi abstract method wajib dari trait.
     */
    protected function getImportModelClass(): string
    {
        return LabelStartUp::class;
    }

    /**
     * Nama file template Excel yang akan didownload.
     * Implementasi abstract method wajib dari trait.
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Label_Pemeriksaan.xlsx';
    }
}
