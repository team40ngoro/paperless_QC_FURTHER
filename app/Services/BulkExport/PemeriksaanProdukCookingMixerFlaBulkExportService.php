<?php

namespace App\Services\BulkExport;

use App\Exports\PemeriksaanProdukCookingMixerFlaExport;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\DataShift;
use App\Models\NamaFormulaFla;
use App\Models\PemeriksaanProdukCookingMixerFla;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PemeriksaanProdukCookingMixerFlaBulkExportService
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
            'id_formula_fla' => 'nullable|exists:nama_formula_flas,id',
            'is_audit' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Relasi disamakan dengan yang dipakai export_pdf.blade.php, supaya kolom
        // "Nama Produk", "Detail Bahan & Kode Produksi", dan blok tanda tangan
        // di Excel bisa terisi sama seperti di PDF.
        $query = PemeriksaanProdukCookingMixerFla::with([
            'plan',
            'user',
            'shift',
            'namaFormulaFla.produk',
            'bahanFormulaFla',
            'qcApprover',
            'produksiApprover',
            'spvApprover',
        ]);

        if ($user->id_role != 1) {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->where('is_audit', false);
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }

        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('id_formula_fla')) {
            $query->where('id_formula_fla', $request->id_formula_fla);
        }

        $records = $query->orderBy('tanggal', 'desc')->get();

        $shift = $request->filled('shift_id') ? DataShift::find($request->shift_id) : null;

        if ($records->isEmpty()) {
            $filterInfo = [
                'Tanggal' => $request->tanggal_awal && $request->tanggal_akhir
                    ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y')
                    : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua Tanggal'),
                'Shift' => $shift->shift ?? 'Semua Shift',
                'Formula' => $request->id_formula_fla ? NamaFormulaFla::find($request->id_formula_fla)->nama_formula ?? 'Tidak Diketahui' : 'Semua Formula',
                'Kode Form' => $request->kode_form ?? 'QF 10/00',
            ];

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data Pemeriksaan Cooking Mixer Fla yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        // Bentuk info filter disamakan dengan yang dikirim ke export_pdf.blade.php
        // (lihat PemeriksaanProdukCookingMixerFlaController::exportPdf), supaya
        // header Excel dan header PDF menampilkan info yang identik.
        $tanggalFilter = 'Semua Tanggal';
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->filled('tanggal_awal')) {
            $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }

        $filters = [
            'tanggal' => $tanggalFilter,
            'shift_nama' => $shift->shift ?? 'Semua Shift',
            'kode_form' => $request->kode_form ?? '-',
        ];

        $plantName = $user->plan->nama_plan ?? 'UNKNOWN';

        $filename = 'Pemeriksaan_Cooking_Mixer_Fla_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(
            new PemeriksaanProdukCookingMixerFlaExport($records, $filters, $filters['kode_form'], $plantName),
            $filename
        );
    }
}