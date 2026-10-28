<?php

namespace App\Services\BulkExport;

use App\Exports\VerifikasiBeratProdukExport;
use App\Models\VerifikasiBeratProduk;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class VerifikasiBeratProdukBulkExportService
{
    use HasNoDataHtmlResponse;

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id'      => 'nullable|exists:data_shift,id',
            'id_produk'     => 'nullable|exists:jenis_produk,id',
            'kode_form'     => 'required|string|max:50',
            'is_audit'      => 'nullable|boolean',
        ]);

        $user = auth()->user();

        $query = VerifikasiBeratProduk::with(['plan', 'shift', 'user', 'produk'])
            ->where('is_audit', $request->boolean('is_audit', false))
            ->whereDate('tanggal', '>=', $request->tanggal_mulai)
            ->whereDate('tanggal', '<=', $request->tanggal_akhir);

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('shift_id', 'asc')->get();

        if ($data->isEmpty()) {
            $filterInfo = [];
            $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y')
                . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            if ($request->filled('shift_id')) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->filled('id_produk')) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
            }

            return $this->buildNoDataHtmlResponse(
                'Tidak ada data yang sesuai dengan filter yang dipilih.',
                $filterInfo
            );
        }

        $filters = [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'shift_id'      => $request->shift_id,
            'id_produk'     => $request->id_produk,
            'kode_form'     => $request->kode_form,
        ];

        $filename = 'Verifikasi-Berat-Produk-' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new VerifikasiBeratProdukExport($data, $filters), $filename);
    }
}
