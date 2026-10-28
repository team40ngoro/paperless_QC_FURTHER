<?php

namespace App\Http\Controllers;

use App\Models\MasterCip;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterCipController extends Controller
{
    /**
     * Daftar semua tahap CIP (key → label).
     */
    private const STEPS = [
        'ro1'  => 'Rinse Outside 1',
        'ri1'  => 'Rinse Inside 1',
        'ro2'  => 'Rinse Outside 2',
        'ri2'  => 'Rinse Inside 2',
        'hc'   => 'Hot Clean Inside',
        'hci'  => 'Hot Clean Outside',
        'ro3'  => 'Rinse Outside 3',
        'ri3'  => 'Rinse Inside 3',
        'dis'  => 'Disinfection',
        'diso' => 'Disinfection Out',
        'ro4'  => 'Rinse Outside 4',
        'ri4'  => 'Rinse Inside 4',
    ];

    public function index(Request $request)
    {
        $user  = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = MasterCip::with(['plan', 'user'])->orderBy('id_plan')->orderBy('step_key');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('step_label', 'like', "%$search%")
                  ->orWhere('step_key', 'like', "%$search%");
            });
        }

        $masters = $query->paginate($perPage);

        return view('super-admin.master_cip.index', compact('masters', 'search', 'perPage'));
    }

    public function create()
    {
        $user  = Auth::user();
        $plans = $user->role === 'superadmin'
            ? Plan::orderBy('nama_plan')->get()
            : Plan::where('id', $user->id_plan)->get();

        $steps = self::STEPS;
        return view('super-admin.master_cip.create', compact('plans', 'steps'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'step_key'   => 'required|string|max:20',
            'std_waktu'  => 'nullable|numeric|min:0',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        // Cek duplikat (1 step_key per plan)
        $exists = MasterCip::where('id_plan', $planId)
            ->where('step_key', $data['step_key'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['step_key' => 'Data untuk tahap ini sudah ada di plan tersebut. Silakan edit yang sudah ada.'])
                ->withInput();
        }

        MasterCip::create([
            'id_plan'    => $planId,
            'user_id'    => $user->id,
            'step_key'   => $data['step_key'],
            'step_label' => self::STEPS[$data['step_key']] ?? $data['step_key'],
            'std_waktu'  => $data['std_waktu'] ?? null,
        ]);

        return redirect()->route('master-cip.index')
            ->with('success', 'Data Master CIP berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $user   = Auth::user();
        $master = MasterCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $master->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = $user->role === 'superadmin'
            ? Plan::orderBy('nama_plan')->get()
            : Plan::where('id', $user->id_plan)->get();

        $steps = self::STEPS;
        return view('super-admin.master_cip.edit', compact('master', 'plans', 'steps'));
    }

    public function update(Request $request, $uuid)
    {
        $user   = Auth::user();
        $master = MasterCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $master->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $data = $request->validate([
            'std_waktu'  => 'nullable|numeric|min:0',
        ]);

        $master->update($data);

        return redirect()->route('master-cip.index')
            ->with('success', 'Data Master CIP berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user   = Auth::user();
        $master = MasterCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $master->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $master->delete();
        return redirect()->route('master-cip.index')
            ->with('success', 'Data Master CIP berhasil dihapus.');
    }

    /**
     * AJAX: Mengembalikan semua data master CIP untuk id_plan tertentu.
     * Response: { step_key: { std_suhu, std_waktu, step_label }, ... }
     */
    public function getByPlan(Request $request)
    {
        $user   = Auth::user();
        $planId = (int) $request->get('plan_id', $user->id_plan ?? 0);

        if ($user->role !== 'superadmin' && $planId !== (int) $user->id_plan) {
            return response()->json([], 403);
        }

        $records = MasterCip::where('id_plan', $planId)->get();

        $result = [];
        foreach ($records as $rec) {
            $result[$rec->step_key] = [
                'std_waktu'  => $rec->std_waktu,
                'step_label' => $rec->step_label,
            ];
        }

        return response()->json($result);
    }
}
