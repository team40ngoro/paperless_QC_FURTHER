<?php

namespace App\Http\Controllers;

use App\Models\ProsesTumbling;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\DataTumbling;
use App\Models\DataShift;
use App\Models\BahanBakuTumbling;
use App\Models\ProsesMarinadeModel;
use App\Models\ProsesTumblingLog;
use App\Models\ProsesAging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class ProsesTumblingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ProsesTumbling::with(['plan', 'user', 'produk', 'dataTumbling', 'shift','prosesAging']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($qp) use ($search) {
                      $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $prosesTumbling = $query->orderBy('created_at', 'desc')
                                 ->paginate(10);
        return view('qc-sistem.proses_tumbling.index', compact('prosesTumbling'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $bahanBakuTumbling = null;
        $prosesAging = null;
        
        // Jika ada parameter bahan_baku_uuid, ambil data bahan baku
        if ($request->has('bahan_baku_uuid')) {
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $request->bahan_baku_uuid)->first();
            if (!$bahanBakuTumbling) {
                return redirect()->route('bahan-baku-tumbling.index')
                    ->with('error', 'Data Bahan Baku Tumbling tidak ditemukan.');
            }
        }
        
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.proses_tumbling.create', compact(
            'produks', 'shifts', 'bahanBakuTumbling'
        ));
    }

    public function store(Request $request)
    {
        \Log::info('Data masuk ke store:', $request->all());

        $validated = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_produksi' => 'required|string',
            'tanggal' => 'required|string', // Ubah validasi
            'jam' => 'required|string',
            'tumbling_data' => 'required|array',
            'tumbling_data.*.id_tumbling' => 'required|exists:data_tumbling,id',
            'tumbling_data.*.suhu_hasil_tumbling' => 'nullable|string',
            'tumbling_data.*.sensori_hasil_tumbling' => 'nullable|in:oke,tidak ok',
        ]);
        \Log::info('Data lolos validasi:', $validated);

        try {
            // Format tanggal sebelum loop
            $formattedDate = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
            $savedCount = 0;
            
            // Ambil data relasi dari parameter URL
            $bahanBakuUuid = $request->input('bahan_baku_uuid') ?: $request->query('bahan_baku_uuid');
            $marinadeUuid = $request->input('marinade_uuid') ?: $request->query('marinade_uuid');
            
            $bahanBakuTumbling = null;
            $prosesMarinade = null;
            
            if ($bahanBakuUuid) {
                $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $bahanBakuUuid)->first();
            }
            
            if ($marinadeUuid) {
                $prosesMarinade = ProsesMarinadeModel::where('uuid', $marinadeUuid)->first();
            }
            
            foreach ($request->tumbling_data as $tumblingData) {
                \Log::info('Data yang akan disimpan:', $tumblingData);
                
                $dataToSave = [
                    'id_plan' => Auth::user()->id_plan,
                    'user_id' => Auth::id(),
                    'id_produk' => $request->id_produk,
                    'id_tumbling' => $tumblingData['id_tumbling'],
                    'shift_id' => $request->shift_id,
                    'aktual_drum_on' => $tumblingData['aktual_drum_on'] ?? null,
                    'aktual_drum_off' => $tumblingData['aktual_drum_off'] ?? null,
                    'aktual_speed' => $tumblingData['aktual_speed'] ?? null,
                    'aktual_total_waktu' => $tumblingData['aktual_total_waktu'] ?? null,
                    'aktual_vakum' => $tumblingData['aktual_vakum'] ?? null,
                    'aktual_drum_on_non_vakum' => $tumblingData['aktual_drum_on_non_vakum'] ?? null,
                    'aktual_drum_off_non_vakum' => $tumblingData['aktual_drum_off_non_vakum'] ?? null,
                    'aktual_speed_non_vakum' => $tumblingData['aktual_speed_non_vakum'] ?? null,
                    'aktual_total_waktu_non_vakum' => $tumblingData['aktual_total_waktu_non_vakum'] ?? null,
                    'aktual_tekanan_non_vakum' => $tumblingData['aktual_tekanan_non_vakum'] ?? null,
                    'waktu_mulai_tumbling' => $tumblingData['waktu_mulai_tumbling'] ?? null,
                    'waktu_selesai_tumbling' => $tumblingData['waktu_selesai_tumbling'] ?? null,
                    'waktu_mulai_tumbling_non_vakum' => $tumblingData['waktu_mulai_tumbling_non_vakum'] ?? null,
                    'waktu_selesai_tumbling_non_vakum' => $tumblingData['waktu_selesai_tumbling_non_vakum'] ?? null,
                    'suhu_hasil_tumbling' => $tumblingData['suhu_hasil_tumbling'] ?? null,
                    'sensori_hasil_tumbling' => $tumblingData['sensori_hasil_tumbling'] ?? null,
                    // 'suhu' => $tumblingData['suhu'] ?? null,
                    // 'kondisi' => $tumblingData['kondisi'] ?? null,
                    // 'waktu_mulai_aging' => $tumblingData['waktu_mulai_aging'] ?? null,
                    // 'waktu_selesai_aging' => $tumblingData['waktu_selesai_aging'] ?? null,
                    'kode_produksi' => $request->kode_produksi,
                    'tanggal' => $formattedDate,
                    'jam' => $request->jam,
                ];
                
                // Tambahkan relasi jika ada
                if ($bahanBakuTumbling) {
                    $dataToSave['bahan_baku_tumbling_id'] = $bahanBakuTumbling->id;
                    $dataToSave['bahan_baku_tumbling_uuid'] = $bahanBakuTumbling->uuid;
                }
                
                if ($prosesMarinade) {
                    $dataToSave['proses_marinade_id'] = $prosesMarinade->id;
                    $dataToSave['proses_marinade_uuid'] = $prosesMarinade->uuid;
                }
                
                $prosesTumbling = ProsesTumbling::create($dataToSave);
                
                if ($prosesTumbling) {
                    $savedCount++;
                    \Log::info('Data berhasil disimpan dengan ID: ' . $prosesTumbling->id);
                }
            }

            return redirect()->route('proses-tumbling.index')
                ->with('success', "Berhasil menyimpan {$savedCount} data proses tumbling.");
        } catch (\Exception $e) {
            \Log::error('Error menyimpan data proses tumbling: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    public function show($uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('qc-sistem.proses_tumbling.show', compact('prosesTumbling'));
    }
    public function edit($uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.proses_tumbling.edit', compact('prosesTumbling', 'produks', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_tumbling' => 'required|exists:data_tumbling,id',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_produksi' => 'required|string',
            'tanggal' => 'required|string', // Ubah validasi
            'suhu_hasil_tumbling' => 'nullable|string',
            'sensori_hasil_tumbling' => 'nullable|in:oke,tidak ok',
        ]);

        // Format tanggal sebelum update
        $formattedDate = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

        $prosesTumbling->update([
            'id_produk' => $request->id_produk,
            'id_tumbling' => $request->id_tumbling,
            'shift_id' => $request->shift_id,
            'aktual_drum_on' => $request->aktual_drum_on,
            'aktual_drum_off' => $request->aktual_drum_off,
            'aktual_speed' => $request->aktual_speed,
            'aktual_total_waktu' => $request->aktual_total_waktu,
            'aktual_vakum' => $request->aktual_vakum,
            'aktual_drum_on_non_vakum' => $request->aktual_drum_on_non_vakum,
            'aktual_drum_off_non_vakum' => $request->aktual_drum_off_non_vakum,
            'aktual_speed_non_vakum' => $request->aktual_speed_non_vakum,
            'aktual_total_waktu_non_vakum' => $request->aktual_total_waktu_non_vakum,
            'aktual_tekanan_non_vakum' => $request->aktual_tekanan_non_vakum,
            'waktu_mulai_tumbling_non_vakum' => $request->waktu_mulai_tumbling_non_vakum,
            'waktu_selesai_tumbling_non_vakum' => $request->waktu_selesai_tumbling_non_vakum,
            'waktu_mulai_tumbling' => $request->waktu_mulai_tumbling,
            'waktu_selesai_tumbling' => $request->waktu_selesai_tumbling,
            'suhu_hasil_tumbling' => $request->suhu_hasil_tumbling,
            'sensori_hasil_tumbling' => $request->sensori_hasil_tumbling,
            // 'suhu' => $request->suhu,
            // 'kondisi' => $request->kondisi,
            // 'waktu_mulai_aging' => $request->waktu_mulai_aging,
            // 'waktu_selesai_aging' => $request->waktu_selesai_aging,
            'kode_produksi' => $request->kode_produksi,
            'tanggal' => $formattedDate, // Gunakan tanggal yang sudah diformat
        ]);

        return redirect()->route('proses-tumbling.index')->with('success', 'Data proses tumbling berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $isReferenced = ProsesAging::where('proses_tumbling_uuid', $uuid)->exists()
            || ProsesAging::where('proses_tumbling_id', $prosesTumbling->id)->exists();

        if ($isReferenced) {
            return redirect()->route('proses-tumbling.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $prosesTumbling->delete();

        return redirect()->route('proses-tumbling.index')->with('success', 'Data proses tumbling berhasil dihapus.');
    }

    // AJAX method to get tumbling data by product
    public function getTumblingByProduct($productId)
    {
        $user = Auth::user();
        $product = JenisProduk::find($productId);

        if ($user->role !== 'superadmin' && (!$product || $product->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $tumblingData = DataTumbling::where('id_produk', $productId)->get();
        return response()->json($tumblingData);
    }

    /**
     * Tampilkan halaman logs untuk Proses Tumbling
     */
    public function showLogs($uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = ProsesTumblingLog::with('user')
            ->where('proses_tumbling_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('qc-sistem.proses_tumbling.logs', compact('prosesTumbling', 'logs'));
    }

    /**
     * API untuk DataTables logs Proses Tumbling
     */
    public function getLogsJson($uuid)
    {
        $prosesTumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesTumbling->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ProsesTumblingLog::with('user')
            ->where('proses_tumbling_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d-m-Y H:i:s'),
                    'user' => $log->user->name ?? 'System',
                    'role' => $log->user->role ?? '-',
                    'field_yang_diubah' => $log->nama_field,
                    'deskripsi_perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address,
                ];
            })
        ]);
    }
}
