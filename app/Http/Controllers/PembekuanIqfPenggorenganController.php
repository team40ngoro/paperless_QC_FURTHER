<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PembekuanIqfPenggorengan;
use App\Models\PembekuanIqfPenggorenganLog;
use App\Models\HasilPenggorengan;
use App\Models\ProsesFrayer;
use App\Models\Frayer2;
use App\Models\Frayer3;
use App\Models\Frayer4;
use App\Models\Frayer5;
use App\Models\ProsesBreader;
use App\Models\ProsesBattering;
use App\Models\PembuatanPredust;
use App\Models\Penggorengan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PembekuanIqfPenggorenganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $query = PembekuanIqfPenggorengan::with([
            'plan', 'user',
            'hasilPenggorenganData',
            'frayerData', 'frayer2Data',
            'breaderData', 'batteringData',
            'predustData', 'penggorenganData.produk','penggorenganData.shift',
            'qcApprover', 'produksiApprover', 'spvApprover'
        ]);

        $query->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        $search = request('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penggorenganData.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('hasilPenggorenganData.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('frayerData.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('frayer2Data.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('breaderData.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('batteringData.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('predustData.penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                });
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('qc-sistem.pembekuan_iqf_penggorengan.index', compact('data', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get UUIDs from query parameters
        $hasilPenggorenganUuid = request('hasil_penggorengan_uuid');
        $frayerUuid = request('frayer_uuid');
        $frayer2Uuid = request('frayer2_uuid');
        $breaderUuid = request('breader_uuid');
        $batteringUuid = request('battering_uuid');
        $predustUuid = request('predust_uuid');
        $penggorenganUuid = request('penggorengan_uuid');

        // Initialize data variables
        $hasilPenggorenganData = null;
        $frayerData = null;
        $frayer2Data = null;
        $breaderData = null;
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;

        // Load hasil penggorengan data
        if ($hasilPenggorenganUuid) {
            $hasilPenggorenganData = HasilPenggorengan::with([
                'plan', 'user', 'produk', 'stdSuhuPusat'
            ])->where('uuid', $hasilPenggorenganUuid)->first();
        }

        // Load frayer2 data and related frayer data
        if ($frayer2Uuid) {
            $frayer2Data = Frayer2::with([
                'plan', 'user', 'produk', 'suhuFrayer2', 'waktuPenggorengan2'
            ])->where('uuid', $frayer2Uuid)->first();
            
            if ($frayer2Data && $frayer2Data->frayer_uuid) {
                $frayerData = ProsesFrayer::with([
                    'plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan'
                ])->where('uuid', $frayer2Data->frayer_uuid)->first();
            }
        }

        // Load frayer data from multiple tables if not loaded from frayer2
        if ($frayerUuid && !$frayerData) {
            // Try Frayer5 first
            $frayerData = Frayer5::with([
                'plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan'
            ])->where('uuid', $frayerUuid)->first();

            // Try Frayer4 if not found
            if (!$frayerData) {
                $frayerData = Frayer4::with([
                    'plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan'
                ])->where('uuid', $frayerUuid)->first();
            }

            // Try Frayer3 if not found
            if (!$frayerData) {
                $frayerData = Frayer3::with([
                    'plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan'
                ])->where('uuid', $frayerUuid)->first();
            }

            // Try Frayer2 if not found
            if (!$frayerData) {
                $frayerData = Frayer2::with([
                    'plan', 'user', 'produk', 'suhuFrayer2', 'waktuPenggorengan2'
                ])->where('uuid', $frayerUuid)->first();
            }

            // Try ProsesFrayer if not found
            if (!$frayerData) {
                $frayerData = ProsesFrayer::with([
                    'plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan'
                ])->where('uuid', $frayerUuid)->first();
            }
        }

        // Load other related data
        if ($breaderUuid) {
            $breaderData = ProsesBreader::with([
                'plan', 'user', 'produk'
            ])->where('uuid', $breaderUuid)->first();
        }

        if ($batteringUuid) {
            $batteringData = ProsesBattering::with([
                'plan', 'user', 'produk'
            ])->where('uuid', $batteringUuid)->first();
        }

        if ($predustUuid) {
            $predustData = PembuatanPredust::with([
                'plan', 'user', 'produk'
            ])->where('uuid', $predustUuid)->first();
        }

        if ($penggorenganUuid) {
            $penggorenganData = Penggorengan::with([
                'plan', 'user', 'produk'
            ])->where('uuid', $penggorenganUuid)->first();
        }

        return view('qc-sistem.pembekuan_iqf_penggorengan.create', compact(
            'hasilPenggorenganData',
            'frayerData',
            'frayer2Data',
            'breaderData',
            'batteringData',
            'predustData',
            'penggorenganData'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'suhu_ruang_iqf' => 'required|string',
            'holding_time' => 'required|string',
            'hasil_penggorengan_uuid' => 'nullable|string|exists:hasil_penggorengan,uuid',
            'frayer2_uuid' => 'nullable|string|exists:frayer_2,uuid',
            'breader_uuid' => 'nullable|string|exists:proses_breader,uuid',
            'battering_uuid' => 'nullable|string|exists:proses_battering,uuid',
            'predust_uuid' => 'nullable|string|exists:pembuatan_predust,uuid',
            'penggorengan_uuid' => 'nullable|string|exists:penggorengan,uuid',
        ]);

        $validated = $request->all();

        // Prepare the data for creation
        $data = [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id, // Auto-fill dari controller
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $validated['jam'],
            'suhu_ruang_iqf' => $validated['suhu_ruang_iqf'],
            'holding_time' => $validated['holding_time'],
        ];

        // Add UUID fields if they exist
        $uuidFields = [
            'hasil_penggorengan_uuid', 'frayer_uuid', 'frayer2_uuid', 
            'breader_uuid', 'battering_uuid', 'predust_uuid', 'penggorengan_uuid'
        ];

        foreach ($uuidFields as $field) {
            if (!empty($validated[$field])) {
                $data[$field] = $validated[$field];
            }
        }

        // Auto-derive relasi proses jika kosong (umumnya terjadi saat skip Frayer1)
        if (!empty($validated['hasil_penggorengan_uuid'])) {
            $hasilPenggorenganData = HasilPenggorengan::where('uuid', $validated['hasil_penggorengan_uuid'])->first();
            if ($hasilPenggorenganData) {
                if (empty($data['penggorengan_uuid']) && !empty($hasilPenggorenganData->penggorengan_uuid)) {
                    $data['penggorengan_uuid'] = $hasilPenggorenganData->penggorengan_uuid;
                }
                if (empty($data['predust_uuid']) && !empty($hasilPenggorenganData->predust_uuid)) {
                    $data['predust_uuid'] = $hasilPenggorenganData->predust_uuid;
                }
                if (empty($data['battering_uuid']) && !empty($hasilPenggorenganData->battering_uuid)) {
                    $data['battering_uuid'] = $hasilPenggorenganData->battering_uuid;
                }
                if (empty($data['breader_uuid']) && !empty($hasilPenggorenganData->breader_uuid)) {
                    $data['breader_uuid'] = $hasilPenggorenganData->breader_uuid;
                }
                if (empty($data['frayer2_uuid']) && !empty($hasilPenggorenganData->frayer2_uuid)) {
                    $data['frayer2_uuid'] = $hasilPenggorenganData->frayer2_uuid;
                }
                if (empty($data['frayer_uuid']) && !empty($hasilPenggorenganData->frayer_uuid)) {
                    $data['frayer_uuid'] = $hasilPenggorenganData->frayer_uuid;
                }
            }
        }

        if (!empty($data['frayer2_uuid'])) {
            $frayer2Data = Frayer2::where('uuid', $data['frayer2_uuid'])->first();
            if ($frayer2Data) {
                if (empty($data['penggorengan_uuid']) && !empty($frayer2Data->penggorengan_uuid)) {
                    $data['penggorengan_uuid'] = $frayer2Data->penggorengan_uuid;
                }
                if (empty($data['predust_uuid']) && !empty($frayer2Data->predust_uuid)) {
                    $data['predust_uuid'] = $frayer2Data->predust_uuid;
                }
                if (empty($data['battering_uuid']) && !empty($frayer2Data->battering_uuid)) {
                    $data['battering_uuid'] = $frayer2Data->battering_uuid;
                }
                if (empty($data['breader_uuid']) && !empty($frayer2Data->breader_uuid)) {
                    $data['breader_uuid'] = $frayer2Data->breader_uuid;
                }
            }
        }

        PembekuanIqfPenggorengan::create($data);

        return redirect()->route('pembekuan-iqf-penggorengan.index')
                        ->with('success', 'Data pembekuan IQF penggorengan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = PembekuanIqfPenggorengan::with([
            'plan', 'user',
            'hasilPenggorenganData',
            'frayerData', 'frayer2Data',
            'breaderData', 'batteringData',
            'predustData', 'penggorenganData'
        ])->where('uuid', $uuid)->firstOrFail();

        return view('qc-sistem.pembekuan_iqf_penggorengan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();
        
        return view('qc-sistem.pembekuan_iqf_penggorengan.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $data = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'suhu_ruang_iqf' => 'required|string',
            'holding_time' => 'required|string',
        ]);

        $validated = $request->all();

        $updateData = [
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $validated['jam'],
            'suhu_ruang_iqf' => $validated['suhu_ruang_iqf'],
            'holding_time' => $validated['holding_time'],
        ];

        $data->update($updateData);

        return redirect()->route('pembekuan-iqf-penggorengan.index')
                        ->with('success', 'Data pembekuan IQF penggorengan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();
        $data->delete();

        return redirect()->route('pembekuan-iqf-penggorengan.index')
                        ->with('success', 'Data pembekuan IQF penggorengan berhasil dihapus.');
    }

    // Logs Methods
    public function showLogs($uuid)
    {
        $user = Auth::user();
        $pembekuanIqfPenggorengan = PembekuanIqfPenggorengan::with(['plan', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $pembekuanIqfPenggorengan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.pembekuan_iqf_penggorengan.logs', compact('pembekuanIqfPenggorengan'));
    }

    public function getLogsJson($uuid)
    {
        $user = Auth::user();
        $pembekuanIqfPenggorengan = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $pembekuanIqfPenggorengan->id_plan != $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PembekuanIqfPenggorenganLog::with('user')
            ->where('pembekuan_iqf_penggorengan_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * Approve pembekuan IQF penggorengan data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $user = auth()->user();
            $data = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();
            $type = $request->input('type');

            // Role-based approval validation
            $userRole = $user->id_role ?? null;
            if (!in_array($userRole, [1, 2, 3, 4, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui data ini.'
                ], 403);
            }

            // Type-based approval logic
            switch ($type) {
                case 'qc':
                    // Only roles 1, 3, 5 can approve QC
                    if (!in_array($userRole, [1, 3, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk persetujuan QC.'
                        ], 403);
                    }
                    
                    if ($data->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data sudah disetujui oleh QC.'
                        ], 400);
                    }
                    
                    $data->update([
                        'approved_by_qc' => true,
                        'qc_approved_by' => $user->id,
                        'qc_approved_at' => now()
                    ]);
                    $approvalType = 'QC';
                    break;

                case 'produksi':
                    // Only role 2 can approve Produksi
                    if ($userRole != 2) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk persetujuan Produksi.'
                        ], 403);
                    }
                    
                    if (!$data->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui QC terlebih dahulu.'
                        ], 400);
                    }
                    
                    if ($data->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data sudah disetujui oleh Produksi.'
                        ], 400);
                    }
                    
                    $data->update([
                        'approved_by_produksi' => true,
                        'produksi_approved_by' => $user->id,
                        'produksi_approved_at' => now()
                    ]);
                    $approvalType = 'Produksi';
                    break;

                case 'spv':
                    // Only role 4 can approve SPV
                    if ($userRole != 4) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk persetujuan SPV.'
                        ], 403);
                    }
                    
                    if (!$data->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui Produksi terlebih dahulu.'
                        ], 400);
                    }
                    
                    if ($data->approved_by_spv) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data sudah disetujui oleh SPV.'
                        ], 400);
                    }
                    
                    $data->update([
                        'approved_by_spv' => true,
                        'spv_approved_by' => $user->id,
                        'spv_approved_at' => now()
                    ]);
                    $approvalType = 'SPV';
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipe persetujuan tidak valid.'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Data berhasil disetujui oleh {$approvalType}.",
                'approval_type' => $approvalType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF with filters for IQF Penggorengan Process Flow
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'nullable|date',
                'id_produk' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = PembekuanIqfPenggorengan::with([
                    'plan', 'user',
                    'hasilPenggorenganData',
                    'frayerData',
                    'frayerData.suhuFrayer',
                    'frayerData.waktuPenggorengan',
                    'frayer2Data',
                    'frayer2Data.suhuFrayer2',
                    'frayer2Data.waktuPenggorengan2',
                    'frayer3Data',
                    'frayer3Data.suhuFrayer',
                    'frayer3Data.waktuPenggorengan',
                    'frayer4Data',
                    'frayer4Data.suhuFrayer',
                    'frayer4Data.waktuPenggorengan',
                    'frayer5Data',
                    'frayer5Data.suhuFrayer',
                    'frayer5Data.waktuPenggorengan',
                    'breaderData.jenisBreader',
                    'batteringData.jenis_better', 
                    'batteringData.produk',
                    'predustData', 
                    'penggorenganData'
            ])->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            // Filter by product through penggorengan relationship
            if ($request->id_produk) {
                $query->whereHas('penggorenganData', function($q) use ($request) {
                    $q->where('id_produk', $request->id_produk);
                });
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal)->format('d-m-Y');
                }
                if ($request->id_produk) {
                    $produk = JenisProduk::find($request->id_produk);
                    $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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

            // Generate PDF with data
            $pdf = Pdf::loadView('qc-sistem.pembekuan_iqf_penggorengan.export_pdf', compact('data', 'request'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'pembekuan_iqf_penggorengan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
