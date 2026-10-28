<?php

namespace App\Http\Controllers;

use App\Models\ProsesBreader;
use App\Models\ProsesBreaderLog;
use App\Models\JenisProduk;
use App\Models\JenisBreader;
use App\Models\DataShift;
use App\Models\ProsesFrayer;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProsesBreaderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ProsesBreader::with(['produk', 'user', 'plan', 'jenisBreader', 'penggorengan.shift']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('qc-sistem.proses_breader.index', compact('data'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get related data based on UUID parameters
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;
        
        if ($request->has('battering_uuid')) {
            $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift', 'predust.penggorengan'])
                ->where('uuid', $request->battering_uuid)
                ->first();
            
            if ($batteringData) {
                // Cek apakah ada predust (flow lengkap)
                $predustData = $batteringData->predust;
                if ($predustData) {
                    $penggorenganData = $predustData->penggorengan;
                } else {
                    // Jika tidak ada predust, ambil penggorengan langsung dari battering (flow langsung)
                    if ($batteringData->penggorengan_uuid) {
                        $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])
                            ->where('uuid', $batteringData->penggorengan_uuid)
                            ->first();
                    }
                }
            }
        }
        
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $jenis_breader = JenisBreader::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $jenis_breader = JenisBreader::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.proses_breader.create', compact('produks', 'jenis_breader', 'batteringData', 'predustData', 'penggorenganData'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            //'id_jenis_breader' => 'required|exists:jenis_breader,id',
            'kode_produksi' => 'required|string',
            'hasil_breader' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
        ]);
        // Gabungkan id_jenis_breader menjadi string dengan koma jika array
$jenisBreader = is_array($request->id_jenis_breader) ? 
    implode(',', $request->id_jenis_breader) : 
    $request->id_jenis_breader;
        $data=[  
            'uuid' => \Str::uuid(),
            'id_produk' => $request->id_produk,
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'id_jenis_breader' => $jenisBreader,
            'kode_produksi' => $request->kode_produksi,
            'hasil_breader' => $request->hasil_breader,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $request->jam,
            'battering_uuid' => $request->battering_uuid ?: null,
            'predust_uuid' => $request->predust_uuid ?: null,
            'penggorengan_uuid' => $request->penggorengan_uuid ?: null,
        ];
      //  dd($data);

        ProsesBreader::create($data);

        return redirect()->route('proses-breader.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = ProsesBreader::where('uuid', $uuid)->firstOrFail();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $jenis_breader = JenisBreader::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $jenis_breader = JenisBreader::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.proses_breader.edit', compact('item', 'produks', 'jenis_breader'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
           'id_jenis_breader' => 'required|array',
           'id_jenis_breader.*' => 'exists:jenis_breader,id',
            'kode_produksi' => 'required|string',
            'hasil_breader' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
        ]);
 // Gabungkan id_jenis_breader menjadi string dengan koma jika array
    $jenisBreader = is_array($request->id_jenis_breader) ? 
        implode(',', $request->id_jenis_breader) : 
        $request->id_jenis_breader;
        $item = ProsesBreader::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'id_produk' => $request->id_produk,
            'id_jenis_breader' => $jenisBreader,
            'kode_produksi' => $request->kode_produksi,
            'hasil_breader' => $request->hasil_breader,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('proses-breader.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = ProsesBreader::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $isReferenced = ProsesFrayer::where('breader_uuid', $uuid)->exists()
            || HasilPenggorengan::where('breader_uuid', $uuid)->exists()
            || PembekuanIqfPenggorengan::where('breader_uuid', $uuid)->exists();

        if ($isReferenced) {
            return redirect()->route('proses-breader.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $item->delete();
        return redirect()->route('proses-breader.index')->with('success', 'Data berhasil dihapus');
    }

    public function getJenisBreaderByProduk($id_produk)
    {
        $user = auth()->user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $data = JenisBreader::where('id_produk', $id_produk)->get(['id', 'jenis_breader']);
        return response()->json($data);
    }

    /**
     * Show logs for a specific Proses Breader record
     */
    public function showLogs($uuid)
    {
        $item = ProsesBreader::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = ProsesBreaderLog::where('proses_breader_uuid', $uuid)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('qc-sistem.proses_breader.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $item = ProsesBreader::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ProsesBreaderLog::where('proses_breader_uuid', $uuid)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'user_name' => $log->user->name ?? 'System',
                    'user_role' => $log->user_role ?? 'N/A',
                    'nama_field' => $log->nama_field,
                    'field_yang_diubah' => $log->field_yang_diubah,
                    'nilai_lama' => $log->nilai_lama,
                    'nilai_baru' => $log->nilai_baru,
                    'ip_address' => $log->ip_address
                ];
            })
        ]);
    }
}
