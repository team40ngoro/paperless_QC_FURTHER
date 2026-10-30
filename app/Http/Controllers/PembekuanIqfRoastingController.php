<?php

namespace App\Http\Controllers;

use App\Models\PembekuanIqfRoasting;
use App\Models\Plan;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembekuanIqfRoastingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'plan', 
            'user', 
            'shift',
            'penggorengan.produk',
            'inputRoasting.produk', 
            'hasilProsesRoasting.produk',
            'prosesRoastingFan.produk',
            'frayer.produk',
            'breader.produk',
            'battering.produk',
            'predust.produk',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ]);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('penggorengan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('inputRoasting.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('hasilProsesRoasting.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('prosesRoastingFan.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('frayer.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('breader.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('battering.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })
                ->orWhereHas('predust.produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                });
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $pembekuanIqfRoastings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view('qc-sistem.pembekuan_iqf_roasting.index', compact('pembekuanIqfRoastings', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Debug sudah tidak diperlukan lagi
        
        // Get related data from query parameters
        $hasilProsesRoastingUuid = $request->query('hasil_proses_roasting_uuid');
        $inputRoastingUuid = $request->query('input_roasting_uuid');
        $prosesRoastingFanUuid = $request->query('proses_roasting_fan_uuid');
        $frayerUuid = $request->query('frayer_uuid');
        $breaderUuid = $request->query('breader_uuid');
        $batteringUuid = $request->query('battering_uuid');
        $predustUuid = $request->query('predust_uuid');
        $penggorenganUuid = $request->query('penggorengan_uuid');
        
        // Load related models
        $hasilRoastingData = null;
        $inputRoastingData = null;
        $roastingFanData = null;
        $frayerData = null;
        $breaderData = null;
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;
        
        // Load input roasting data
        if ($inputRoastingUuid) {
            $inputRoastingData = \App\Models\InputRoasting::with(['produk', 'shift'])->where('uuid', $inputRoastingUuid)->first();
        }
        
        // Start with hasil proses roasting data
        if ($hasilProsesRoastingUuid) {
            $hasilRoastingData = \App\Models\HasilProsesRoasting::with(['produk', 'shift', 'user'])->where('uuid', $hasilProsesRoastingUuid)->first();
            
            // If hasil proses roasting exists, get the complete chain
            if ($hasilRoastingData) {
                // Get proses roasting fan data
                if ($hasilRoastingData->proses_roasting_fan_uuid) {
                    $roastingFanData = \App\Models\ProsesRoastingFan::with(['produk', 'shift'])->where('uuid', $hasilRoastingData->proses_roasting_fan_uuid)->first();
                }
                
                // Get frayer data
                if ($hasilRoastingData->frayer_uuid) {
                    $frayerData = \App\Models\ProsesFrayer::with(['produk'])->where('uuid', $hasilRoastingData->frayer_uuid)->first();
                }
                
                // Get breader data
                if ($hasilRoastingData->breader_uuid) {
                    $breaderData = \App\Models\ProsesBreader::with(['produk'])->where('uuid', $hasilRoastingData->breader_uuid)->first();
                }
                
                // Get battering data
                if ($hasilRoastingData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', $hasilRoastingData->battering_uuid)->first();
                }
                
                // Get predust data
                if ($hasilRoastingData->predust_uuid) {
                    $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $hasilRoastingData->predust_uuid)->first();
                }
                
                // Get penggorengan data
                if ($hasilRoastingData->penggorengan_uuid) {
                    $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $hasilRoastingData->penggorengan_uuid)->first();
                }
            }
        }
        
        // Fallback: if no hasil proses roasting data, try to load from individual UUIDs
        if (!$hasilRoastingData) {
            if ($prosesRoastingFanUuid) {
                $roastingFanData = \App\Models\ProsesRoastingFan::with(['produk', 'shift', 'user'])->where('uuid', $prosesRoastingFanUuid)->first();
            }
            
            if ($frayerUuid) {
                $frayerData = \App\Models\ProsesFrayer::with(['produk', 'shift'])->where('uuid', $frayerUuid)->first();
            }
            
            if ($breaderUuid) {
                $breaderData = \App\Models\ProsesBreader::with(['produk', 'shift'])->where('uuid', $breaderUuid)->first();
            }
            
            if ($batteringUuid) {
                $batteringData = \App\Models\ProsesBattering::with(['produk', 'shift'])->where('uuid', $batteringUuid)->first();
            }
            
            if ($predustUuid) {
                $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $predustUuid)->first();
            }
            
            if ($penggorenganUuid) {
                $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $penggorenganUuid)->first();
            }
        }
        
        // Get dropdown data based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            // $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        // Set default selected shift (can be null)
        $selectedShift = null;
        

        return view('qc-sistem.pembekuan_iqf_roasting.create', compact(
            'plans', 
            'hasilProsesRoastingUuid',
            'inputRoastingUuid',
            'prosesRoastingFanUuid',
            'frayerUuid',
            'breaderUuid',
            'batteringUuid',
            'predustUuid',
            'penggorenganUuid',
            'hasilRoastingData',
            'inputRoastingData',
            'frayerData',
            'breaderData',
            'batteringData',
            'predustData',
            'penggorenganData',
            'roastingFanData'
        ));
    }

    public function store(Request $request)
    {
        // Auto-detect shift berdasarkan alur proses
        $shift_id = null;

        if ($request->penggorengan_uuid) {
            // Kondisi 1: Alur Penggorengan - ambil shift dari penggorengan
            $penggorengan = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();
            if ($penggorengan) {
                $shift_id = $penggorengan->shift_id;
            }
        } elseif ($request->hasil_proses_roasting_uuid) {
            // Kondisi 2: Alur Roasting - ambil shift dari hasil_proses_roasting
            $hasilRoasting = \App\Models\HasilProsesRoasting::where('uuid', $request->hasil_proses_roasting_uuid)->first();
            if ($hasilRoasting) {
                $shift_id = $hasilRoasting->id_shift;
            }
        }

        if (!$shift_id) {
            return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari proses sebelumnya'])->withInput();
        }
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'suhu_ruang_iqf' => 'required|string|max:255',
            'holding_time' => 'required|string|max:255',
        ]);
        
        $data = $request->all();
        $data['uuid'] = \Illuminate\Support\Str::uuid()->toString();
        $data['user_id'] = Auth::id();
        $data['shift_id'] = $shift_id;
        $data['id_plan'] = Auth::user()->id_plan;
        $data['tanggal'] = Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');
        $data['jam'] = $request->jam; 
        // Add UUID fields from request (following HasilProsesRoastingController pattern)
        $data['hasil_proses_roasting_uuid'] = $request->hasil_proses_roasting_uuid;
        $data['proses_roasting_fan_uuid'] = $request->proses_roasting_fan_uuid;
        $data['input_roasting_uuid'] = $request->input_roasting_uuid;
        $data['frayer_uuid'] = $request->frayer_uuid;
        $data['breader_uuid'] = $request->breader_uuid;
        $data['battering_uuid'] = $request->battering_uuid;
        $data['predust_uuid'] = $request->predust_uuid;
        $data['penggorengan_uuid'] = $request->penggorengan_uuid;


        PembekuanIqfRoasting::create($data);

        return redirect()->route('pembekuan-iqf-roasting.index')->with('success', 'Data pembekuan IQF roasting berhasil ditambahkan!');
    }

    public function show($uuid)
    {
        $pembekuanIqfRoasting = PembekuanIqfRoasting::where('uuid', $uuid)->with(['plan', 'user', 'shift'])->firstOrFail();
        return view('qc-sistem.pembekuan_iqf_roasting.show', compact('pembekuanIqfRoasting'));
    }

    public function edit($uuid)
    {
        $pembekuanIqfRoasting = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            // $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.pembekuan_iqf_roasting.edit', compact('pembekuanIqfRoasting', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $pembekuanIqfRoasting = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
        
        // TAMBAHKAN: Auto-detect shift berdasarkan alur proses
        $shift_id = null;

        if ($request->penggorengan_uuid) {
            // Kondisi 1: Alur Penggorengan - ambil shift dari penggorengan
            $penggorengan = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();
            if ($penggorengan) {
                $shift_id = $penggorengan->shift_id;
            }
        } elseif ($request->hasil_proses_roasting_uuid) {
            // Kondisi 2: Alur Roasting - ambil shift dari hasil_proses_roasting
            $hasilRoasting = \App\Models\HasilProsesRoasting::where('uuid', $request->hasil_proses_roasting_uuid)->first();
            if ($hasilRoasting) {
                $shift_id = $hasilRoasting->id_shift;
            }
        }

        if (!$shift_id) {
            return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari proses sebelumnya'])->withInput();
        }
        
        // UBAH: Validasi tanpa shift_id
        $request->validate([
            'tanggal' => 'required|date',
            'suhu_ruang_iqf' => 'required|string|max:255',
            'holding_time' => 'required|string|max:255',
        ]);

        // UBAH: Gunakan $shift_id yang sudah didapat
        $data = [
            'shift_id' => $shift_id,  // ← GANTI DARI $request->shift_id
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
            'suhu_ruang_iqf' => $request->suhu_ruang_iqf,
            'holding_time' => $request->holding_time,
            // UUID fields from request
            'hasil_proses_roasting_uuid' => $request->hasil_proses_roasting_uuid,
            'proses_roasting_fan_uuid' => $request->proses_roasting_fan_uuid,
            'frayer_uuid' => $request->frayer_uuid,
            'breader_uuid' => $request->breader_uuid,
            'battering_uuid' => $request->battering_uuid,
            'predust_uuid' => $request->predust_uuid,
            'penggorengan_uuid' => $request->penggorengan_uuid,
        ];

        $pembekuanIqfRoasting->update($data);

        return redirect()->route('pembekuan-iqf-roasting.index')->with('success', 'Data pembekuan IQF roasting berhasil diperbarui!');
    }

    public function destroy($uuid)
    {
        $pembekuanIqfRoasting = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
        $pembekuanIqfRoasting->delete();

        return redirect()->route('pembekuan-iqf-roasting.index')->with('success', 'Data pembekuan IQF roasting berhasil dihapus!');
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'nullable|date',
                'id_produk' => 'nullable|integer',
                'flow_type' => 'nullable|in:penggorengan,input_roasting',
                'shift' => 'nullable|in:1,2,3',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = PembekuanIqfRoasting::with([
                'plan', 'user',
                'penggorenganData.produk', 'penggorenganData.shift',
                'predustData', 'batteringData', 'breaderData',
                'frayerData', 'frayer2Data', 'frayer3Data', 'frayer4Data', 'frayer5Data',
                'inputRoastingData.produk', 'inputRoastingData.shift',
                'bahanBakuRoastingData', 'prosesRoastingFanData', 'hasilProsesRoastingData'
            ])->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->id_produk) {
                $query->where(function($q) use ($request) {
                    $q->whereHas('penggorenganData', function($subQ) use ($request) {
                        $subQ->where('id_produk', $request->id_produk);
                    })->orWhereHas('inputRoastingData', function($subQ) use ($request) {
                        $subQ->where('id_produk', $request->id_produk);
                    });
                });
            }
            // Filter berdasarkan flow_type yang dipilih user - HARUS PERTAMA
            if ($request->flow_type) {
                if ($request->flow_type === 'penggorengan') {
                    $query->whereNotNull('penggorengan_uuid');
                } elseif ($request->flow_type === 'input_roasting') {
                    $query->where(function($q) {
                        $q->whereNotNull('input_roasting_uuid')
                        ->orWhereNotNull('hasil_proses_roasting_uuid');
                    })->whereNull('penggorengan_uuid'); // TAMBAH INI - pastikan tidak ada penggorengan_uuid
                }
            }

            // Filter berdasarkan shift
            if ($request->shift) {
                $query->where(function($q) use ($request) {
                    $q->whereHas('penggorenganData.shift', function($subQ) use ($request) {
                        $subQ->where('shift', $request->shift);
                    })->orWhereHas('inputRoastingData.shift', function($subQ) use ($request) {
                        $subQ->where('shift', $request->shift);
                    });
                });
            }
            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Tidak Ada Data</title></head><body style="font-family: Arial; text-align: center; padding: 50px;"><h1>Tidak Ada Data Ditemukan</h1><p>Silakan coba dengan filter yang berbeda.</p></body></html>';
                return response($html)->header('Content-Type', 'text/html');
            }

            // Tentukan template berdasarkan flow_type yang dipilih
            $template = 'export_pdf_penggorengan'; // default

            if ($request->flow_type) {
                // User memilih kondisi spesifik
                if ($request->flow_type === 'penggorengan') {
                    $template = 'export_pdf_penggorengan';
                } elseif ($request->flow_type === 'input_roasting') {
                    $template = 'export_pdf_input_roasting';
                }
            } else {
                // Auto-detect berdasarkan data yang ada
                foreach ($data as $item) {
                    if ($item->penggorengan_uuid) {
                        $template = 'export_pdf_penggorengan';
                        break;
                    } elseif ($item->input_roasting_uuid || $item->hasil_proses_roasting_uuid) {
                        $template = 'export_pdf_input_roasting';
                        break;
                    }
                }
            }
            // Generate PDF dengan template yang sesuai
            $pdf = \PDF::loadView("qc-sistem.pembekuan_iqf_roasting.{$template}", compact('data', 'request'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'pembekuan_iqf_roasting_' . ($request->flow_type ?? 'all') . '_' . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pembekuanIqfRoasting = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukForming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (all buttons), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (all buttons), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (all buttons), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$pembekuanIqfRoasting->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pembekuanIqfRoasting->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pembekuanIqfRoasting->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pembekuanIqfRoasting->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }
}
