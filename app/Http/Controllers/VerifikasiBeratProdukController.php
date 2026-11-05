<?php

namespace App\Http\Controllers;

use App\Models\VerifikasiBeratProduk;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\VerifikasiBeratLog;
use Carbon\Carbon;


class VerifikasiBeratProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('verifikasi-berat-produk.audit');
        }
        $query = VerifikasiBeratProduk::operasional()->with(['plan', 'user', 'shift', 'produk']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($qProd) use ($search) {
                      $qProd->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $perPage = request('per_page', 10);
        $this->applyDateRangeFilter(request(), $query);
        $data = $query->orderBy('tanggal', 'desc')
                      ->orderBy('shift_id', 'asc')
                      ->orderBy('created_at', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate($perPage);

        return view('qc-sistem.verifikasi-berat-produk.index', [
            'verifikasiBeratProduk' => $data,
            'records' => $data->getCollection(),
            'data' => $data,
            'search' => $search,
            'perPage' => $perPage
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            $shifts  = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts  = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.verifikasi-berat-produk.create', compact('shifts', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date', 
            'jam' => 'required',
            'kode_produksi' => 'required|string|max:255',
            'gramase' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'jenis_produk_kfc' => 'required|in:KFC,non-KFC',
            'berat_breader' => 'nullable|array',
            'berat_breader.*' => 'nullable|string|max:255',
            'rata_rata_breader' => 'nullable|numeric',
            'pickup_breader' => 'nullable|string|max:255',
            'pickup_total_breader' => 'nullable|string|max:255',
            'after_forming' => 'nullable|array',
            'after_forming.*' => 'nullable|string|max:255',
            'rata_rata_after_forming' => 'nullable|numeric',
            'berat_dry_kfc' => 'nullable|array',
            'berat_dry_kfc.*' => 'nullable|string|max:255',
            'rata_rata_dry_kfc' => 'nullable|numeric',
            'berat_wet_kfc' => 'nullable|array',
            'berat_wet_kfc.*' => 'nullable|string|max:255',
            'rata_rata_wet_kfc' => 'nullable|numeric',
            'pickup_after_forming_kfc' => 'nullable|string|max:255',
            'berat_predusting' => 'nullable|array',
            'berat_predusting.*' => 'nullable|string|max:255',
            'rata_rata_predusting' => 'nullable|numeric',
            'pickup_after_forming_predusting' => 'nullable|string|max:255',
            'berat_battering' => 'nullable|array',
            'berat_battering.*' => 'nullable|string|max:255',
            'rata_rata_battering' => 'nullable|numeric',
            'pickup_after_predusting_battering' => 'nullable|string|max:255',
            'berat_breadering' => 'nullable|array',
            'berat_breadering.*' => 'nullable|string|max:255',
            'rata_rata_breadering' => 'nullable|numeric',
            'pickup_after_battering_breadering' => 'nullable|string|max:255',
            'berat_fryer_1' => 'nullable|array',
            'berat_fryer_1.*' => 'nullable|string|max:255',
            'rata_rata_fryer_1' => 'nullable|numeric',
            'pickup_breadering_fryer_1' => 'nullable|string|max:255',
            'berat_fryer_2' => 'nullable|array',
            'berat_fryer_2.*' => 'nullable|string|max:255',
            'rata_rata_fryer_2' => 'nullable|numeric',
            'pickup_fryer_1_fryer_2' => 'nullable|string|max:255',
            'pickup_total' => 'nullable|string|max:255',
            'pickup_total_fryer_2' => 'nullable|string|max:255',
            'berat_roasting' => 'nullable|array',
            'berat_roasting.*' => 'nullable|string|max:255',
            'rata_rata_roasting' => 'nullable|numeric',
            'pickup_after_breadering_roasting' => 'nullable|string|max:255',
            'pickup_total_roasting' => 'nullable|string|max:255',
            'hasil_adonan' => 'nullable|in:OK,not OK',
            'suhu_adonan' => 'nullable|numeric',
        ]);

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now());

        // Verify shift belongs to user's plan
        $shift = DataShift::where('id', $request->shift_id)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->first();

        if (!$shift) {
            return back()->withErrors(['shift_id' => 'Shift tidak valid untuk plan Anda.']);
        }

        // Verify product belongs to user's plan
        $produk = JenisProduk::where('id', $request->id_produk)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->first();

        if (!$produk) {
            return back()->withErrors(['id_produk' => 'Produk tidak valid untuk plan Anda.']);
        }

        // Calculate average from breader values
        $breaderFiltered = $request->berat_breader ? array_filter($request->berat_breader) : null;
        $verifikasiBeratProduk = new VerifikasiBeratProduk();
        $rataRataBreader = $breaderFiltered ? $verifikasiBeratProduk->calculateBreaderAverage($breaderFiltered) : null;

        // Calculate average from after forming values
        $afterFormingFiltered = $request->after_forming ? array_filter($request->after_forming) : null;
        $rataRataAfterForming = $afterFormingFiltered ? $verifikasiBeratProduk->calculateAfterFormingAverage($afterFormingFiltered) : null;

        // Calculate average from KFC dry values
        $dryKfcFiltered = $request->berat_dry_kfc ? array_filter($request->berat_dry_kfc) : null;
        $rataRataDryKfc = $dryKfcFiltered ? $verifikasiBeratProduk->calculateDryKfcAverage($dryKfcFiltered) : null;

        // Calculate average from KFC wet values
        $wetKfcFiltered = $request->berat_wet_kfc ? array_filter($request->berat_wet_kfc) : null;
        $rataRataWetKfc = $wetKfcFiltered ? $verifikasiBeratProduk->calculateWetKfcAverage($wetKfcFiltered) : null;

        // Calculate average from predusting values
        $predustingFiltered = $request->berat_predusting ? array_filter($request->berat_predusting) : null;
        $rataRataPredusting = $predustingFiltered ? $verifikasiBeratProduk->calculatePredustingAverage($predustingFiltered) : null;

        // Calculate average from battering values
        $batteringFiltered = $request->berat_battering ? array_filter($request->berat_battering) : null;
        $rataRataBattering = $batteringFiltered ? $verifikasiBeratProduk->calculateBatteringAverage($batteringFiltered) : null;

        // Calculate average from breadering values
        $breaderingFiltered = $request->berat_breadering ? array_filter($request->berat_breadering) : null;
        $rataRataBreaderingValue = $breaderingFiltered ? $verifikasiBeratProduk->calculateBreaderingAverage($breaderingFiltered) : null;

        // Calculate average from fryer 1 values
        $fryer1Filtered = $request->berat_fryer_1 ? array_filter($request->berat_fryer_1) : null;
        $rataRataFryer1 = $fryer1Filtered ? $verifikasiBeratProduk->calculateFryer1Average($fryer1Filtered) : null;

        // Calculate average from fryer 2 values
        $fryer2Filtered = $request->berat_fryer_2 ? array_filter($request->berat_fryer_2) : null;
        $rataRataFryer2 = $fryer2Filtered ? $verifikasiBeratProduk->calculateFryer2Average($fryer2Filtered) : null;

        // Calculate average from roasting values
        $roastingFiltered = $request->berat_roasting ? array_filter($request->berat_roasting) : null;
        $rataRataRoasting = $roastingFiltered ? $verifikasiBeratProduk->calculateRoastingAverage($roastingFiltered) : null;

        VerifikasiBeratProduk::create([
            'id_plan' => $user->role === 'superadmin' ? $produk->id_plan : $user->id_plan,
            'user_id' => $user->id,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
             'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'kode_produksi' => $request->kode_produksi,
            'gramase' => $request->gramase,
            'catatan' => $request->catatan,
            'jenis_produk_kfc' => $request->jenis_produk_kfc,
            'berat_breader' => $breaderFiltered,
            'rata_rata_breader' => $rataRataBreader,
            'pickup_breader' => $request->pickup_breader,
            'pickup_total_breader' => $request->pickup_total_breader,
            'after_forming' => $afterFormingFiltered,
            'rata_rata_after_forming' => $rataRataAfterForming,
            'berat_dry_kfc' => $dryKfcFiltered,
            'rata_rata_dry_kfc' => $rataRataDryKfc,
            'berat_wet_kfc' => $wetKfcFiltered,
            'rata_rata_wet_kfc' => $rataRataWetKfc,
            'pickup_after_forming_kfc' => $request->pickup_after_forming_kfc,
            'berat_predusting' => $predustingFiltered,
            'rata_rata_predusting' => $rataRataPredusting,
            'pickup_after_forming_predusting' => $request->pickup_after_forming_predusting,
            'berat_battering' => $batteringFiltered,
            'rata_rata_battering' => $rataRataBattering,
            'pickup_after_predusting_battering' => $request->pickup_after_predusting_battering,
            'berat_breadering' => $breaderingFiltered,
            'rata_rata_breadering' => $rataRataBreaderingValue,
            'pickup_after_battering_breadering' => $request->pickup_after_battering_breadering,
            'berat_fryer_1' => $fryer1Filtered,
            'rata_rata_fryer_1' => $rataRataFryer1,
            'pickup_breadering_fryer_1' => $request->pickup_breadering_fryer_1,
            'berat_fryer_2' => $fryer2Filtered,
            'rata_rata_fryer_2' => $rataRataFryer2,
            'pickup_fryer_1_fryer_2' => $request->pickup_fryer_1_fryer_2,
            'pickup_total' => $request->pickup_total,
            'pickup_total_fryer_2' => $request->pickup_total_fryer_2,
            'berat_roasting' => $roastingFiltered,
            'rata_rata_roasting' => $rataRataRoasting,
            'pickup_after_breadering_roasting' => $request->pickup_after_breadering_roasting,
            'pickup_total_roasting' => $request->pickup_total_roasting,
            'hasil_adonan' => $request->hasil_adonan,
            'suhu_adonan' => $request->suhu_adonan,
        ]);

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::with(['plan', 'user', 'shift', 'produk'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('qc-sistem.verifikasi-berat-produk.show', compact('verifikasiBeratProduk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $user = Auth::user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($verifikasiBeratProduk->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('verifikasi-berat-produk.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $shifts  = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts  = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.verifikasi-berat-produk.edit', compact('verifikasiBeratProduk', 'shifts', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $user = Auth::user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($verifikasiBeratProduk->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('verifikasi-berat-produk.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
        
        $user = Auth::user();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'kode_produksi' => 'required|string|max:255',
            'gramase' => 'required|numeric|min:0',
            'jenis_produk_kfc' => 'required|in:KFC,non-KFC',
            'berat_breader' => 'nullable|array',
            'berat_breader.*' => 'nullable|string|max:255',
            'rata_rata_breader' => 'nullable|numeric',
            'pickup_breader' => 'nullable|string|max:255',
            'pickup_total_breader' => 'nullable|string|max:255',
            'after_forming' => 'nullable|array',
            'after_forming.*' => 'nullable|string|max:255',
            'rata_rata_after_forming' => 'nullable|numeric',
            'berat_dry_kfc' => 'nullable|array',
            'berat_dry_kfc.*' => 'nullable|string|max:255',
            'rata_rata_dry_kfc' => 'nullable|numeric',
            'berat_wet_kfc' => 'nullable|array',
            'berat_wet_kfc.*' => 'nullable|string|max:255',
            'rata_rata_wet_kfc' => 'nullable|numeric',
            'pickup_after_forming_kfc' => 'nullable|string|max:255',
            'berat_predusting' => 'nullable|array',
            'berat_predusting.*' => 'nullable|string|max:255',
            'rata_rata_predusting' => 'nullable|numeric',
            'pickup_after_forming_predusting' => 'nullable|string|max:255',
            'berat_battering' => 'nullable|array',
            'berat_battering.*' => 'nullable|string|max:255',
            'rata_rata_battering' => 'nullable|numeric',
            'pickup_after_predusting_battering' => 'nullable|string|max:255',
            'berat_breadering' => 'nullable|array',
            'berat_breadering.*' => 'nullable|string|max:255',
            'rata_rata_breadering' => 'nullable|numeric',
            'pickup_after_battering_breadering' => 'nullable|string|max:255',
            'berat_fryer_1' => 'nullable|array',
            'berat_fryer_1.*' => 'nullable|string|max:255',
            'rata_rata_fryer_1' => 'nullable|numeric',
            'pickup_breadering_fryer_1' => 'nullable|string|max:255',
            'pickup_total' => 'nullable|string|max:255',
            'berat_fryer_2' => 'nullable|array',
            'berat_fryer_2.*' => 'nullable|string|max:255',
            'rata_rata_fryer_2' => 'nullable|numeric',
            'pickup_fryer_1_fryer_2' => 'nullable|string|max:255',
            'pickup_total_fryer_2' => 'nullable|string|max:255',
            'berat_roasting' => 'nullable|array',
            'berat_roasting.*' => 'nullable|string|max:255',
            'rata_rata_roasting' => 'nullable|numeric',
            'pickup_after_breadering_roasting' => 'nullable|string|max:255',
            'pickup_total_roasting' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'hasil_adonan' => 'nullable|in:OK,not OK',
            'suhu_adonan' => 'nullable|numeric',
        ]);

        // Verify shift belongs to user's plan
        $shift = DataShift::where('id', $request->shift_id)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->first();

        if (!$shift) {
            return back()->withErrors(['shift_id' => 'Shift tidak valid untuk plan Anda.']);
        }

        // Verify product belongs to user's plan
        $produk = JenisProduk::where('id', $request->id_produk)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->first();

        if (!$produk) {
            return back()->withErrors(['id_produk' => 'Produk tidak valid untuk plan Anda.']);
        }

        // Calculate averages from array values (same as store method)
        $breaderFiltered = $request->berat_breader ? array_filter($request->berat_breader) : null;
        $rataRataBreader = $breaderFiltered ? $verifikasiBeratProduk->calculateBreaderAverage($breaderFiltered) : null;

        $afterFormingFiltered = $request->after_forming ? array_filter($request->after_forming) : null;
        $rataRataAfterForming = $afterFormingFiltered ? $verifikasiBeratProduk->calculateAfterFormingAverage($afterFormingFiltered) : null;

        $dryKfcFiltered = $request->berat_dry_kfc ? array_filter($request->berat_dry_kfc) : null;
        $rataRataDryKfc = $dryKfcFiltered ? $verifikasiBeratProduk->calculateDryKfcAverage($dryKfcFiltered) : null;

        $wetKfcFiltered = $request->berat_wet_kfc ? array_filter($request->berat_wet_kfc) : null;
        $rataRataWetKfc = $wetKfcFiltered ? $verifikasiBeratProduk->calculateWetKfcAverage($wetKfcFiltered) : null;

        $predustingFiltered = $request->berat_predusting ? array_filter($request->berat_predusting) : null;
        $rataRataPredusting = $predustingFiltered ? $verifikasiBeratProduk->calculatePredustingAverage($predustingFiltered) : null;

        $batteringFiltered = $request->berat_battering ? array_filter($request->berat_battering) : null;
        $rataRataBattering = $batteringFiltered ? $verifikasiBeratProduk->calculateBatteringAverage($batteringFiltered) : null;

        $breaderingFiltered = $request->berat_breadering ? array_filter($request->berat_breadering) : null;
        $rataRataBreaderingValue = $breaderingFiltered ? $verifikasiBeratProduk->calculateBreaderingAverage($breaderingFiltered) : null;

        $fryer1Filtered = $request->berat_fryer_1 ? array_filter($request->berat_fryer_1) : null;
        $rataRataFryer1 = $fryer1Filtered ? $verifikasiBeratProduk->calculateFryer1Average($fryer1Filtered) : null;

        $fryer2Filtered = $request->berat_fryer_2 ? array_filter($request->berat_fryer_2) : null;
        $rataRataFryer2 = $fryer2Filtered ? $verifikasiBeratProduk->calculateFryer2Average($fryer2Filtered) : null;

        $roastingFiltered = $request->berat_roasting ? array_filter($request->berat_roasting) : null;
        $rataRataRoasting = $roastingFiltered ? $verifikasiBeratProduk->calculateRoastingAverage($roastingFiltered) : null;

        $verifikasiBeratProduk->update([
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now()),
            'kode_produksi' => $request->kode_produksi,
            'gramase' => $request->gramase,
            'jenis_produk_kfc' => $request->jenis_produk_kfc,
            'berat_breader' => $breaderFiltered,
            'rata_rata_breader' => $rataRataBreader,
            'pickup_breader' => $request->pickup_breader,
            'pickup_total_breader' => $request->pickup_total_breader,
            'after_forming' => $afterFormingFiltered,
            'rata_rata_after_forming' => $rataRataAfterForming,
            'berat_dry_kfc' => $dryKfcFiltered,
            'rata_rata_dry_kfc' => $rataRataDryKfc,
            'berat_wet_kfc' => $wetKfcFiltered,
            'rata_rata_wet_kfc' => $rataRataWetKfc,
            'pickup_after_forming_kfc' => $request->pickup_after_forming_kfc,
            'berat_predusting' => $predustingFiltered,
            'rata_rata_predusting' => $rataRataPredusting,
            'pickup_after_forming_predusting' => $request->pickup_after_forming_predusting,
            'berat_battering' => $batteringFiltered,
            'rata_rata_battering' => $rataRataBattering,
            'pickup_after_predusting_battering' => $request->pickup_after_predusting_battering,
            'berat_breadering' => $breaderingFiltered,
            'rata_rata_breadering' => $rataRataBreaderingValue,
            'pickup_after_battering_breadering' => $request->pickup_after_battering_breadering,
            'berat_fryer_1' => $fryer1Filtered,
            'rata_rata_fryer_1' => $rataRataFryer1,
            'pickup_breadering_fryer_1' => $request->pickup_breadering_fryer_1,
            'pickup_total' => $request->pickup_total,
            'berat_fryer_2' => $fryer2Filtered,
            'rata_rata_fryer_2' => $rataRataFryer2,
            'pickup_fryer_1_fryer_2' => $request->pickup_fryer_1_fryer_2,
            'pickup_total_fryer_2' => $request->pickup_total_fryer_2,
            'berat_roasting' => $roastingFiltered,
            'rata_rata_roasting' => $rataRataRoasting,
            'pickup_after_breadering_roasting' => $request->pickup_after_breadering_roasting,
            'pickup_total_roasting' => $request->pickup_total_roasting,
            'catatan' => $request->catatan,
            'hasil_adonan' => $request->hasil_adonan,
            'suhu_adonan' => $request->suhu_adonan,
        ]);

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $verifikasiBeratProduk->delete();

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil dihapus.');
    }
    /**
     * Tampilkan riwayat perubahan/logs untuk satu data verifikasi berat produk
     */
    public function showLogs($uuid)
    {
        $item = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Otorisasi: hanya superadmin atau data dengan plan yang sama
        if (!$user->hasRole('superadmin') && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil log terkait data ini (paginasi)
        $logs = VerifikasiBeratLog::where('verifikasi_berat_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('qc-sistem.verifikasi-berat-produk.logs', compact('item', 'logs'));
    }
    /**
     * Export single data to PDF by UUID
     */
    public function exportPdf($uuid)
    {
        $item = VerifikasiBeratProduk::with(['plan', 'shift', 'user', 'produk'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $user = Auth::user();

        // Apply role-based filtering
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $data = collect([$item]);

        $filters = [
            'tanggal' => $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-',
            'shift' => $item->shift ? 'Shift ' . $item->shift->shift : '-',
            'kode_form' => $item->kode_form ?? 'QF 11/00', // Update default form code if needed
            'plan' => $item->plan ? $item->plan->nama_plan : '-',
            'produk' => $item->produk ? $item->produk->nama_produk : '-'
        ];

        $pdf = \PDF::loadView('qc-sistem.verifikasi-berat-produk.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'verifikasi-berat-produk-' . ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'data') . '-' . substr($uuid, 0, 8) . '.pdf';
        
        return $pdf->stream($filename);
    }

       /**
     * Bulk export PDF untuk Verifikasi Berat Produk
     */
    public function bulkExportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id' => 'nullable|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'is_audit' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
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

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang ditemukan dengan filter yang dipilih.');
        }

        $query->update(['kode_form' => $request->kode_form]);

        $shift = $request->filled('shift_id') ? DataShift::find($request->shift_id) : null;
        $plan = $user->role === 'superadmin' && $data->isNotEmpty() ? $data->first()->plan : $user->plan;
        $produk = $request->filled('id_produk') ? \App\Models\JenisProduk::find($request->id_produk) : null;

        $filters = [
            'tanggal_mulai' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d/m/Y'),
            'tanggal_akhir' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y'),
            'shift' => $shift ? 'Shift ' . $shift->shift : 'Semua Shift',
            'kode_form' => $request->kode_form,
            'plan' => $plan ? $plan->nama_plan : '-',
            'produk' => $produk ? $produk->nama_produk : 'Semua Produk'
        ];

        $pdf = \PDF::loadView('qc-sistem.verifikasi-berat-produk.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'verifikasi-berat-produk-' . $request->tanggal_mulai . '_to_' . $request->tanggal_akhir . '.pdf';
        
        return $pdf->stream($filename);
    }
     public function approve(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|exists:verifikasi_berat_produk,uuid',
            'type' => 'required|in:qc,produksi,spv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $item = VerifikasiBeratProduk::where('uuid', $request->uuid)->firstOrFail();

            // Check role-based permissions
            $userRole = $user->id_role ?? null;
            
            if ($request->type === 'qc') {
                // Only role 1, 3, 5 can approve as QC
                if (!in_array($userRole, [1, 3, 5])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai QC'
                    ], 403);
                }
                
                if ($item->approved_by_qc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh QC sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_qc = true;
                $item->qc_approved_by = $user->id;
                $item->qc_approved_at = now();
                
            } elseif ($request->type === 'produksi') {
                // Only role 2 can approve as Produksi
                if ($userRole !== 2) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai Produksi'
                    ], 403);
                }
                
                // Check if QC approval exists
                if (!$item->approved_by_qc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menunggu persetujuan QC terlebih dahulu'
                    ], 400);
                }
                
                if ($item->approved_by_produksi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh Produksi sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_produksi = true;
                $item->produksi_approved_by = $user->id;
                $item->produksi_approved_at = now();
                
            } elseif ($request->type === 'spv') {
                // Only role 4 can approve as SPV
                if ($userRole !== 4) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai SPV'
                    ], 403);
                }
                
                // Check if Produksi approval exists
                if (!$item->approved_by_produksi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menunggu persetujuan Produksi terlebih dahulu'
                    ], 400);
                }
                
                if ($item->approved_by_spv) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh SPV sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_spv = true;
                $item->spv_approved_by = $user->id;
                $item->spv_approved_at = now();
            }

            $item->save();

         VerifikasiBeratLog::create([
                'verifikasi_berat_id' => $item->id,
                'verifikasi_berat_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve_' . $request->type,
                'field_yang_diubah' => ['approved_by_' . $request->type],
                'nilai_lama' => [false],
                'nilai_baru' => [true],
                'keterangan' => 'Persetujuan ' . strtoupper($request->type) . ' oleh ' . $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_shift' => 'required|exists:data_shift,id',
            'type' => 'required|in:qc,produksi,spv',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $query = VerifikasiBeratProduk::whereDate('tanggal', $request->tanggal)
                ->where('shift_id', $request->id_shift);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui.'], 404);
            }

            $userRole = $user->id_role ?? null;
            $type = $request->type;
            $approvedCount = 0;

            foreach ($items as $item) {
                if ($type === 'qc') {
                    if (in_array($userRole, [1, 3, 5]) && !$item->approved_by_qc) {
                        $item->approved_by_qc = true;
                        $item->qc_approved_by = $user->id;
                        $item->qc_approved_at = now();
                        $item->save();
                        $approvedCount++;
                    }
                } elseif ($type === 'produksi') {
                    if (in_array($userRole, [1, 2, 5]) && $item->approved_by_qc && !$item->approved_by_produksi) {
                        $item->approved_by_produksi = true;
                        $item->produksi_approved_by = $user->id;
                        $item->produksi_approved_at = now();
                        $item->save();
                        $approvedCount++;
                    }
                } elseif ($type === 'spv') {
                    if (in_array($userRole, [1, 4, 5]) && $item->approved_by_produksi && !$item->approved_by_spv) {
                        $item->approved_by_spv = true;
                        $item->spv_approved_by = $user->id;
                        $item->spv_approved_at = now();
                        $item->save();
                        $approvedCount++;
                    }
                }
                
                if ($approvedCount > 0) {
                    VerifikasiBeratLog::create([
                        'verifikasi_berat_id' => $item->id,
                        'verifikasi_berat_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'aksi' => 'bulk_approve_' . $type,
                        'keterangan' => 'Batch approval ' . strtoupper($type) . ' by ' . $user->name
                    ]);
                }
            }

            return response()->json([
                'success' => true, 
                'message' => "Berhasil menyetujui $approvedCount data."
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1|max:100',
                'uuids.*' => 'required|uuid'
            ]);

            $user = Auth::user();
            $type = $request->input('type');
            $uuids = $request->input('uuids', []);

            // Role-based permission check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
                ], 403);
            }

            $query = VerifikasiBeratProduk::with('auditVersion')
                ->where('is_audit', false)
                ->whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update operasional records
            $updateData = [
                "approved_by_{$type}" => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now(),
            ];

            \Illuminate\Support\Facades\DB::transaction(function () use ($itemsToUpdate, $updateData, $user, $type) {
                foreach ($itemsToUpdate as $item) {
                    $item->update($updateData);

                    if ($item->auditVersion) {
                        $item->auditVersion->update($updateData);
                    }

                    VerifikasiBeratLog::create([
                        'verifikasi_berat_id' => $item->id,
                        'verifikasi_berat_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'aksi' => 'bulk_approve_selected_' . $type,
                        'keterangan' => 'Selected approval ' . strtoupper($type) . ' by ' . $user->name
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil verifikasi {$count} data " . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ===================== AUDIT METHODS =====================

    public function copyToAudit($uuid)
    {
        $user = Auth::user();
        
        // Auditor (id_role 6) bisa akses audit, atau user dengan permission 'create-verifikasi-berat-produk'
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-verifikasi-berat-produk')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        $clone = $item->copyToAudit();

        return redirect()->route('verifikasi-berat-produk.audit')
            ->with('success', 'Data berhasil dicopy ke Audit. Silakan lakukan penyesuaian.');
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = VerifikasiBeratProduk::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

        $query = VerifikasiBeratProduk::with(['plan', 'user', 'shift', 'produk'])
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', "%{$search}%")
                  ->orWhereHas('produk', function ($qp) use ($search) {
                      $qp->where('nama_produk', 'LIKE', "%{$search}%");
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('shift_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('qc-sistem.verifikasi-berat-produk.index_audit', compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = VerifikasiBeratProduk::with(['plan', 'user', 'shift', 'produk'])
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_produksi', 'like', "%{$search}%")
                      ->orWhereHas('produk', function ($qp) use ($search) {
                          $qp->where('nama_produk', 'like', "%{$search}%");
                      });
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('shift_id', 'asc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'html' => view('qc-sistem.verifikasi-berat-produk._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($records->total() / $perPage),
                    'per_page' => $perPage,
                    'total' => $records->total(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $records->total() > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkExportExcel(Request $request, \App\Services\BulkExport\VerifikasiBeratProdukBulkExportService $service)
    {
        return $service->export($request);
    }
}
