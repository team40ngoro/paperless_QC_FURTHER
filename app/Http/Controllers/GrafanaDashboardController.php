<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\VerifPeralatan;
use App\Models\VerifPeralatanDetail;
use App\Models\VerifPeralatanBagian;
use App\Exports\AllProcessExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class GrafanaDashboardController extends Controller
{
    /**
     * Display Grafana Dashboard with dynamic iframe panel IDs & filter dropdowns.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Dapatkan active role ID & id_plan user yang sedang login
        $activeRole = $user->id_role ?? $user->role;
        $activeRoleId = (int) ($user->id_role ?? (is_numeric($user->role) ? $user->role : 0));
        $idPlan = $user->id_plan ?? 1;

        // List semua panel ID default (1 s/d 12)
        $panelIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,13,14,15];

        // Aturan per Role:
        // Role 2 & Role 3:
        // - Sembunyikan panel ID 9, 10, 11, 12
        // - Dropdown pilihan plant HANYA sesuai plant user yang login
        if (in_array($activeRoleId, [2, 3])) {
            $excludedPanels = [9, 10, 11, 12,13,14,15];
            $panelIds = array_values(array_diff($panelIds, $excludedPanels));

            // Hanya ambil plant milik user yang sedang login
            $plans = Plan::where('id', $idPlan)->get();
            if ($plans->isEmpty()) {
                $plans = Plan::all();
            }
        } else {
            // Role 1 & Role 4 (dan role lainnya):
            // - Tampilkan semua panel (1-12)
            // - Tampilkan semua opsi plant
            $plans = Plan::all();
        }

        // Ambil data JenisProduk dari database
        // Role 2 & 3: hanya produk milik plant user yang sedang login
        // Role 1 & 4: semua produk
        if (in_array($activeRoleId, [2, 3])) {
            $produks = JenisProduk::where('id_plan', $idPlan)->orderBy('nama_produk')->get();
        } else {
            $produks = JenisProduk::orderBy('nama_produk')->get();
        }

        // Mengembalikan view super-admin.dashboard-grafana
        return view('super-admin.dashboard-grafana', compact('panelIds', 'activeRole', 'activeRoleId', 'idPlan', 'plans', 'produks'));
    }

    /**
     * Get manual sanitation / verifikasi peralatan metrics via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSanitationData(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reqPlan  = $request->input('id_plan');
        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        $userPlan = $user->id_plan ?? 1;
        $idPlan = (!empty($reqPlan) && $reqPlan !== '$__all' && $reqPlan !== 'all') ? $reqPlan : null;

        $activeRoleId = (int) ($user->id_role ?? (is_numeric($user->role) ? $user->role : 0));
        if (in_array($activeRoleId, [2, 3]) && !$idPlan) {
            $idPlan = $userPlan;
        }

        $headerQuery = VerifPeralatan::query()->operasional();

        if ($idPlan) {
            $headerQuery->where('id_plan', $idPlan);
        }

        if (!empty($fromDate) && !empty($toDate)) {
            $headerQuery->whereBetween('tanggal', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        } elseif (!empty($fromDate)) {
            $headerQuery->whereDate('tanggal', '>=', Carbon::parse($fromDate));
        } elseif (!empty($toDate)) {
            $headerQuery->whereDate('tanggal', '<=', Carbon::parse($toDate));
        }

        $headerIds = $headerQuery->pluck('id');

        if ($headerIds->isEmpty()) {
            return response()->json([
                'total_inspection' => 0,
                'total_nc'         => 0,
                'total_ok'         => 0,
                'top_area'         => [],
                'top_mesin'        => [],
                'top_titik'        => [],
            ]);
        }

        $details = VerifPeralatanDetail::with(['area', 'mesin.area'])
            ->whereIn('verif_peralatan_id', $headerIds)
            ->get();

        $detailIds = $details->pluck('id');

        $bagians = VerifPeralatanBagian::with(['bagian', 'detail.area', 'detail.mesin.area'])
            ->whereIn('verif_peralatan_detail_id', $detailIds)
            ->get();

        $detailOkCount = $details->where('verifikasi', true)->count();
        $detailNcCount = $details->where('verifikasi', false)->count();

        $bagianOkCount = $bagians->where('verifikasi', true)->count();
        $bagianNcCount = $bagians->where('verifikasi', false)->count();

        $totalOk = $detailOkCount + $bagianOkCount;
        $totalNc = $detailNcCount + $bagianNcCount;
        $totalInspection = $totalOk + $totalNc;

        // Top 3 Area
        $areaNcMap = [];
        foreach ($details->where('verifikasi', false) as $d) {
            $areaName = $d->area->area ?? ($d->mesin->area->area ?? 'Unknown Area');
            $areaNcMap[$areaName] = ($areaNcMap[$areaName] ?? 0) + 1;
        }
        foreach ($bagians->where('verifikasi', false) as $b) {
            $areaName = $b->detail->area->area ?? ($b->detail->mesin->area->area ?? 'Unknown Area');
            $areaNcMap[$areaName] = ($areaNcMap[$areaName] ?? 0) + 1;
        }
        arsort($areaNcMap);
        $topArea = [];
        foreach (array_slice($areaNcMap, 0, 3, true) as $name => $count) {
            $topArea[] = ['nama' => $name, 'total' => $count];
        }

        // Top 3 Bagian / Mesin
        $mesinNcMap = [];
        foreach ($details->where('verifikasi', false) as $d) {
            $mesinName = $d->mesin->nama_mesin ?? 'Unknown Mesin';
            $mesinNcMap[$mesinName] = ($mesinNcMap[$mesinName] ?? 0) + 1;
        }
        foreach ($bagians->where('verifikasi', false) as $b) {
            $mesinName = $b->detail->mesin->nama_mesin ?? 'Unknown Mesin';
            $mesinNcMap[$mesinName] = ($mesinNcMap[$mesinName] ?? 0) + 1;
        }
        arsort($mesinNcMap);
        $topMesin = [];
        foreach (array_slice($mesinNcMap, 0, 3, true) as $name => $count) {
            $topMesin[] = ['nama' => $name, 'total' => $count];
        }

        // Top 3 Titik / Part
        $titikNcMap = [];
        foreach ($bagians->where('verifikasi', false) as $b) {
            $titikName = $b->bagian->nama_bagian ?? 'Unknown Titik/Part';
            $titikNcMap[$titikName] = ($titikNcMap[$titikName] ?? 0) + 1;
        }
        arsort($titikNcMap);
        $topTitik = [];
        foreach (array_slice($titikNcMap, 0, 3, true) as $name => $count) {
            $topTitik[] = ['nama' => $name, 'total' => $count];
        }

        return response()->json([
            'total_inspection' => $totalInspection,
            'total_nc'         => $totalNc,
            'total_ok'         => $totalOk,
            'top_area'         => $topArea,
            'top_mesin'        => $topMesin,
            'top_titik'        => $topTitik,
        ]);
    }

    /**
     * Export dashboard summary and panel data to Excel.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelAll(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return abort(403, 'Unauthorized');
        }

        // Ambil id_plan user yang sedang login sebagai default (persis sepertiGrafana iframe)
        $userPlan = $user->id_plan ?? 1;
        $reqPlan  = $request->input('id_plan');

        // Jika id_plan dari request tidak spesifik (kosong / $__all / all), gunakan id_plan milik user login
        $idPlan = (!empty($reqPlan) && $reqPlan !== '$__all' && $reqPlan !== 'all') ? $reqPlan : $userPlan;

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        // Jika from_date & to_date tidak diisi, default ke Last 24 Hours (persis seperti Grafana default: from=now-24h&to=now)
        if (empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::now()->subHours(24)->format('Y-m-d H:i:s');
            $toDate   = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            if (!empty($fromDate)) {
                $fromDate = Carbon::parse($fromDate)->format('Y-m-d H:i:s');
            }
            if (!empty($toDate)) {
                $toDate = Carbon::parse($toDate)->format('Y-m-d H:i:s');
            }
        }

        $activeRoleId = (int) ($user->id_role ?? (is_numeric($user->role) ? $user->role : 0));

        $filters = [
            'id_plan'   => $request->input('id_plan'),
            'id_produk' => $request->input('id_produk'),
            'from_date' => $fromDate,
            'to_date'   => $toDate,
            'role_id'   => $activeRoleId,
        ];

        $filename = 'Dashboard_Production_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new AllProcessExport($filters), $filename);
    }

    /**
     * Get detail NC breakdown: Area + Mesin + Bagian/Detail + NG Category — paginated.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSanitationDetail(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reqPlan  = $request->input('id_plan');
        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $page     = max(1, (int) $request->input('page', 1));
        $perPage  = 10;

        $userPlan     = $user->id_plan ?? 1;
        $activeRoleId = (int) ($user->id_role ?? (is_numeric($user->role) ? $user->role : 0));

        $idPlan = (!empty($reqPlan) && $reqPlan !== '$__all' && $reqPlan !== 'all') ? $reqPlan : null;
        if (in_array($activeRoleId, [2, 3]) && !$idPlan) {
            $idPlan = $userPlan;
        }

        // Ambil header IDs terlebih dahulu berdasarkan filter plan & tanggal
        $headerQuery = VerifPeralatan::query()->operasional();
        if ($idPlan) {
            $headerQuery->where('id_plan', $idPlan);
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $headerQuery->whereBetween('tanggal', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        } elseif (!empty($fromDate)) {
            $headerQuery->whereDate('tanggal', '>=', Carbon::parse($fromDate));
        } elseif (!empty($toDate)) {
            $headerQuery->whereDate('tanggal', '<=', Carbon::parse($toDate));
        }

        $headerIds = $headerQuery->pluck('id');

        if ($headerIds->isEmpty()) {
            return response()->json(['data' => [], 'total' => 0, 'per_page' => $perPage, 'page' => 1]);
        }

        // Query DB langsung dengan JOIN agar efisien untuk GROUP BY
        $query = \Illuminate\Support\Facades\DB::table('verif_peralatan_bagian as vpb')
            ->join('verif_peralatan_detail as vpd', 'vpb.verif_peralatan_detail_id', '=', 'vpd.id')
            ->join('input_mesin_peralatan as imp', 'vpd.id_mesin', '=', 'imp.id')
            ->join('input_area as ia', 'imp.id_area', '=', 'ia.id')
            ->join('bagian_mesin as bm', 'vpb.id_bagian', '=', 'bm.id')
            ->whereIn('vpd.verif_peralatan_id', $headerIds)
            ->where('vpb.verifikasi', false)
            ->select(
                'ia.area as area',
                'imp.nama_mesin as mesin',
                'bm.nama_bagian as bagian',
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(TRIM(vpb.keterangan), ""), "-") as ng_category'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as total')
            )
            ->groupBy('ia.area', 'imp.nama_mesin', 'bm.nama_bagian', 'ng_category')
            ->orderByDesc('total');

        $totalCount = $query->get()->count();

        $data = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->values();

        return response()->json([
            'data'     => $data,
            'total'    => $totalCount,
            'per_page' => $perPage,
            'page'     => $page,
        ]);
    }
}

