<?php

namespace App\Http\Controllers;

use App\Models\BagianMesin;
use App\Models\InputArea;
use App\Models\InputMesinPeralatan;
use Illuminate\Http\Request;

class InputMesinPeralatanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $areas = InputArea::query()
            ->forUser($user)
            ->orderBy('area')
            ->get();

        $selectedAreaId = $request->get('area_id');

        $query = InputMesinPeralatan::with(['plan', 'user', 'area', 'bagian'])
            ->forUser($user)
            ->orderBy('id_area')
            ->orderBy('nama_mesin');

        if (!empty($selectedAreaId)) {
            $query->where('id_area', $selectedAreaId);
        }

        $items = $query->paginate(10);

        $itemsByArea = $items->groupBy('id_area');

        return view('super-admin.input_mesin_peralatan.index', compact('items', 'areas', 'selectedAreaId', 'itemsByArea'));
    }

    public function create()
    {
        $user = auth()->user();

        $areas = InputArea::query()
            ->forUser($user)
            ->orderBy('area')
            ->get();

        return view('super-admin.input_mesin_peralatan.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'id_area' => 'required|exists:input_area,id',
            'jenis_produk' => 'nullable|array',
            'jenis_produk.*' => 'in:Forming,Non-Forming',
            'nama_mesin' => 'required|array|min:1',
            'nama_mesin.*' => 'required|string|max:255',
            'bagian' => 'nullable|array',
            'bagian.*' => 'nullable|array',
            'bagian.*.*' => 'nullable|string|max:255',
        ]);

        $area = InputArea::findOrFail($data['id_area']);

        if ($user->role !== 'superadmin' && (int) $area->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $namaMesinList = array_values(array_filter(array_map(function ($val) {
            return is_string($val) ? trim($val) : '';
        }, $data['nama_mesin']), function ($val) {
            return $val !== '';
        }));

        if (count($namaMesinList) === 0) {
            return back()->withErrors(['nama_mesin' => 'Nama mesin wajib diisi minimal 1.'])->withInput();
        }

        foreach ($namaMesinList as $index => $namaMesin) {
            $mesin = InputMesinPeralatan::create([
                'id_plan' => $area->id_plan,
                'user_id' => $user->id,
                'id_area' => $area->id,
                'nama_mesin' => $namaMesin,
                'jenis_produk' => $request->jenis_produk ?: null,
            ]);

            if (!empty($data['bagian'][$index])) {
                $bagianList = array_values(array_filter(array_map(function ($val) {
                    return is_string($val) ? trim($val) : '';
                }, $data['bagian'][$index]), function ($val) {
                    return $val !== '';
                }));

                foreach ($bagianList as $namaBagian) {
                    BagianMesin::create([
                        'id_mesin' => $mesin->id,
                        'nama_bagian' => $namaBagian,
                    ]);
                }
            }
        }

        return redirect()->route('input-mesin-peralatan.index')->with('success', 'Data mesin/peralatan berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $user = auth()->user();

        $inputMesinPeralatan = InputMesinPeralatan::with('bagian')->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $inputMesinPeralatan->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $areas = InputArea::query()
            ->forUser($user)
            ->orderBy('area')
            ->get();

        return view('super-admin.input_mesin_peralatan.edit', [
            'item' => $inputMesinPeralatan,
            'areas' => $areas,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();

        $inputMesinPeralatan = InputMesinPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $inputMesinPeralatan->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $data = $request->validate([
            'id_area' => 'required|exists:input_area,id',
            'jenis_produk' => 'nullable|array',
            'jenis_produk.*' => 'in:Forming,Non-Forming',
            'nama_mesin' => 'required|string|max:255',
            'bagian' => 'nullable|array',
            'bagian.*' => 'nullable|string|max:255',
        ]);

        $area = InputArea::findOrFail($data['id_area']);

        if ($user->role !== 'superadmin' && (int) $area->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $inputMesinPeralatan->update([
            'id_area' => $area->id,
            'id_plan' => $area->id_plan,
            'nama_mesin' => $data['nama_mesin'],
            'jenis_produk' => $request->jenis_produk ?: null,
        ]);

        $bagianList = array_values(array_filter(array_map(function ($val) {
            return is_string($val) ? trim($val) : '';
        }, $data['bagian'] ?? []), function ($val) {
            return $val !== '';
        }));

        $inputMesinPeralatan->bagian()->delete();

        foreach ($bagianList as $namaBagian) {
            BagianMesin::create([
                'id_mesin' => $inputMesinPeralatan->id,
                'nama_bagian' => $namaBagian,
            ]);
        }

        return redirect()->route('input-mesin-peralatan.index')->with('success', 'Data mesin/peralatan berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $user = auth()->user();

        $inputMesinPeralatan = InputMesinPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $inputMesinPeralatan->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $inputMesinPeralatan->delete();

        return redirect()->route('input-mesin-peralatan.index')->with('success', 'Data mesin/peralatan berhasil dihapus.');
    }
}
