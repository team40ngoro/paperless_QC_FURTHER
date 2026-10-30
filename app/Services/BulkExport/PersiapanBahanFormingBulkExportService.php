<?php

namespace App\Services\BulkExport;

use App\Exports\PersiapanBahanFormingExport;
use App\Models\PersiapanBahanForming;
use App\Models\PersiapanBahanNonForming;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PersiapanBahanFormingBulkExportService
{
    use HasNoDataHtmlResponse;

    public function export(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id' => 'nullable|exists:data_shift,id',
            'produk_id' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:50',
            'is_audit' => 'nullable'
        ]);

        $user = auth()->user();
        $isAudit = $request->boolean('is_audit', false);

        // Query Forming
        $queryForming = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->where('is_audit', $isAudit)
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        })
        ->whereDate('tanggal', '>=', $request->tanggal_mulai)
        ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->shift_id) {
            $queryForming->where('shift_id', $request->shift_id);
        }

        if ($request->produk_id) {
            $queryForming->whereHas('formula.produk', function ($q) use ($request) {
                $q->where('id', $request->produk_id);
            });
        }

        $dataForming = $queryForming->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

        // Query Non-Forming
        $queryNonForming = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'details.bahanNonForming',
            'shift',
            'suhuAdonan',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->where('is_audit', $isAudit)
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        })
        ->whereDate('tanggal', '>=', $request->tanggal_mulai)
        ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->shift_id) {
            $queryNonForming->where('shift_id', $request->shift_id);
        }

        if ($request->produk_id) {
            $queryNonForming->whereHas('formulaNonForming.produk', function ($q) use ($request) {
                $q->where('id', $request->produk_id);
            });
        }

        $dataNonForming = $queryNonForming->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

        // Handle No Data
        if ($dataForming->isEmpty() && $dataNonForming->isEmpty()) {
            $filterInfo = [
                'Mulai Tanggal' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
                'Sampai Tanggal' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
            ];

            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo['Shift'] = $shift ? $shift->shift : 'Unknown';
            }

            if ($request->produk_id) {
                $produk = JenisProduk::find($request->produk_id);
                $filterInfo['Produk'] = $produk ? $produk->nama_produk : 'Unknown';
            }

            $filterInfo['Mode'] = $isAudit ? 'Audit' : 'Operasional';

            return $this->buildNoDataHtmlResponse('Data Persiapan Bahan Cold Mixing Tidak Ditemukan', $filterInfo);
        }

        // Simpan/update kode_form
        $dataForming->each(fn($item) => $item->update(['kode_form' => $request->kode_form]));
        $dataNonForming->each(fn($item) => $item->update(['kode_form' => $request->kode_form]));

        $shiftText = $request->shift_id ? (DataShift::find($request->shift_id)->shift ?? 'Semua Shift') : 'Semua Shift';
        $produkText = $request->produk_id ? (JenisProduk::find($request->produk_id)->nama_produk ?? 'Semua Produk') : 'Semua Produk';
        $planName = $user->getEffectivePlan()->nama_plan ?? 'PLANT';

        $filterInfo = [
            'tanggal_mulai' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
            'tanggal_akhir' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
            'shift' => $shiftText,
            'produk' => $produkText,
            'kode_form' => $request->kode_form,
            'plan_name' => $planName,
            'is_audit' => $isAudit,
        ];

        $filename = 'persiapan_bahan_forming&nonforming_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PersiapanBahanFormingExport($dataForming, $dataNonForming, $filterInfo), $filename);
    }
}
