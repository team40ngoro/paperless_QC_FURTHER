<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuhuAdonan;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SuhuAdonanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $data = SuhuAdonan::with(['produk', 'plan', 'user'])->get();
        } else {
            $data = SuhuAdonan::with(['produk', 'plan', 'user'])
                ->where('id_plan', $user->id_plan)
                ->get();
        }
        return view('super-admin.suhu_adonan.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.suhu_adonan.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
          //  'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_suhu' => 'required|string',
        ]);

        $data = $request->all();
        
        $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;
        $data['uuid'] = Str::uuid();

        SuhuAdonan::create($data);
        return redirect()->route('suhu-adonan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $item = SuhuAdonan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.suhu_adonan.edit', compact('item', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $item = SuhuAdonan::where('uuid', $uuid)->firstOrFail();
       
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $request->validate([
           // 'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_suhu' => 'required|string',
        ]);

        $data = $request->except('user_id');
       
        $data['id_plan'] = $user->id_plan;

        $item->update($data);
        return redirect()->route('suhu-adonan.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = SuhuAdonan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();
        return redirect()->route('suhu-adonan.index')->with('success', 'Data berhasil dihapus');
    }
}