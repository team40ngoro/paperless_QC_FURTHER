<?php

namespace App\Http\Controllers;

use App\Models\JenisEmulsi;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class JenisEmulsiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = JenisEmulsi::with(['plan', 'user', 'produk']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->get();
        return view('super-admin.jenis_emulsi.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produk = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_emulsi.create', compact('plans', 'produk'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input array
            $request->validate([
                'id_plan' => 'required|exists:plan,id',
                'id_produk' => 'required|exists:jenis_produk,id',
                'nama_emulsi' => 'required|array|min:1',
                'nama_emulsi.*' => 'required|string|max:255',
            ], [
                'nama_emulsi.required' => 'Minimal harus ada 1 nama emulsi.',
                'nama_emulsi.array' => 'Format nama emulsi tidak valid.',
                'nama_emulsi.min' => 'Minimal harus ada 1 nama emulsi.',
                'nama_emulsi.*.required' => 'Nama emulsi tidak boleh kosong.',
                'nama_emulsi.*.string' => 'Nama emulsi harus berupa teks.',
                'nama_emulsi.*.max' => 'Nama emulsi maksimal 255 karakter.',
            ]);

            $user_id = auth()->id();
            $created_count = 0;
            $failed_count = 0;
            $duplicate_emulsi = [];

            // Loop untuk setiap nama emulsi
            foreach ($request->nama_emulsi as $index => $emulsi) {
                $trimmed_emulsi = trim($emulsi);
                
                if (!empty($trimmed_emulsi)) {
                    // Cek duplikasi
                    $existing = JenisEmulsi::where('id_plan', $request->id_plan)
                        ->where('id_produk', $request->id_produk)
                        ->where('nama_emulsi', $trimmed_emulsi)
                        ->first();
                    
                    if ($existing) {
                        $duplicate_emulsi[] = $trimmed_emulsi;
                        $failed_count++;
                        continue;
                    }

                    $data = [
                        'uuid' => (string) Str::uuid(),
                        'id_plan' => $request->id_plan,
                        'id_produk' => $request->id_produk,
                        'nama_emulsi' => $trimmed_emulsi,
                        'user_id' => $user_id,
                    ];
                    
                    JenisEmulsi::create($data);
                    $created_count++;
                } else {
                    $failed_count++;
                }
            }

            // Prepare success message
            $message = "Berhasil menambahkan {$created_count} Jenis Emulsi";
            
            if ($failed_count > 0) {
                $message .= ", {$failed_count} gagal";
                if (!empty($duplicate_emulsi)) {
                    $message .= " (duplikat: " . implode(', ', $duplicate_emulsi) . ")";
                }
            }

            return redirect()->route('jenis-emulsi.index')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('JenisEmulsi Store Error:', [
                'message' => $e->getMessage(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($uuid)
    {
        $item = JenisEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produk = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_emulsi.edit', compact('item', 'plans', 'produk'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_emulsi' => 'required|string',
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
           // 'nama_produk_id' => 'required|exists:jenis_produk,id',
        ]);
        $item = JenisEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'nama_emulsi' => $request->nama_emulsi,
            'id_plan' => $request->id_plan,
            'id_produk' => $request->id_produk,
          //  'nama_produk_id' => $request->nama_produk_id,
        ]);
        return redirect()->route('jenis-emulsi.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $jenis_emulsi = JenisEmulsi::where('uuid', $uuid)->firstOrFail();
        
        // Cek apakah ada data Total Pemakaian Emulsi yang terkait
        if ($jenis_emulsi->totalPemakaian()->exists()) {
            return redirect()->route('jenis-emulsi.index')
                ->with('error', 'Tidak bisa menghapus! Ada ' . $jenis_emulsi->totalPemakaian()->count() . ' data Total Pemakaian Emulsi yang terkait dengan Jenis Emulsi ini.');
        }
        
        $jenis_emulsi->delete();

        return redirect()->route('jenis-emulsi.index')->with('success', 'Jenis emulsi berhasil dihapus');
    }
    public function getProdukByPlan($plan_id)
{
    $produk = JenisProduk::where('id_plan', $plan_id)->get(['id', 'nama_produk']);
   
    return response()->json($produk);
}
}
