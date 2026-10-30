<?php

namespace App\Services\BulkExport;

use App\Exports\LabelStartUpExport;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\LabelStartUp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LabelStartUpBulkExportService
{
    use HasNoDataHtmlResponse;

    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'shift_id' => 'nullable|integer',
            'produk_id' => 'nullable|integer',
            'kode_form' => 'nullable|string|max:50',
            'is_audit' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isAudit = $request->boolean('is_audit');
        $produkId = $request->input('produk_id');

        $query = LabelStartUp::with(['details' => function ($q) use ($produkId) {
            if ($produkId) {
                $q->where('id_produk', $produkId);
            }
            $q->with('produk');
        }, 'plan', 'shift', 'user'])
        ->where('is_audit', $isAudit);

        if ($user->role_id != 1) {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        if ($produkId) {
            $query->whereHas('details', function ($q) use ($produkId) {
                $q->where('id_produk', $produkId);
            });
        }

        $records = $query->orderBy('tanggal', 'asc')->orderBy('id_shift', 'asc')->get();

        if ($records->isEmpty()) {
            $filterInfo = [
                'Tanggal' => $request->tanggal_mulai && $request->tanggal_akhir 
                    ? Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') 
                    : ($request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->format('d-m-Y') : 'Semua Tanggal'),
                'Shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
                'Produk' => $produkId ? JenisProduk::find($produkId)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
                'Kode Form' => $request->kode_form ?? 'QF 20/00',
            ];

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data Label Pemeriksaan / Start Up yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        $filename = 'Label_Pemeriksaan_' . ($request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(new LabelStartUpExport($records), $filename);
    }
}
