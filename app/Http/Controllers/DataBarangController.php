<?php

namespace App\Http\Controllers;

use App\Models\DataBarang;
use App\Models\InputArea;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = DataBarang::with(['plan', 'user', 'area']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Filter by area
        $selectedAreaId = $request->get('id_area');
        if ($selectedAreaId) {
            $query->where('id_area', $selectedAreaId);
        }
        
        // Search by nama_barang
        $search = $request->get('search');
        if ($search) {
            $query->where('nama_barang', 'LIKE', '%' . $search . '%');
        }
        
        $dataBarang = $query->orderBy('id_area')->orderBy('id')->get();
        $groupedCollection = $dataBarang->groupBy('id_area');
        
        $perPage = 4;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $groupedCollection->slice(($currentPage - 1) * $perPage, $perPage);
        
        $groupedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $groupedCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        $plans = Plan::when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('id', $user->id_plan);
        })->get();
        
        $areas = InputArea::when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        })->orderBy('area')->get();
        
        return view('super-admin.data_barang.index', compact('dataBarang', 'groupedData', 'plans', 'areas', 'selectedAreaId', 'search'));
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

        $areas = InputArea::forUser($user)->orderBy('area')->get();

        return view('super-admin.data_barang.create', compact('plans', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_area' => 'required|exists:input_area,id',
            'nama_barang' => 'required|array|min:1',
            'nama_barang.*' => 'required|string|max:255',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:0',
        ]);

        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            $request->validate([
                'id_plan' => 'in:' . $user->id_plan,
            ], [
                'id_plan.in' => 'Anda tidak memiliki akses untuk plan tersebut.',
            ]);
        }

        $selectedArea = InputArea::findOrFail($request->id_area);
        if ((int) $selectedArea->id_plan !== (int) $request->id_plan) {
            return back()->withErrors(['id_area' => 'Area tidak sesuai dengan plan yang dipilih.'])->withInput();
        }
        if ($user->role !== 'superadmin' && (int) $selectedArea->id_plan !== (int) $user->id_plan) {
            return back()->withErrors(['id_area' => 'Anda tidak memiliki akses untuk area tersebut.'])->withInput();
        }

        $createdCount = 0;
        $duplicateCount = 0;
        $duplicateItems = [];

        foreach ($request->nama_barang as $index => $namaBarang) {
            $namaBarang = trim($namaBarang);
            
            if (empty($namaBarang)) {
                continue;
            }

            // Check if item already exists for this plan
            $exists = DataBarang::where('id_plan', $request->id_plan)
                                ->where('id_area', $request->id_area)
                                ->where('nama_barang', $namaBarang)
                                ->exists();

            if ($exists) {
                $duplicateCount++;
                $duplicateItems[] = $namaBarang;
                continue;
            }

            DataBarang::create([
                'id_plan' => $request->id_plan,
                'id_area' => $request->id_area,
                'user_id' => $user->id,
                'nama_barang' => $namaBarang,
                'jumlah' => $request->jumlah[$index] ?? 0,
            ]);

            $createdCount++;
        }

        $message = '';
        if ($createdCount > 0) {
            $message .= "Berhasil menambahkan {$createdCount} data barang.";
        }
        
        if ($duplicateCount > 0) {
            $message .= " {$duplicateCount} data sudah ada sebelumnya: " . implode(', ', $duplicateItems);
        }

        return redirect()->route('data-barang.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataBarang $dataBarang)
    {
        $user = Auth::user();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $dataBarang->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('super-admin.data_barang.show', compact('dataBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $dataBarang = DataBarang::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        $areas = InputArea::forUser($user)->orderBy('area')->get();
        return view('super-admin.data_barang.edit', compact('dataBarang', 'plans', 'areas'));
    }

    public function update(Request $request, $uuid)
    {
        $dataBarang = DataBarang::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $validatedData = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_area' => 'required|exists:input_area,id',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($user->role !== 'superadmin' && (int) $validatedData['id_plan'] !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk plan tersebut.');
        }

        $selectedArea = InputArea::findOrFail($validatedData['id_area']);
        if ((int) $selectedArea->id_plan !== (int) $validatedData['id_plan']) {
            return back()->withErrors(['id_area' => 'Area tidak sesuai dengan plan yang dipilih.'])->withInput();
        }
        if ($user->role !== 'superadmin' && (int) $selectedArea->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk area tersebut.');
        }
        
        $dataBarang->update($validatedData);

        return redirect()->route('data-barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $dataBarang = DataBarang::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $dataBarang->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $dataBarang->delete();

        return redirect()->route('data-barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }

    public function getJumlahById($id)
    {
        $barang = DataBarang::find($id);
        if ($barang) {
            return response()->json(['jumlah' => $barang->jumlah]);
        }
        return response()->json(['jumlah' => 0]);
    }
}
