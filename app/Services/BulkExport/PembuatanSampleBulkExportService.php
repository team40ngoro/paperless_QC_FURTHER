<?php

namespace App\Services\BulkExport;

use App\Exports\PembuatanSampleExport;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PembuatanSample;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PembuatanSampleBulkExportService
{
    use HasNoDataHtmlResponse;

    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'id_shift' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'nullable|string',
            'is_audit' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($request->has('is_audit')) {
            $query->where('is_audit', $request->boolean('is_audit'));
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $query->where('tanggal', '>=', $tanggalAwal);
        }

        if ($request->filled('id_shift')) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $records = $query->orderBy('jenis_sample', 'asc')->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

        if ($records->isEmpty()) {
            $filterInfo = [
                'Tanggal' => $request->tanggal_awal && $request->tanggal_akhir 
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') 
                    : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua Tanggal'),
                'Shift' => $request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
                'Produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
                'Kode Form' => $request->kode_form ?? 'QF 17/00',
            ];

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data Pembuatan Sample yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        $filename = 'Pembuatan_Sample_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(new PembuatanSampleExport($records), $filename);
    }
}
