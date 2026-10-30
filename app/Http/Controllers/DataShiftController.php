<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

class DataShiftController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = DataShift::with(['plan', 'user']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get();
        return view('super-admin.data_shift.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $users = User::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $users = User::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.data_shift.create', compact('plans', 'users'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'shift' => 'required|string',
        ]);

        
        DataShift::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'id_plan' => $request->id_plan,
            'user_id' => $user->id, // Ambil dari user login
            'shift' => $request->shift,
        ]);
        return redirect()->route('data-shift.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $item = DataShift::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $users = User::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $users = User::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.data_shift.edit', compact('item', 'plans', 'users'));
    }

    public function update(Request $request, $uuid)
    {
        $item = DataShift::where('uuid', $uuid)->firstOrFail();
      
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
          //  'user_id' => 'required|exists:users,id',
            'shift' => 'required|string',
        ]);
     
        $item->update([
            'id_plan' => $request->id_plan,
          //  'user_id' => $request->user_id,
            'user_id' => $item->user_id, // Tetap gunakan user_id dari item yang ada
            'shift' => $request->shift,
        ]);
        return redirect()->route('data-shift.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = DataShift::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('data-shift.index')->with('success', 'Data berhasil dihapus');
    }
}
