<?php

namespace App\Http\Controllers;

use App\Models\InputArea;
use App\Models\Plan;
use App\Models\SubArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Role-based data filtering
        if ($user->role === 'superadmin') {
            $inputAreas = InputArea::with(['plan', 'user', 'subarea'])->get();
            $plans = Plan::all();
        } else {
            $inputAreas = InputArea::with(['plan', 'user', 'subarea'])
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.input_area.index', compact('inputAreas', 'plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get plans based on user role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.input_area.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'area' => 'required|string|max:255',
            'subarea.*' => 'nullable|string|max:255',
        ]);

        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            $request->validate([
                'id_plan' => 'in:' . $user->id_plan,
            ], [
                'id_plan.in' => 'Anda tidak memiliki akses untuk plan tersebut.',
            ]);
        }

        // Handle nullable subarea
        $subareas = $request->subarea;
        if (is_array($subareas) && count($subareas) === 1 && $subareas[0] === null) {
            $request->request->remove('subarea');
        }

        $inputArea = InputArea::create([
            'id_plan' => $request->id_plan,
            'user_id' => $user->id, // Automatically set from logged-in user
            'area' => $request->area,
        ]);

        if ($request->has('subarea')) {
            foreach ($request->subarea as $subareaName) {
                if (!empty($subareaName)) {
                    SubArea::create([
                        'id_input_area' => $inputArea->id,
                        'lokasi_area' => $subareaName,
                    ]);
                }
            }
        }

        return redirect()->route('input-area.index')
            ->with('success', 'Data area berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        $inputArea = InputArea::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $inputArea->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('super-admin.input_area.show', compact('inputArea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $inputArea = InputArea::with('subarea')->where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $inputArea->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get plans based on user role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.input_area.edit', compact('inputArea', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $inputArea = InputArea::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $inputArea->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'area' => 'required|string|max:255',
            'subarea_name.*' => 'nullable|string|max:255',
            'new_subarea.*' => 'nullable|string|max:255',
        ]);

        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            $request->validate([
                'id_plan' => 'in:' . $user->id_plan,
            ], [
                'id_plan.in' => 'Anda tidak memiliki akses untuk plan tersebut.',
            ]);
        }

        $inputArea->update([
            'id_plan' => $request->id_plan,
            'area' => $request->area,
        ]);

        // Update existing subareas
        if ($request->has('subarea_id')) {
            foreach ($request->subarea_id as $index => $id) {
                $subArea = SubArea::find($id);
                if ($subArea) {
                    $subArea->update(['lokasi_area' => $request->subarea_name[$index]]);
                }
            }
        }

        // Create new subareas
        if ($request->has('new_subarea')) {
            foreach ($request->new_subarea as $subareaName) {
                if (!empty($subareaName)) {
                    SubArea::create([
                        'id_input_area' => $inputArea->id,
                        'lokasi_area' => $subareaName,
                    ]);
                }
            }
        }

        // Delete subareas
        if ($request->has('delete_subarea')) {
            SubArea::destroy($request->delete_subarea);
        }

        return redirect()->route('input-area.index')
            ->with('success', 'Data area berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $inputArea = InputArea::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $inputArea->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $inputArea->delete();

        return redirect()->route('input-area.index')
            ->with('success', 'Data area berhasil dihapus.');
    }
}
