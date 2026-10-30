<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dynamic dashboard based on the logged-in user's role.
     */
    /**
     * Set ke true untuk menyembunyikan dashboard baru (inspector & management)
     * sementara data belum real. Semua role akan diarahkan ke dashboard standar.
     * Set ke false untuk mengaktifkan kembali dashboard per-role.
     */
    const HIDE_ROLE_DASHBOARDS = true;

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Jika dashboard per-role sedang disembunyikan, arahkan semua ke dashboard standar
        if (self::HIDE_ROLE_DASHBOARDS) {
            return view('super-admin.dashboard');
        }

        // Dapatkan nama role dalam format lowercase untuk pencocokan yang aman
        $role = strtolower($user->role);

        $filter = $request->get('filter', 'today'); // default today
        $selectedProduk = $request->get('id_produk');
        
        // Ambil daftar produk berdasarkan plan user untuk dropdown filter
        $produkList = \App\Models\JenisProduk::where('id_plan', $user->id_plan)
                        ->orderBy('nama_produk')
                        ->get();

        // Gabungan Forming dan Non Forming logic/Mixing Forming & Non Forming
        $queryForming = \App\Models\PersiapanBahanForming::query();
        $queryNonForming = \App\Models\PersiapanBahanNonForming::query();
        // Gabungan emulsi logic
        $querySuhuEmulsi = \App\Models\SuhuEmulsi::query()->whereHas('pembuatanEmulsi');
        // Better logic
        $queryBetter = \App\Models\AktualBetter::query()->with('std');
        // Thumbling logic
        $queryThumbling = \App\Models\ProsesTumbling::query();
        // Pemasakan Nasi logic
        $queryPemasakanNasi = \App\Models\PemasakanNasi::query();
        // Cooking Mixer logic
        $queryCookingMixer = \App\Models\PemeriksaanProdukCookingMixerFla::query();

        // Filter Plan (Kecuali Superadmin yang bisa melihat semua? Tapi biasanya dikunci per plan user)
        if ($user->role !== 'superadmin') {
            $queryForming->where('plan_id', $user->id_plan);
            $queryNonForming->where('plan_id', $user->id_plan);
            $querySuhuEmulsi->whereHas('pembuatanEmulsi', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });
            $queryBetter->whereHas('persiapan', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });
            $queryThumbling->where('id_plan', $user->id_plan);
            $queryPemasakanNasi->where('id_plan', $user->id_plan);
            $queryCookingMixer->where('id_plan', $user->id_plan);
        }

        // Filter Produk jika dipilih
        if ($selectedProduk) {
            $queryForming->whereHas('formula', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
            $queryNonForming->whereHas('formulaNonForming', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
            $querySuhuEmulsi->whereHas('pembuatanEmulsi', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
            $queryBetter->whereHas('persiapan', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
            $queryThumbling->where('id_produk', $selectedProduk);
            $queryPemasakanNasi->where('id_produk', $selectedProduk);
            $queryCookingMixer->whereHas('namaFormulaFla', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
        }

        if ($filter == 'today') {
            $today = \Carbon\Carbon::today();
            $queryForming->whereDate('tanggal', $today);
            $queryNonForming->whereDate('tanggal', $today);
            $querySuhuEmulsi->whereHas('pembuatanEmulsi', function($q) use ($today) {
                $q->whereDate('tanggal', $today);
            });
            $queryBetter->whereHas('persiapan', function($q) use ($today) {
                $q->whereDate('tanggal', $today);
            });
            $queryThumbling->whereDate('tanggal', $today);
            $queryPemasakanNasi->whereDate('tanggal', $today);
            $queryCookingMixer->whereDate('tanggal', $today);
        } elseif ($filter == 'month') {
            $month = date('m');
            $year = date('Y');
            $queryForming->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryNonForming->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $querySuhuEmulsi->whereHas('pembuatanEmulsi', function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            });
            $queryBetter->whereHas('persiapan', function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            });
            $queryThumbling->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryPemasakanNasi->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryCookingMixer->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        } elseif ($filter == 'custom') {
            $monthDate = $request->get('month_date', date('Y-m')); // Format: YYYY-MM
            $dateParts = explode('-', $monthDate);
            $year = $dateParts[0] ?? date('Y');
            $month = $dateParts[1] ?? date('m');
            
            $queryForming->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryNonForming->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $querySuhuEmulsi->whereHas('pembuatanEmulsi', function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            });
            $queryBetter->whereHas('persiapan', function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            });
            $queryThumbling->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryPemasakanNasi->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $queryCookingMixer->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        }

        // 1. Hitung Mixing (Forming & Non Forming)
        $totalForming = $queryForming->count();
        $okForming = (clone $queryForming)->where('kondisi', '✔')->count();
        
        $totalNonForming = $queryNonForming->count();
        $okNonForming = (clone $queryNonForming)->where('kondisi', '✔')->count();

        $totalMixing = $totalForming + $totalNonForming;
        $totalMixingOk = $okForming + $okNonForming;

        $persentaseMixing = $totalMixing > 0 ? round(($totalMixingOk / $totalMixing) * 100) : 0;

        // 2. Hitung Emulsi
        $totalEmulsi = $querySuhuEmulsi->count();
        $okEmulsi = (clone $querySuhuEmulsi)->where('suhu', '✔')->count();

        $persentaseEmulsi = $totalEmulsi > 0 ? round(($okEmulsi / $totalEmulsi) * 100) : 0;

        // 3. Hitung Better (Batter)
        $aktualBetters = $queryBetter->get();
        $totalBetter = $aktualBetters->count();
        $okBetter = 0;

        foreach ($aktualBetters as $aktual) {
            $std = $aktual->std;
            if (!$std) continue; // Skip jika tidak ada data standar

            // Parse Viskositas
            $stdVisText = str_replace(',', '.', $std->std_viskositas); // jaga2 jika pakai koma
            $stdVisArr = explode('-', $stdVisText);
            $minVis = (float) trim($stdVisArr[0]);
            $maxVis = isset($stdVisArr[1]) ? (float) trim($stdVisArr[1]) : $minVis;

            // Parse Salinity
            $stdSalText = str_replace(',', '.', $std->std_salinitas);
            $stdSalArr = explode('-', $stdSalText);
            $minSal = (float) trim($stdSalArr[0]);
            $maxSal = isset($stdSalArr[1]) ? (float) trim($stdSalArr[1]) : $minSal;

            // Aktual values
            $actVis = (float) str_replace(',', '.', $aktual->aktual_vis);
            $actSal = (float) str_replace(',', '.', $aktual->aktual_sal);
            $actSuhu = (float) str_replace(',', '.', $aktual->aktual_suhu_air);

            // Validasi ketiga kondisi
            $visValid = ($actVis >= $minVis && $actVis <= $maxVis);
            $salValid = ($actSal >= $minSal && $actSal <= $maxSal);
            $suhuValid = ($actSuhu <= 10.0);

            if ($visValid && $salValid && $suhuValid) {
                $okBetter++;
            }
        }
        $persentaseBatter = $totalBetter > 0 ? round(($okBetter / $totalBetter) * 100) : 0;

        // 4. Hitung Thumbling (Tumbling)
        $tumblings = $queryThumbling->get();
        $totalThumbling = $tumblings->count();
        $okThumbling = 0;

        foreach ($tumblings as $tum) {
            $suhuHasil = (float) str_replace(',', '.', (string) $tum->suhu_hasil_tumbling);
            $sensoriText = strtolower(trim((string) $tum->sensori_hasil_tumbling));

            // Validasi: suhu <= 10 dan sensori = 'oke'
            if ($suhuHasil <= 10.0 && $sensoriText === 'oke') {
                $okThumbling++;
            }
        }
        $persentaseThumbling = $totalThumbling > 0 ? round(($okThumbling / $totalThumbling) * 100) : 0;

        // 5. Hitung Pemasakan Nasi (sensori_kondisi == 'OK')
        $totalNasi = $queryPemasakanNasi->count();
        $okNasi = (clone $queryPemasakanNasi)->where('sensori_kondisi', 'OK')->count();
        $persentasePemasakNasi = $totalNasi > 0 ? round(($okNasi / $totalNasi) * 100) : 0;

        // 6. Hitung Cooking Mixer (semua 4 organoleptic == 'OK')
        $cookingMixers = $queryCookingMixer->get();
        $totalCookingMixer = $cookingMixers->count();
        $okCookingMixer = 0;

        foreach ($cookingMixers as $cm) {
            // Semua 4 harus OK
            if (
                $cm->organo_warna   === 'OK' &&
                $cm->organo_aroma   === 'OK' &&
                $cm->organo_tekstur === 'OK' &&
                $cm->organo_rasa    === 'OK'
            ) {
                $okCookingMixer++;
            }
        }
        $persentaseCookingMixer = $totalCookingMixer > 0 ? round(($okCookingMixer / $totalCookingMixer) * 100) : 0;

        // --- FETCH CURRENT & PREVIOUS PROCESSING DATA ---
        // 1. Mixing (Forming / Non-Forming gabung, ambil yang terbaru)
        $formingQuery = \App\Models\PersiapanBahanForming::with('formula.produk')->latest('id');
        $nonFormingQuery = \App\Models\PersiapanBahanNonForming::with('formulaNonForming.produk')->latest('id');
        
        if ($user->role !== 'superadmin') {
            $formingQuery->where('plan_id', $user->id_plan);
            $nonFormingQuery->where('plan_id', $user->id_plan);
        }

        if ($selectedProduk) {
            $formingQuery->whereHas('formula', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
            $nonFormingQuery->whereHas('formulaNonForming', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
        }
        
        $forming = $formingQuery->take(5)->get();
        $nonForming = $nonFormingQuery->take(5)->get();
        
        // Gabungkan dan pastikan variabel Nama Produk & Kode Produksi tersedia seragam
        $mixingRecords = collect();
        foreach($forming as $f) {
            $f->display_name = $f->formula->produk->nama_produk ?? 'N/A';
            // INFO: Di Forming, Kode Produksi disimpan di kode_produksi_emulsi
            $f->display_code = $f->kode_produksi_emulsi ?? '-'; 
            $mixingRecords->push($f);
        }
        foreach($nonForming as $nf) {
            $nf->display_name = $nf->formulaNonForming->produk->nama_produk ?? 'N/A';
            // INFO: Di Non-Forming, Kode Produksi disimpan di kode_produksi
            $nf->display_code = $nf->kode_produksi ?? '-'; 
            $mixingRecords->push($nf);
        }

        $mixingRecords = $mixingRecords->sortByDesc('created_at')->values();
        $currentMixing = $mixingRecords->get(0);
        $prevMixing = $mixingRecords->get(1);

        // 2. Battering
        $betterQuery = \App\Models\PersiapanBahanBetter::with('produk')->latest('id');
        if($user->role !== 'superadmin') $betterQuery->where('id_plan', $user->id_plan);
        if($selectedProduk) $betterQuery->where('id_produk', $selectedProduk);
        $betterRecords = $betterQuery->take(2)->get();
        $currentBatter = $betterRecords->get(0);
        $prevBatter = $betterRecords->get(1);

        // 3. Frayer (Hasil Penggorengan)
        $frayerQuery = \App\Models\HasilPenggorengan::with(['produk', 'penggorengan'])->latest('id');
        if($user->role !== 'superadmin') $frayerQuery->where('id_plan', $user->id_plan);
        if($selectedProduk) $frayerQuery->where('id_produk', $selectedProduk);
        $frayerRecords = $frayerQuery->take(2)->get();
        $currentFrayer = $frayerRecords->get(0);
        $prevFrayer = $frayerRecords->get(1);

        // 4. Roasting (Hasil Roasting)
        $roastingQuery = \App\Models\HasilProsesRoasting::with(['produk', 'inputRoasting'])->latest('id');
        if($user->role !== 'superadmin') $roastingQuery->where('id_plan', $user->id_plan);
        if($selectedProduk) $roastingQuery->where('id_produk', $selectedProduk);
        $roastingRecords = $roastingQuery->take(2)->get();
        $currentRoasting = $roastingRecords->get(0);
        $prevRoasting = $roastingRecords->get(1);

        // 5. Packing (Pengemasan Plastik)
        $packingQuery = \App\Models\PengemasanPlastik::with('pengemasanProduk.produk')->latest('id');
        if($user->role !== 'superadmin') $packingQuery->where('id_plan', $user->id_plan);
        if($selectedProduk) {
            $packingQuery->whereHas('pengemasanProduk', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
        }
        $packingRecords = $packingQuery->take(2)->get();
        $currentPacking = $packingRecords->get(0);
        $prevPacking = $packingRecords->get(1);

        // 6. Cartoning (Pengemasan Karton)
        $cartoningQuery = \App\Models\PengemasanKarton::with('pengemasanProduk.produk')->latest('id');
        if($user->role !== 'superadmin') $cartoningQuery->where('id_plan', $user->id_plan);
        if($selectedProduk) {
            $cartoningQuery->whereHas('pengemasanProduk', function($q) use ($selectedProduk) {
                $q->where('id_produk', $selectedProduk);
            });
        }
        $cartoningRecords = $cartoningQuery->take(2)->get();
        $currentCartoning = $cartoningRecords->get(0);
        $prevCartoning = $cartoningRecords->get(1);

        // Data array untuk dilempar ke view
        $data = [
            'filter' => $filter,
            'produkList' => $produkList,
            'selectedProduk' => $selectedProduk,
            'persentaseMixing' => $persentaseMixing,
            'persentaseEmulsi' => $persentaseEmulsi,
            'persentaseBatter' => $persentaseBatter, 
            'persentaseThumbling' => $persentaseThumbling,
            'persentasePemasakNasi' => $persentasePemasakNasi,
            'persentaseCookingMixer' => $persentaseCookingMixer,
            
            // Real-time processing data
            'currentMixing' => $currentMixing,
            'prevMixing'    => $prevMixing,
            'currentBatter' => $currentBatter,
            'prevBatter'    => $prevBatter,
            'currentFrayer' => $currentFrayer,
            'prevFrayer'    => $prevFrayer,
            'currentRoasting' => $currentRoasting,
            'prevRoasting'    => $prevRoasting,
            'currentPacking'  => $currentPacking,
            'prevPacking'     => $prevPacking,
            'currentCartoning' => $currentCartoning,
            'prevCartoning'    => $prevCartoning,
        ];

        // Group 1: SPV QC, Admin, Superadmin (Tampilan Gambar 1)
        if (in_array($role, ['spv qc', 'spv_qc', 'spv', 'admin', 'superadmin'])) {
            return view('dashboard.management', $data);
        }

        // Group 2: QC Inspector, Produksi (Tampilan Gambar 2)
        if (in_array($role, ['qc inspector', 'qc_inspector', 'inspector', 'produksi', 'production'])) {
            return view('dashboard.inspector', $data);
        }

        // Fallback default ke dashboard standard lama jika role tidak terdefinisi
        return view('super-admin.dashboard');
    }
}
