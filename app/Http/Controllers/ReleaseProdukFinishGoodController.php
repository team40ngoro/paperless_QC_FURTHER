<?php

namespace App\Http\Controllers;

use App\Models\ReleaseProdukFinishGood;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReleaseProdukFinishGoodController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('release-produk.audit');
        }
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = ReleaseProdukFinishGood::with(['plan', 'user', 'shift', 'produk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $groupedData = $records->getCollection()->groupBy(function($item) {
            $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
            return $dateStr . '_' . ($item->id_shift ?? 'no_shift');
        });
        $records->setCollection($groupedData);

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.release_produk.index', compact('records', 'search', 'perPage', 'shifts', 'produks'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.release_produk.create', compact('produks', 'plans', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'id_shift' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'suhu_iqf' => 'required|numeric',
            'suhu_produk' => 'required|string',
            'lama_pembekuan' => 'required|integer',
            'foto_kemasan_sekunder' => 'nullable|image|mimes:jpeg,png,jpg',
            'foto_kemasan_primer' => 'nullable|image|mimes:jpeg,png,jpg',
            'kondisi_kemasan_primer' => 'required|string',
            'berat_packing_sekunder' => 'required|array',
            'berat_packing_sekunder.*' => 'required|numeric',
            'jumlah_box' => 'required|integer',
            'status' => 'required|in:Release,Tidak Release',
            'keterangan' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
        ]);

        $user = Auth::user();
        $data = $request->all();
        $data['uuid'] = Str::uuid();
        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;

        // Handle Image Uploads with backend compression as safety
        if ($request->hasFile('foto_kemasan_sekunder')) {
            $data['foto_kemasan_sekunder'] = $this->uploadImage($request->file('foto_kemasan_sekunder'), 'sekunder');
        }

        if ($request->hasFile('foto_kemasan_primer')) {
            $data['foto_kemasan_primer'] = $this->uploadImage($request->file('foto_kemasan_primer'), 'primer');
        }

        ReleaseProdukFinishGood::create($data);

        return redirect()->route('release-produk.index')->with('success', 'Data Release Produk berhasil disimpan.');
    }

    public function show($uuid)
    {
        $item = ReleaseProdukFinishGood::where('uuid', $uuid)->with(['plan', 'user', 'shift', 'produk'])->firstOrFail();
        
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403);
        }

        return view('qc-sistem.release_produk.show', compact('item'));
    }

    public function edit($uuid)
    {
        $item = ReleaseProdukFinishGood::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403);
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('release-produk.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        return view('qc-sistem.release_produk.edit', compact('item', 'produks', 'plans', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $item = ReleaseProdukFinishGood::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403);
        }

        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('release-produk.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'id_shift' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'suhu_iqf' => 'required|numeric',
            'suhu_produk' => 'required|string',
            'lama_pembekuan' => 'required|integer',
            'foto_kemasan_sekunder' => 'nullable|image|mimes:jpeg,png,jpg',
            'foto_kemasan_primer' => 'nullable|image|mimes:jpeg,png,jpg',
            'kondisi_kemasan_primer' => 'required|string',
            'berat_packing_sekunder' => 'required|array',
            'berat_packing_sekunder.*' => 'required|numeric',
            'jumlah_box' => 'required|integer',
            'status' => 'required|in:Release,Tidak Release',
            'keterangan' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_kemasan_sekunder')) {
            if ($item->foto_kemasan_sekunder) {
                Storage:: disk('public')->delete($item->foto_kemasan_sekunder);
            }
            $data['foto_kemasan_sekunder'] = $this->uploadImage($request->file('foto_kemasan_sekunder'), 'sekunder');
        }

        if ($request->hasFile('foto_kemasan_primer')) {
            if ($item->foto_kemasan_primer) {
                Storage::disk('public')->delete($item->foto_kemasan_primer);
            }
            $data['foto_kemasan_primer'] = $this->uploadImage($request->file('foto_kemasan_primer'), 'primer');
        }

        $item->update($data);

        return redirect()->route('release-produk.index')->with('success', 'Data Release Produk berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $item = ReleaseProdukFinishGood::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403);
        }

        if ($item->foto_kemasan_sekunder) {
            Storage::disk('public')->delete($item->foto_kemasan_sekunder);
        }
        if ($item->foto_kemasan_primer) {
            Storage::disk('public')->delete($item->foto_kemasan_primer);
        }

        $item->delete();

        return redirect()->route('release-produk.index')->with('success', 'Data Release Produk berhasil dihapus.');
    }

    private function uploadImage($file, $type)
    {
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            $extension = 'jpg'; // Fallback
        }
        
        $filename = time() . '_' . uniqid() . '_' . $type . '.' . $extension;
        $path = 'uploads/release_produk/' . $filename;
        
        // Use Intervention Image to resize/compress
        $image = Image::make($file)->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Encode based on extension
        if (strtolower($extension) === 'png') {
            $image->encode('png', 7); // PNG compression 0-9
        } else {
            $image->encode('jpg', 80);
        }
        
        Storage::disk('public')->put($path, $image->stream());
        
        return $path;
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = ReleaseProdukFinishGood::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('produk', function ($q) use ($search) {
                        $q->where('nama_produk', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('jam', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            $total = $records->total();

            $groupedData = $records->getCollection()->groupBy(function($item) {
                $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
                return $dateStr . '_' . ($item->id_shift ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view('qc-sistem.release_produk._table', compact('records'))->render(),
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

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [2],
                'spv' => [4]
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
            }

            $item = ReleaseProdukFinishGood::where('uuid', $uuid)->firstOrFail();

            if ($type === 'produksi' && !$item->approved_by_qc) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh QC terlebih dahulu.'], 422);
            }
            if ($type === 'spv' && !$item->approved_by_produksi) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'], 422);
            }

            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json(['success' => false, 'message' => 'Data sudah disetujui.'], 422);
            }
            
            $item->update([
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui data: ' . $e->getMessage()], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type');
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            $userRole = $user->id_role;
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas.'], 403);
            }

            $query = ReleaseProdukFinishGood::whereDate('tanggal', $tanggal)
                ->where('id_shift', $shift_id)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $items = $query->get();
            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            if ($type === 'produksi' && $items->contains(fn($i) => !$i->approved_by_qc)) {
                return response()->json(['success' => false, 'message' => 'Verifikasi QC belum lengkap.'], 422);
            } elseif ($type === 'spv' && $items->contains(fn($i) => !$i->approved_by_produksi)) {
                return response()->json(['success' => false, 'message' => 'Verifikasi Produksi belum lengkap.'], 422);
            }

            $query->update([
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Berhasil verifikasi masal ' . strtoupper($type)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

            $query = ReleaseProdukFinishGood::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->where('is_audit', $isAudit)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
            }
            if ($request->filled('shift_id')) {
                $query->where('id_shift', $request->shift_id);
            }
            if ($produkId) {
                $query->where('id_produk', $produkId);
            }

            $data = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

            if ($data->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk diekspor sesuai filter yang dipilih.');
            }

            foreach ($data as $item) {
                $item->update(['kode_form' => $request->kode_form]);
            }

            $kode_form = $request->kode_form;
            $filename = 'release_produk_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            $filterInfo = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift' => $request->shift_id ? \App\Models\DataShift::find($request->shift_id)->shift : 'Semua',
                'produk' => $produkId ? \App\Models\JenisProduk::find($produkId)->nama_produk : 'Semua',
                'is_audit' => $isAudit
            ];

            $pdf = Pdf::loadView('qc-sistem.release_produk.export_pdf', compact('data', 'kode_form', 'filterInfo'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Gagal membuat PDF bulk: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
    public function exportPdf($uuid)
    {
        try {
            $item = ReleaseProdukFinishGood::where('uuid', $uuid)
                ->with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->firstOrFail();

            $data = collect([$item]);
            $kode_form = $item->kode_form ?: 'QF 14/00';
            
            $pdf = Pdf::loadView('qc-sistem.release_produk.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->stream('release_produk_' . $item->uuid . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cetak PDF: ' . $e->getMessage());
        }
    }

    // ===================== AUDIT METHODS =====================

    public function copyToAudit($uuid)
    {
        $user = Auth::user();
        
        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-release-produk')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = ReleaseProdukFinishGood::where('uuid', $uuid)->firstOrFail();
        $clone = $item->copyToAudit();

        return redirect()->route('release-produk.audit')
            ->with('success', 'Data berhasil dicopy ke Audit. Silakan lakukan penyesuaian.');
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = ReleaseProdukFinishGood::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = ReleaseProdukFinishGood::with(['plan', 'user', 'shift', 'produk'])
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
                $q->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.release_produk.index_audit', compact('records', 'search', 'perPage', 'shifts', 'produks'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = ReleaseProdukFinishGood::with(['plan', 'user', 'shift', 'produk'])
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('produk', function ($q) use ($search) {
                        $q->where('nama_produk', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('jam', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'html' => view('qc-sistem.release_produk._table_audit', compact('records'))->render(),
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
}
