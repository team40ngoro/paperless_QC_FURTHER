<?php

namespace App\Services\BulkExport;

use App\Exports\ProdukYumExport;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\ProdukYum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProdukYumBulkExportService
{
    use HasNoDataHtmlResponse;

    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'kode_form' => 'nullable|string|max:50',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'is_audit' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        $query = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $records = $query->orderBy('tanggal', 'desc')->get();

        if ($records->isEmpty()) {
            $filterInfo = [
                'Tanggal' => $request->tanggal_awal && $request->tanggal_akhir 
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') 
                    : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua Tanggal'),
                'Shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
                'Produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
                'Kode Form' => $request->kode_form ?? 'QF 16/00',
            ];

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data Produk YUM yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        $filename = 'Produk_YUM_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(new ProdukYumExport($records), $filename);
    }
}
