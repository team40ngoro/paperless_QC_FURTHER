<?php

namespace App\Services\BulkExport;

use App\Exports\PersiapanBahanEmulsiExport;
use App\Models\PembuatanEmulsi;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PersiapanBahanEmulsiBulkExportService
{
    use HasNoDataHtmlResponse;

    public function export(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id'      => 'nullable|exists:data_shift,id',
            'id_produk'     => 'nullable|exists:jenis_produk,id',
            'kode_form'     => 'required|string|max:50',
        ]);

        $user = auth()->user();

        $query = PembuatanEmulsi::with([
            'plan',
            'produk', 
            'shift',
            'suhuEmulsi.bahanEmulsi',
            'nama_emulsi',
            'nomor_emulsi',
            'total_pemakaian',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->where('is_audit', $request->boolean('is_audit', false))
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
              ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

        if ($data->isEmpty()) {
            $filterInfo = [];
            $filterInfo[] = 'Periode: ' . \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->id_produk) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
            }

            return $this->noDataResponse($filterInfo);
        }

        // Update kode_form jika ada data
        $query->update(['kode_form' => $request->kode_form]);

        $shiftName = 'Semua Shift';
        if ($request->shift_id) {
            $shiftObj = DataShift::find($request->shift_id);
            $shiftName = $shiftObj ? 'Shift ' . $shiftObj->shift : 'Shift Unknown';
        }

        $produkName = 'Semua Produk';
        if ($request->id_produk) {
            $produkObj = JenisProduk::find($request->id_produk);
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

        $filename = 'persiapan_bahan_emulsi_bulk_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PersiapanBahanEmulsiExport($data, $filterInfo), $filename);
    }
}
