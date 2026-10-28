<?php

namespace App\Services\BulkExport;

use App\Exports\WizardProses2Export;
use App\Models\PembekuanIqfRoasting;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class WizardProses2BulkExportService
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

        $query = PembekuanIqfRoasting::with([
            'user', 'plan', 'shift',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat',
            'prosesRoastingFanData.penggorengan',
            'prosesRoastingFanData.produk',
            'prosesRoastingFanData.suhuBlok',
            'prosesRoastingFanData.stdFan',
            'breaderData',
            'batteringData.jenis_better',
            'predustData.jenisPredust',
            'penggorenganData.shift',
        ])
        ->where('is_audit', $request->boolean('is_audit', false))
        ->whereNotNull('penggorengan_uuid')
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

        $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
              ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->shift_id) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->produk_id) {
            $query->whereHas('hasilProsesRoastingData', function ($q) use ($request) {
                $q->where('id_produk', $request->produk_id);
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        if ($data->isEmpty()) {
            $filterInfo = [];
            $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->produk_id) {
                $produk = JenisProduk::find($request->produk_id);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
            }

            return $this->buildNoDataHtmlResponse('Tidak ada data yang sesuai dengan filter yang dipilih.', $filterInfo);
        }

        $filters = [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'shift_id'      => $request->shift_id,
            'produk_id'     => $request->produk_id,
            'kode_form'     => $request->kode_form
        ];

        $filename = 'Wizard-Proses2-Bulk-' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new WizardProses2Export($data, $request->kode_form, $filters), $filename);
    }
}
