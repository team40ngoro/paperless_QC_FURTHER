<?php

namespace App\Services\BulkExport;

use App\Exports\PersiapanBahanBetterExport;
use App\Models\PersiapanBahanBetter;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PersiapanBahanBetterBulkExportService
{
    use HasNoDataHtmlResponse;

    public function export(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_filter'  => 'nullable|exists:data_shift,id',
            'produk_filter' => 'nullable|exists:jenis_produk,id',
            'kode_form'     => 'required|string|max:50',
        ]);

        $user = auth()->user();

        $query = PersiapanBahanBetter::with([
            'user', 'plan', 'produk', 'better', 'aktuals.std', 'shift', 
            'qcApprover', 'produksiApprover', 'spvApprover'
        ])
        ->where('is_audit', $request->boolean('is_audit', false))
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
              ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->filled('shift_filter')) {
            $query->where('shift_id', $request->shift_filter);
        }

        if ($request->filled('produk_filter')) {
            $query->where('id_produk', $request->produk_filter);
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

        if ($data->isEmpty()) {
            $filterInfo = [];
            $filterInfo[] = 'Periode: ' . \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            if ($request->shift_filter) {
                $shift = DataShift::find($request->shift_filter);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->produk_filter) {
                $produk = JenisProduk::find($request->produk_filter);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
            }

            return $this->noDataResponse($filterInfo);
        }

        // Update kode_form jika ada data
        $query->update(['kode_form' => $request->kode_form]);

        $shiftName = 'Semua Shift';
        if ($request->shift_filter) {
            $shiftObj = DataShift::find($request->shift_filter);
            $shiftName = $shiftObj ? 'Shift ' . $shiftObj->shift : 'Shift Unknown';
        }

        $produkName = 'Semua Produk';
        if ($request->produk_filter) {
            $produkObj = JenisProduk::find($request->produk_filter);
            $produkName = $produkObj ? $produkObj->nama_produk : 'Produk Unknown';
        }

        $filterInfo = [
            'tanggal_mulai' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
            'tanggal_akhir' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
            'shift'         => $shiftName,
            'produk'        => $produkName,
            'kode_form'     => $request->kode_form,
            'plan_name'     => $user->plan->nama_plan ?? 'PLANT',
            'is_audit'      => $request->boolean('is_audit', false),
        ];

        $filename = 'persiapan_bahan_better_bulk_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PersiapanBahanBetterExport($data, $filterInfo), $filename);
    }
}
