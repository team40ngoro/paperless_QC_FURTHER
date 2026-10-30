<?php

namespace App\Services\BulkExport;

use App\Exports\ProsesThawingExport;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\DataShift;
use App\Models\ProsesTwahing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProsesTwahingBulkExportService
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
            'kode_form' => 'nullable|string',
            'is_audit' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $query = ProsesTwahing::with(['details.rm', 'shift', 'user', 'plan', 'produk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        if ($request->filled('id_shift')) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $query->where('tanggal', '>=', $tanggalAwal);
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();

        if ($records->isEmpty()) {
            $filterInfo = [
                'Tanggal' => $request->tanggal_awal && $request->tanggal_akhir 
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') 
                    : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua Tanggal'),
                'Shift' => $request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
                'Kode Form' => $request->kode_form ?? 'QF 33/00',
            ];

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data Proses Thawing yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        $filename = 'Proses_Thawing_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(new ProsesThawingExport($records), $filename);
    }
}
