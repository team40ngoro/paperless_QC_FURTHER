<?php

namespace App\Services\BulkExport;

use App\Exports\BarangMudahPecahExport;
use App\Models\BarangMudahPecah;
use App\Models\DataShift;
use App\Models\InputArea;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BarangMudahPecahBulkExportService
{
    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|integer',
            'id_area' => 'nullable|integer|exists:input_area,id',
            'kode_form' => 'required|string|max:50',
        ]);

        $user = Auth::user();

        // Relasi disamakan dengan yang dipakai bulkExportPdf() di controller
        // (ditambah qcApprover/produksiApprover/spvApprover supaya blok tanda
        // tangan tidak perlu query User::find() satu per satu).
        $query = BarangMudahPecah::with([
            'plan',
            'user',
            'shift',
            'namaBarang',
            'area',
            'qcApprover',
            'produksiApprover',
            'spvApprover',
        ]);

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        $query->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->tanggal_awal) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }

        if ($request->shift_id) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->id_area) {
            $query->where('id_area', $request->id_area);
        }

        $records = $query->orderBy('created_at', 'desc')->get();

        if ($records->isEmpty()) {
            $filterInfo = [];

            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift->shift ?? 'Unknown');
            }
            if ($request->id_area) {
                $area = InputArea::find($request->id_area);
                $filterInfo[] = 'Area: ' . ($area->area ?? 'Unknown');
            }

            $listItems = collect($filterInfo)->map(fn ($info) => '<li>' . e($info) . '</li>')->implode('');

            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Data Tidak Ditemukan</title>'
                . '<style>body{font-family:Arial,sans-serif;padding:40px;text-align:center;background:#f8f9fa}'
                . '.container{max-width:600px;margin:0 auto;background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1)}'
                . '.icon{font-size:64px;color:#ffc107;margin-bottom:20px}h1{color:#495057;margin-bottom:20px}'
                . '.message{color:#6c757d;margin-bottom:30px;font-size:16px}'
                . '.filter-info{background:#e9ecef;padding:15px;border-radius:5px;margin:20px 0;text-align:left}'
                . '.filter-info ul{margin:0;padding-left:20px}'
                . '.btn{background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:20px}</style>'
                . '</head><body><div class="container"><div class="icon">&#9888;&#65039;</div>'
                . '<h1>Data Tidak Ditemukan</h1>'
                . '<p class="message">Tidak ada data Barang Mudah Pecah yang sesuai dengan filter yang dipilih.</p>'
                . (!empty($filterInfo) ? '<div class="filter-info"><h4>Filter yang digunakan:</h4><ul>' . $listItems . '</ul></div>' : '')
                . '<p style="color:#6c757d;font-size:14px;">Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.</p>'
                . '<a href="javascript:window.close()" class="btn">Tutup</a></div></body></html>';

            return response($html)->header('Content-Type', 'text/html');
        }

        // Sama seperti bulkExportPdf(): kode_form yang diinput ikut disimpan ke
        // semua record yang lolos filter, supaya PDF & Excel dari batch yang
        // sama selalu menampilkan kode_form yang identik.
        BarangMudahPecah::whereIn('id', $records->pluck('id'))->update(['kode_form' => $request->kode_form]);

        $filename = 'barang_mudah_pecah_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new BarangMudahPecahExport($records, $request->kode_form),
            $filename
        );
    }
}