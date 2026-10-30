<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenyimpananBahan;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenyimpananBahanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PenyimpananBahan::with(['shift', 'plan', 'user']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        // Optional filter to show history per group_uuid
        if (request()->filled('group_uuid')) {
            $query->where('group_uuid', request('group_uuid'));
        } else {
            // Default: tampilkan hanya record ORIGINAL (bukan hasil per 2 jam)
            // Original adalah baris di mana group_uuid null (data lama) atau group_uuid == uuid (kepala grup)
            $query->where(function($q) {
                $q->whereNull('group_uuid')
                  ->orWhereColumn('group_uuid', 'uuid');
            });
        }
        $penyimpanan = $query->orderBy('tanggal', 'desc')->get(); // Ubah dari $data ke $penyimpanan
        return view('qc-sistem.penyimpanan_bahan.index', compact('penyimpanan'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('qc-sistem.penyimpanan_bahan.create', compact('shifts', 'plans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'pemeriksaan_kondisi' => 'required|array',
            'pemeriksaan_kebersihan' => 'required|array',
            'kebersihan_ruang' => 'required|array',
        ]);

        $count = count($data['pemeriksaan_kondisi']);
        for ($i = 0; $i < $count; $i++) {
            PenyimpananBahan::create([
                'uuid' => Str::uuid(),
                'shift_id' => $data['shift_id'],
                'id_plan' => $user->id_plan, // otomatis dari user login
                'user_id' => $user->id,      // otomatis dari user login
                'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
                'pemeriksaan_kondisi' => $data['pemeriksaan_kondisi'][$i] ?? null,
                'pemeriksaan_kebersihan' => $data['pemeriksaan_kebersihan'][$i] ?? null,
                'kebersihan_ruang' => $data['kebersihan_ruang'][$i] ?? null,
            ]);
        }

        return redirect()->route('penyimpanan-bahan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $penyimpanan = PenyimpananBahan::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('qc-sistem.penyimpanan_bahan.edit', compact('penyimpanan', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_plan' => 'required|exists:plan,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'pemeriksaan_kondisi' => 'required|string',
            'pemeriksaan_kebersihan' => 'required|string',
            'kebersihan_ruang' => 'required|string',
        ]);
        $item = PenyimpananBahan::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'shift_id' => $request->shift_id,
            'id_plan' => $request->id_plan,
            'user_id' => $user->id,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'pemeriksaan_kondisi' => $request->pemeriksaan_kondisi,
            'pemeriksaan_kebersihan' => $request->pemeriksaan_kebersihan,
            'kebersihan_ruang' => $request->kebersihan_ruang,
        ]);
        return redirect()->route('penyimpanan-bahan.index')->with('success', 'Data berhasil diupdate');
    }

    public function twoHourEdit($uuid)
    {
        $penyimpanan = PenyimpananBahan::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        $twoHour = true; // flag untuk view
        return view('qc-sistem.penyimpanan_bahan.edit', compact('penyimpanan', 'shifts', 'plans', 'twoHour'));
    }

    public function twoHourStore(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_plan' => 'required|exists:plan,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'pemeriksaan_kondisi' => 'required|string',
            'pemeriksaan_kebersihan' => 'required|string',
            'kebersihan_ruang' => 'required|string',
        ]);

        $original = PenyimpananBahan::where('uuid', $uuid)->firstOrFail();
        $groupUuid = $original->group_uuid ?: $original->uuid; // gunakan uuid asli sebagai group jika belum ada

        PenyimpananBahan::create([
            'uuid' => Str::uuid(),
            'group_uuid' => $groupUuid,
            'shift_id' => $request->shift_id,
            'id_plan' => $request->id_plan,
            'user_id' => $user->id,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'pemeriksaan_kondisi' => $request->pemeriksaan_kondisi,
            'pemeriksaan_kebersihan' => $request->pemeriksaan_kebersihan,
            'kebersihan_ruang' => $request->kebersihan_ruang,
        ]);

        // Pastikan original menyimpan group_uuid agar terhubung dalam riwayat
        if (!$original->group_uuid) {
            $original->update(['group_uuid' => $groupUuid]);
        }

        return redirect()->route('penyimpanan-bahan.index', ['group_uuid' => $groupUuid])
            ->with('success', 'Data per 2 jam berhasil disimpan sebagai record baru');
    }

    public function destroy($uuid)
    {
        $item = PenyimpananBahan::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('penyimpanan-bahan.index')->with('success', 'Data berhasil dihapus');
    }
}
