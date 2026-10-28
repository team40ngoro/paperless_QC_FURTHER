<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersiapanColdMixing;
use App\Models\AktualSuhuAdonan;
use App\Models\JenisProduk;
use App\Models\SuhuAdonan;
use Illuminate\Support\Str;

class PersiapanColdMixingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PersiapanColdMixing::with(['user', 'plan', 'produk', 'suhu_adonan', 'aktuals', 'shift']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get();
        return view('qc-sistem.persiapan_cold_mixing.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_cold_mixing.create', compact('produks', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'rework' => 'nullable|string',
            'hasil_pemeriksaan' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $persiapan = PersiapanColdMixing::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'id_produk' => $request->id_produk,
            'id_suhu_adonan' => $request->id_suhu_adonan,
            'rework' => $request->rework,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
        ]);
        // Hitung total aktual suhu
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $total = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '')->sum();

        AktualSuhuAdonan::create([
            'uuid' => Str::uuid(),
            'id_persiapan_cold_mixing' => $persiapan->id,
            'id_suhu_adonan' => $request->id_suhu_adonan,
            'aktual_suhu_1' => $request->aktual_suhu_1,
            'aktual_suhu_2' => $request->aktual_suhu_2,
            'aktual_suhu_3' => $request->aktual_suhu_3,
            'aktual_suhu_4' => $request->aktual_suhu_4,
            'aktual_suhu_5' => $request->aktual_suhu_5,
            'total_aktual_suhu' => $total,
        ]);
        return redirect()->route('persiapan-cold-mixing.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $item = PersiapanColdMixing::where('uuid', $uuid)->with(['produk', 'suhu_adonan', 'aktuals', 'shift'])->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }

        $suhuAdonans = SuhuAdonan::where('id_produk', $item->id_produk)->get();
        $aktual = $item->aktuals->first();
        return view('qc-sistem.persiapan_cold_mixing.edit', compact('item', 'produks', 'shifts', 'suhuAdonans', 'aktual'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'rework' => 'nullable|string',
            'hasil_pemeriksaan' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $persiapan = PersiapanColdMixing::where('uuid', $uuid)->firstOrFail();
        $persiapan->update([
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'id_produk' => $request->id_produk,
            'shift_id' => $request->shift_id,
            'id_suhu_adonan' => $request->id_suhu_adonan,
            'rework' => $request->rework,
            'tanggal' => $request->tanggal,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
        ]);
        // Update atau buat data aktual
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $total = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '')->sum();

        $aktual = AktualSuhuAdonan::where('id_persiapan_cold_mixing', $persiapan->id)->first();
        if ($aktual) {
            $aktual->update([
                'tanggal' => $request->tanggal,
                'shift_id' => $request->shift_id,
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $total,
            ]);
        } else {
            AktualSuhuAdonan::create([
                'uuid' => Str::uuid(),
                'id_persiapan_cold_mixing' => $persiapan->id,
                'tanggal' => $request->tanggal,
                'shift_id' => $request->shift_id,
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $total,
            ]);
        }
        return redirect()->route('persiapan-cold-mixing.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $persiapan = PersiapanColdMixing::where('uuid', $uuid)->firstOrFail();
        // Hapus data aktual terkait
        AktualSuhuAdonan::where('id_persiapan_cold_mixing', $persiapan->id)->delete();
        $persiapan->delete();
        return redirect()->route('persiapan-cold-mixing.index')->with('success', 'Data berhasil dihapus');
    }
}
