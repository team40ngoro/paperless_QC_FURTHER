<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanChillroom;
use App\Models\ChillroomLog;
use Illuminate\Http\Request;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\DataRm;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\ChillroomBulkExportService;

class ChillroomController extends Controller
{
    use HasNoDataHtmlResponse, HasExcelImport;
    // Tampilkan semua data
    public function index(Request $request)
    {
        $user = auth()->user();

        // Auditor langsung ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('chillroom.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover', 'auditVersion'])
            ->where('is_audit', false)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam_kedatangan', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate($perPage);
        
        $groupedData = $records->getCollection()->groupBy(function($item) {
            $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
            return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
        });
        
        $records->setCollection($groupedData);

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.chillroom.index', compact('records', 'search', 'perPage', 'shifts'));
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $isAudit = $request->get('is_audit', 0);

            $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover', 'auditVersion'])
                ->where('is_audit', $isAudit)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam_kedatangan', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->orderBy('id', 'desc')
                             ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(function($item) {
                $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
                return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view('qc-sistem.chillroom._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
    public function create()
    {
          $user = auth()->user();
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
        $shifts = DataShift::all();
        $dataRm = DataRm::all();
        } else {
          
          
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataRm = DataRm::where('id_plan', $user->id_plan)->get();
           
        }

      
        return view('qc-sistem.chillroom.create', compact('shifts', 'plans', 'dataRm'));
    }
    

   // Simpan data baru
    public function store(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Request data received:', $request->all());
        
        $data = $request->validate([
            // Common fields (shared across all entries)
            'tanggal' => 'required|date',
            'shift_id' => 'required|integer',
            'jam_kedatangan' => 'required|date_format:H:i',
            
            // Entry arrays (repeater fields)
            'entries' => 'required|array|min:1',
            'entries.*.nama_rm' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.berat' => 'required|string',
            'entries.*.suhu' => 'nullable|numeric',
            'entries.*.sensori' => 'nullable|string',
            'entries.*.kemasan' => 'required|string',
            'entries.*.keterangan' => 'nullable|string',
            'entries.*.standar_berat' => 'nullable|string',
            'entries.*.jumlah_rm_value' => 'nullable|array',
            'entries.*.jumlah_rm_value.*' => 'nullable|numeric',
            'entries.*.berat_atas' => 'nullable|numeric',
            'entries.*.berat_std' => 'nullable|numeric',
            'entries.*.berat_bawah' => 'nullable|numeric',
            'entries.*.status_rm' => 'nullable|string',
            'entries.*.catatan_rm' => 'nullable|string',
        ]);

        $user = auth()->user();
        $successCount = 0;
        $errors = [];

        try {
                foreach ($data['entries'] as $index => $entry) {
                    try {
                        // Skip entries yang kosong atau tidak memiliki data penting
                        if (empty($entry['nama_rm']) && empty($entry['kode_produksi']) && empty($entry['berat'])) {
                            continue; // Skip entry kosong
                        }
                        
                        // PERBAIKAN: Siapkan data jumlah_rm dari berat_atas, berat_std, berat_bawah
                        $jumlahRmData = [
                            'entry_' . ($index + 1) => [
                                'berat_atas' => !empty($entry['berat_atas']) ? (int)$entry['berat_atas'] : 0,
                                'berat_std' => !empty($entry['berat_std']) ? (int)$entry['berat_std'] : 0,
                                'berat_bawah' => !empty($entry['berat_bawah']) ? (int)$entry['berat_bawah'] : 0,
                            ]
                        ];
                        
                        // PERBAIKAN: Siapkan data sampel berat terpisah untuk nilai_jumlah_rm
                        $nilaiSampelData = [];
                        if (!empty($entry['jumlah_rm_value']) && is_array($entry['jumlah_rm_value'])) {
                            // Filter out empty values and convert to float
                            $filteredValues = array_filter($entry['jumlah_rm_value'], function($value) {
                                return $value !== null && trim((string)$value) !== '' && is_numeric($value);
                            });
                            
                            if (!empty($filteredValues)) {
                                // Store as array of sample values
                                $nilaiSampelData['entry_' . ($index + 1)] = array_values(array_map('floatval', $filteredValues));
                            }
                        }
                        
                        // If no valid samples, create empty array structure
                        if (empty($nilaiSampelData)) {
                            $nilaiSampelData['entry_' . ($index + 1)] = [];
                        }

                        // Role QC hanya boleh input card Detail Bahan Baku; card Sampling Berat Daging SPO diabaikan/dikosongkan
                        if ($user && $user->isQc()) {
                            $entry['standar_berat'] = '-';
                            $entry['berat_atas'] = null;
                            $entry['berat_std'] = null;
                            $entry['berat_bawah'] = null;
                            $entry['status_rm'] = '-';
                            $entry['catatan_rm'] = '-';
                            $jumlahRmData = [
                                'entry_' . ($index + 1) => [
                                    'berat_atas' => 0,
                                    'berat_std' => 0,
                                    'berat_bawah' => 0,
                                ]
                            ];
                            $nilaiSampelData = [
                                'entry_' . ($index + 1) => []
                            ];
                        }
                        
                        \Log::info("Entry {$index} - jumlah_rm_data (agregasi):", $jumlahRmData);
                        \Log::info("Entry {$index} - nilai_jumlah_rm (sampel berat):", $nilaiSampelData);
                        
                        PenerimaanChillroom::create([
                            'uuid' => Str::uuid(),
                            'id_plan' => $user->id_plan,
                            'user_id' => $user->id,
                            'tanggal' => Carbon::parse($data['tanggal'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
                            'shift_id' => $data['shift_id'],
                            'jam_kedatangan' => $data['jam_kedatangan'],
                            'nama_rm' => $entry['nama_rm'] ?? '-',
                            'kode_produksi' => $entry['kode_produksi'] ?? '-',
                            'berat' => $entry['berat'] ?? '-',
                            'suhu' => $entry['suhu'] ?? '0',
                            'sensori' => $entry['sensori'] ?? '-',
                            'kemasan' => $entry['kemasan'] ?? '-',
                            'keterangan' => $entry['keterangan'] ?? '-',
                            'standar_berat' => $entry['standar_berat'] ?? '-',
                            'berat_atas' => $entry['berat_atas'] ?? null,
                            'berat_std' => $entry['berat_std'] ?? null,
                            'berat_bawah' => $entry['berat_bawah'] ?? null,
                            // PERBAIKAN: jumlah_rm berisi agregasi berat (atas, std, bawah)
                            'jumlah_rm' => json_encode($jumlahRmData),
                            // PERBAIKAN: nilai_jumlah_rm berisi data sampel berat
                            'nilai_jumlah_rm' => json_encode($nilaiSampelData),
                            'status_rm' => $entry['status_rm'] ?? '-',
                            'catatan_rm' => $entry['catatan_rm'] ?? '-',
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        \Log::error("Error saving entry {$index}: " . $e->getMessage());
                        $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
                    }
                }

                if ($successCount > 0) {
                    $message = "Berhasil menyimpan {$successCount} data chillroom";
                    if (!empty($errors)) {
                        $message .= ". Beberapa data gagal disimpan: " . implode(', ', $errors);
                    }
                    return redirect()->route('chillroom.index')->with('success', $message);
                } else {
                    return redirect()->back()->withErrors($errors)->withInput();
                }

            } catch (\Exception $e) {
                \Log::error("Store error: " . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
            }
    }

    // Tampilkan detail data
    public function show($uuid)
    {
        $chillroom = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover', 'qcVerifications.verifiedBy'])
            ->where('uuid', $uuid)->firstOrFail();
        
        $user = auth()->user();
        if ($user->role !== 'superadmin' && (int) $chillroom->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('chillroom.show', $chillroom)) {
            return $redirect;
        }

        return view('qc-sistem.chillroom.show', compact('chillroom'));
    }

    // Tampilkan detail data untuk diubah
    public function edit($uuid)
    {
        $chillroom = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();

        // Check edit time limit using global config
        $user = auth()->user();
        if ($redirect = $this->redirectToCanonicalAuditUrl('chillroom.edit', $chillroom)) {
            return $redirect;
        }

        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($chillroom->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('chillroom.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
        $shifts = DataShift::all();
        $dataRm = DataRm::all();
        } else {
          
          
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataRm = DataRm::where('id_plan', $user->id_plan)->get();
           
        }
        return view('qc-sistem.chillroom.edit', compact('chillroom', 'shifts', 'plans', 'dataRm'));
    }
   
    // Update data
    public function update(Request $request, $uuid)
    {
        try {
            // Find the record
            $data = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();

            // Check edit time limit using global config
            $user = auth()->user();
            $restrictedRoles = config('edit_limit.restricted_role_ids', []);
            if (in_array($user->id_role, $restrictedRoles)) {
                $timeLimitHours = config('edit_limit.time_limit_hours', 48);
                $createdAt = \Carbon\Carbon::parse($data->created_at);
                if ($createdAt->addHours($timeLimitHours)->isPast()) {
                    return redirect()->route('chillroom.index')
                        ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
                }
            }
            
            // Validate request
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required|integer',
                'jam_kedatangan' => 'required|date_format:H:i',
                'nama_rm' => 'required|string',
                'kode_produksi' => 'required|string',
                'berat' => 'required|string',
                'suhu' => 'nullable|numeric',
                'sensori' => 'nullable|string',
                'kemasan' => 'required|string',
                'keterangan' => 'nullable|string',
                'standar_berat' => 'nullable|string',
                'berat_samples' => 'nullable|array',
                'berat_samples.*' => 'nullable|numeric',
                'berat_atas' => 'nullable|numeric',
                'berat_std' => 'nullable|numeric',
                'berat_bawah' => 'nullable|numeric',
                'status_rm' => 'nullable|string',
                'catatan_rm' => 'nullable|string',
            ]);

            // PERBAIKAN: Siapkan data jumlah_rm dari berat_atas, berat_std, berat_bawah
            $jumlahRmData = [
                'entry_1' => [
                    'berat_atas' => !empty($validated['berat_atas']) ? (int)$validated['berat_atas'] : 0,
                    'berat_std' => !empty($validated['berat_std']) ? (int)$validated['berat_std'] : 0,
                    'berat_bawah' => !empty($validated['berat_bawah']) ? (int)$validated['berat_bawah'] : 0,
                ]
            ];
            
            // PERBAIKAN: Siapkan data sampel berat terpisah untuk nilai_jumlah_rm
            $nilaiSampelData = [];
            if (!empty($validated['berat_samples']) && is_array($validated['berat_samples'])) {
                // Filter out empty values and convert to float
                $filteredValues = array_filter($validated['berat_samples'], function($value) {
                    return $value !== null && trim((string)$value) !== '' && is_numeric($value);
                });
                
                if (!empty($filteredValues)) {
                    // Store as array of sample values
                    $nilaiSampelData['entry_1'] = array_values(array_map('floatval', $filteredValues));
                }
            }
            
            // If no valid samples, create empty array structure
            if (empty($nilaiSampelData)) {
                $nilaiSampelData['entry_1'] = [];
            }
            
            // Log untuk debugging
            \Log::info('Update - jumlah_rm_data (agregasi):', $jumlahRmData);
            \Log::info('Update - nilai_jumlah_rm (sampel berat):', $nilaiSampelData);

            // Prepare old data for logging
            $oldData = [
                'tanggal' => $data->tanggal,
                'shift_id' => $data->shift_id,
                'jam_kedatangan' => $data->jam_kedatangan,
                'nama_rm' => $data->nama_rm,
                'kode_produksi' => $data->kode_produksi,
                'berat' => $data->berat,
                'suhu' => $data->suhu,
                'sensori' => $data->sensori,
                'kemasan' => $data->kemasan,
                'keterangan' => $data->keterangan,
                'standar_berat' => $data->standar_berat,
                'berat_atas' => $data->berat_atas,
                'berat_std' => $data->berat_std,
                'berat_bawah' => $data->berat_bawah,
                'jumlah_rm' => $data->jumlah_rm,
                'nilai_jumlah_rm' => $data->nilai_jumlah_rm,
                'status_rm' => $data->status_rm,
                'catatan_rm' => $data->catatan_rm,
            ];

            // Role QC hanya boleh mengubah Detail Bahan Baku; pertahankan data sampling berat SPO yang sudah ada
            $isQc = $user && $user->isQc();
            if ($isQc) {
                $validated['standar_berat'] = $data->standar_berat;
                $validated['berat_atas'] = $data->berat_atas;
                $validated['berat_std'] = $data->berat_std;
                $validated['berat_bawah'] = $data->berat_bawah;
                $validated['status_rm'] = $data->status_rm;
                $validated['catatan_rm'] = $data->catatan_rm;
                $finalJumlahRm = $data->jumlah_rm;
                $finalNilaiSampel = $data->nilai_jumlah_rm;
            } else {
                $finalJumlahRm = json_encode($jumlahRmData);
                $finalNilaiSampel = json_encode($nilaiSampelData);
            }

            // Update the record
            $data->update([
                'tanggal' => Carbon::parse($validated['tanggal'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'shift_id' => $validated['shift_id'],
                'jam_kedatangan' => $validated['jam_kedatangan'],
                'nama_rm' => $validated['nama_rm'],
                'kode_produksi' => $validated['kode_produksi'],
                'berat' => $validated['berat'],
                'suhu' => $validated['suhu'] ?? $data->suhu ?? '0',
                'sensori' => $validated['sensori'] ?? $data->sensori ?? '-',
                'kemasan' => $validated['kemasan'],
                'keterangan' => $validated['keterangan'] ?? '-',
                'standar_berat' => $validated['standar_berat'] ?? null,
                'berat_atas' => $validated['berat_atas'] ?? null,
                'berat_std' => $validated['berat_std'] ?? null,
                'berat_bawah' => $validated['berat_bawah'] ?? null,
                // PERBAIKAN: jumlah_rm berisi agregasi berat (atas, std, bawah)
                'jumlah_rm' => $finalJumlahRm,
                // PERBAIKAN: nilai_jumlah_rm berisi data sampel berat
                'nilai_jumlah_rm' => $finalNilaiSampel,
                'status_rm' => $validated['status_rm'] ?? '-',
                'catatan_rm' => $validated['catatan_rm'] ?? '-',
            ]);

            // Prepare new data for logging
            $newData = [
                'tanggal' => $data->tanggal,
                'shift_id' => $data->shift_id,
                'jam_kedatangan' => $data->jam_kedatangan,
                'nama_rm' => $data->nama_rm,
                'kode_produksi' => $data->kode_produksi,
                'berat' => $data->berat,
                'suhu' => $data->suhu,
                'sensori' => $data->sensori,
                'kemasan' => $data->kemasan,
                'keterangan' => $data->keterangan,
                'standar_berat' => $data->standar_berat,
                'berat_atas' => $data->berat_atas,
                'berat_std' => $data->berat_std,
                'berat_bawah' => $data->berat_bawah,
                'jumlah_rm' => $data->jumlah_rm,
                'nilai_jumlah_rm' => $data->nilai_jumlah_rm,
                'status_rm' => $data->status_rm,
                'catatan_rm' => $data->catatan_rm,
            ];

            // Detect changed fields
            $changedFields = [];
            foreach ($oldData as $key => $value) {
                if ($value != $newData[$key]) {
                    $changedFields[] = $key;
                }
            }

            // Create log entry
            if (!empty($changedFields)) {
                $user = auth()->user();
                
                ChillroomLog::create([
                    'uuid' => Str::uuid(),
                    'penerimaan_chillroom_id' => $data->id,
                    'penerimaan_chillroom_uuid' => $data->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'update',
                    'field_yang_diubah' => $changedFields,
                    'nilai_lama' => array_intersect_key($oldData, array_flip($changedFields)),
                    'nilai_baru' => array_intersect_key($newData, array_flip($changedFields)),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => 'Data updated'
                ]);
            }

        // Redirect based on audit status
        if ($data->is_audit) {
            return redirect()->route('chillroom.audit')->with('success', 'Data Audit berhasil diperbarui');
        }

        return redirect()->route('chillroom.index')->with('success', 'Data operasional berhasil diperbarui');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during update: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Update error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengupdate data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Hapus data
    public function destroy($uuid)
    {
          $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('chillroom.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Show logs for specific chillroom data
     */
    public function showLogs($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        
        $logs = ChillroomLog::where('penerimaan_chillroom_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.chillroom.logs', compact('item', 'logs'));
    }

    /**
     * Simpan verifikasi QC untuk data Chillroom.
     */
    public function storeQcVerification(Request $request, $uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if (!$user || !$user->isQc()) {
            abort(403, 'Hanya role QC yang dapat melakukan verifikasi ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:OK,NOT_OK',
            'suhu_rm' => 'nullable|numeric|between:-60,60',
            'sensori' => 'nullable|in:✔,✘',
            'note' => 'nullable|string|max:1000',
        ]);

        $latest = $item->qcVerifications()->first();

        $verification = $item->qcVerifications()->create([
            'penerimaan_chillroom_uuid' => $item->uuid,
            'status' => $validated['status'],
            'suhu_rm' => $validated['suhu_rm'] ?? null,
            'sensori' => $validated['sensori'] ?? null,
            'note' => $validated['note'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        $item->update([
            'approved_by_qc' => true,
            'qc_approved_by' => $user->id,
            'qc_approved_at' => now(),
        ]);

        ChillroomLog::create([
            'penerimaan_chillroom_id' => $item->id,
            'penerimaan_chillroom_uuid' => $item->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'QC',
            'aksi' => 'qc_verification',
            'field_yang_diubah' => ['qc_verification_status', 'suhu_rm', 'sensori'],
            'nilai_lama' => ['qc_verification_status' => $latest->status ?? '-', 'suhu_rm' => $latest->suhu_rm ?? null, 'sensori' => $latest->sensori ?? null],
            'nilai_baru' => ['qc_verification_status' => $verification->status, 'suhu_rm' => $verification->suhu_rm, 'sensori' => $verification->sensori],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'keterangan' => $validated['note'] ?? 'Verifikasi QC',
        ]);

        return redirect()->back()->with('success', 'Verifikasi QC berhasil disimpan.');
    }

    /**
     * Tampilkan log verifikasi QC untuk data Chillroom.
     */
    public function qcVerificationLogs($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        $verifications = $item->qcVerifications()
            ->with('verifiedBy')
            ->orderByDesc('verified_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('qc-sistem.chillroom.qc-verification-logs', compact('item', 'verifications'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        
        $logs = ChillroomLog::where('penerimaan_chillroom_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($log) {
                        return [
                            'id' => $log->id,
                            'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                            'user' => $log->user_name,
                            'role' => $log->user_role,
                            'aksi' => ucfirst($log->aksi),
                            'field_diubah' => implode(', ', $log->field_yang_diubah ?? []),
                            'deskripsi' => $log->deskripsi_perubahan,
                            'ip_address' => $log->ip_address
                        ];
                    });
        
        return response()->json($logs);
    }

    /**
     * Log chillroom activity
     */
   private function logActivity($chillroomUuid, $action, $oldData = null, $newData = null)
{
    try {
        $user = auth()->user();
        $chillroom = PenerimaanChillroom::where('uuid', $chillroomUuid)->first();
        
        if (!$chillroom) {
            \Log::warning("Chillroom not found for UUID: {$chillroomUuid}");
            return;
        }

        // Detect changed fields
        $changedFields = [];
        $nilaiLama = [];
        $nilaiBaru = [];

        if ($oldData && $newData) {
            foreach ($oldData as $key => $value) {
                if (isset($newData[$key]) && $value != $newData[$key]) {
                    $changedFields[] = $key;
                    $nilaiLama[$key] = $value;
                    $nilaiBaru[$key] = $newData[$key];
                }
            }
        }

        ChillroomLog::create([
            'uuid' => Str::uuid(),
            'penerimaan_chillroom_id' => $chillroom->id,
            'penerimaan_chillroom_uuid' => $chillroom->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'Unknown',
            'aksi' => $action,
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $nilaiBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => ucfirst($action) . ' performed'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error logging activity: ' . $e->getMessage());
    }
}

    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->boolean('is_audit')) {
                $auditedSourceUuids = PenerimaanChillroom::where('is_audit', true)
                    ->whereNotNull('source_uuid')
                    ->pluck('source_uuid')
                    ->toArray();

                $query->where(function ($q) use ($auditedSourceUuids) {
                    $q->where('is_audit', true)
                      ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                          $subQ->where('is_audit', false);
                          if (!empty($auditedSourceUuids)) {
                              $subQ->whereNotIn('uuid', $auditedSourceUuids);
                          }
                      });
                });
            } else {
                $query->where('is_audit', false);
            }

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal_mulai && $request->tanggal_akhir) {
                    $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' sampai ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
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

            // Prepare filters for PDF view
            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = PDF::loadView('qc-sistem.chillroom.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'chillroom_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save kode form for specific record
     */
    public function updateKodeForm(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = PenerimaanChillroom::where('uuid', $request->uuid)->firstOrFail();
            
            // Update kode form
            $item->update([
                'kode_form' => $request->kode_form
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode form berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kode form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            // Validasi role dan type yang diizinkan
            $allowedRoles = [
                'spv' => [4] // Role 4 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan persetujuan ini'
                ], 403);
            }

            $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();

            // Check if already approved
            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 422);
            }
            
            // Update field approval yang sesuai
            $approverField = $type . '_approved_by';
            $approvalDateField = $type . '_approved_at';
            
            $item->update([
                $approvalField => true,
                $approverField => $user->id,
                $approvalDateField => now()
            ]);

            // Log approval activity
            ChillroomLog::create([
                'uuid' => Str::uuid(),
                'penerimaan_chillroom_id' => $item->id,
                'penerimaan_chillroom_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'Unknown',
                'aksi' => 'approval',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Smart Copy & Edit Audit:
     * Jika belum ada versi audit -> copy ke audit lalu langsung buka form edit audit.
     * Jika sudah ada versi audit -> langsung buka form edit versi yang sudah ada.
     */
    public function copyToAudit($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('chillroom.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('chillroom.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('chillroom.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    /**
     * Index page for Audit records dengan Audit-First Smart Filter.
     * Menampilkan data audit (is_audit = true) DIPADUKAN DENGAN data operasional
     * yang BELUM memiliki versi audit.
     */
    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        // Ambil daftar source_uuid yang sudah di-copy ke audit (bebas collation error)
        $auditedSourceUuids = PenerimaanChillroom::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'])
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam_kedatangan', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate($perPage);

        $groupedData = $records->getCollection()->groupBy(function ($item) {
            $dateStr = $item->tanggal
                ? (is_string($item->tanggal)
                    ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                    : $item->tanggal->format('Y-m-d'))
                : 'no_date';
            return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
        });

        $records->setCollection($groupedData);

        return view('qc-sistem.chillroom.index_audit', compact('records', 'search', 'perPage'));
    }

    /**
     * AJAX search untuk halaman audit chillroom.
     */
    public function auditSearchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $auditedSourceUuids = PenerimaanChillroom::where('is_audit', true)
                ->whereNotNull('source_uuid')
                ->pluck('source_uuid')
                ->toArray();

            $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'])
                ->where(function ($q) use ($auditedSourceUuids) {
                    $q->where('is_audit', true)
                      ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                          $subQ->where('is_audit', false);
                          if (!empty($auditedSourceUuids)) {
                              $subQ->whereNotIn('uuid', $auditedSourceUuids);
                          }
                      });
                })
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam_kedatangan', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->orderBy('id', 'desc')
                             ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(function ($item) {
                $dateStr = $item->tanggal
                    ? (is_string($item->tanggal)
                        ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                        : $item->tanggal->format('Y-m-d'))
                    : 'no_date';
                return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view('qc-sistem.chillroom._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
    public function bulkApprove(Request $request)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // spv
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            // Role check - verifikasi QC hanya melalui form verifikasi QC
            $userRole = $user->id_role;
            $allowedRoles = [
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'], 403);
            }

            // Find records in this shift
            $query = PenerimaanChillroom::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shift_id)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $items = $query->get();
            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan untuk shift ini.'], 404);
            }

            // Check Sequential
            if ($type === 'spv') {
                if ($items->contains(fn($i) => !$i->approved_by_qc)) {
                    return response()->json(['success' => false, 'message' => 'Verifikasi QC belum lengkap untuk shift ini.'], 422);
                }
            }

            // Update
            $updateData = [
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            $query->update($updateData);

            // SYNC to audit versions: Update audit records dengan source_uuid yang sama
            foreach ($items as $item) {
                if ($item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }

            // Log
            foreach ($items as $item) {
                ChillroomLog::create([
                    'uuid' => (string) Str::uuid(),
                    'penerimaan_chillroom_id' => $item->id,
                    'penerimaan_chillroom_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval',
                    'field_yang_diubah' => ['approved_by_' . $type],
                    'nilai_lama' => ['approved_by_' . $type => false],
                    'nilai_baru' => ['approved_by_' . $type => true],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved by ' . strtoupper($type)
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Berhasil verifikasi masal ' . strtoupper($type)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkApproveSelected(Request $request)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // spv
            $uuids = $request->input('uuids', []); // array of selected UUIDs

            // Validate - verifikasi QC hanya melalui form verifikasi QC
            $request->validate([
                'type' => 'required|in:spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid'
            ]);

            // Role check
            $userRole = $user->id_role;
            $allowedRoles = [
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'], 403);
            }

            // Find records by UUIDs - only unapproved ones (smart auto-skip)
            $query = PenerimaanChillroom::whereIn('uuid', $uuids)
                ->where('approved_by_' . $type, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json(['success' => true, 'message' => 'Semua data sudah diverifikasi sebelumnya.']);
            }

            // Update
            $updateData = [
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            PenerimaanChillroom::whereIn('uuid', $uuids)
                ->where('approved_by_' . $type, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // SYNC to audit versions: Update audit records dengan source_uuid yang sama
            foreach ($itemsToUpdate as $item) {
                if ($item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }

            // Log
            foreach ($itemsToUpdate as $item) {
                ChillroomLog::create([
                    'uuid' => (string) Str::uuid(),
                    'penerimaan_chillroom_id' => $item->id,
                    'penerimaan_chillroom_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval_selected',
                    'field_yang_diubah' => ['approved_by_' . $type],
                    'nilai_lama' => ['approved_by_' . $type => false],
                    'nilai_baru' => ['approved_by_' . $type => true],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved Selected by ' . strtoupper($type)
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Berhasil verifikasi ' . $count . ' data ' . strtoupper($type)]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkExportExcel(Request $request, ChillroomBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }

    /**
     * ============================================================
     * EXCEL IMPORT TRAIT IMPLEMENTATION
     * ============================================================
     */

    /**
     * Get the model class for import
     */
    protected function getImportModelClass(): string
    {
        return PenerimaanChillroom::class;
    }

    /**
     * Get the template filename
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Import_Chillroom_' . date('Y-m-d_H-i-s') . '.xlsx';
    }
}