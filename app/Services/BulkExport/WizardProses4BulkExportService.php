<?php

namespace App\Services\BulkExport;

use App\Exports\WizardProses4Export;
use App\Models\ProsesAging;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WizardProses4BulkExportService
{
    use HasNoDataHtmlResponse;

    public function export(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id'      => 'nullable|integer',
            'produk_id'     => 'nullable|integer',
            'kode_form'     => 'required|string|max:50',
            'is_audit'      => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $isAudit = $request->boolean('is_audit', false);

        $query = ProsesAging::with([
            'user', 'plan', 'produk', 'bahanBaku.shift',
            'prosesTumbling.dataTumbling',
            'prosesTumbling.shift',
            'prosesTumbling.user',
            'prosesTumbling.bahanBakuTumbling',
            'prosesTumblings.dataTumbling',
            'qcApprover', 'produksiApprover', 'spvApprover'
        ])
        ->where('is_audit', $isAudit)
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
              ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->filled('shift_id')) {
            $query->whereHas('bahanBaku', function ($q) use ($request) {
                $q->where('shift_id', $request->shift_id);
            });
        }

        if ($request->filled('produk_id')) {
            $query->where('id_produk', $request->produk_id);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            $filterInfo = [];
            $filterInfo[] = 'Periode: ' . \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->produk_id) {
                $produk = JenisProduk::find($request->produk_id);
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
        if ($request->produk_id) {
            $produkObj = JenisProduk::find($request->produk_id);
            $produkName = $produkObj ? $produkObj->nama_produk : 'Produk Unknown';
        }

        $filterInfo = [
            'tanggal_mulai' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
            'tanggal_akhir' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
            'shift'         => $shiftName,
            'produk'        => $produkName,
            'kode_form'     => $request->kode_form,
            'plan_name'     => $user->plan->nama_plan ?? 'PLANT',
            'is_audit'      => $isAudit,
        ];

        $filename = 'wizard_proses4_bulk_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new WizardProses4Export($data, $filterInfo), $filename);
    }
}
