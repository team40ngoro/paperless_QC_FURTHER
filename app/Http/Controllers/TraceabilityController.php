<?php

namespace App\Http\Controllers;

use App\Services\TraceabilityService;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    /**
     * Tampilkan halaman utama Traceability.
     */
    public function index()
    {
        return view('traceability.index');
    }

    /**
     * Lakukan pencarian batch/kode produksi lintas modul.
     */
    public function search(Request $request, TraceabilityService $service)
    {
        $request->validate([
            'production_code' => 'required|string|min:2'
        ], [
            'production_code.required' => 'Kata kunci pencarian harus diisi.',
            'production_code.min'      => 'Kata kunci minimal 2 karakter.'
        ]);

        $productionCode = $request->input('production_code');
        $data = $service->traceByBatch($productionCode);

        return view('traceability.index', [
            'results'         => $data['modules'],
            'process_flow'    => $data['process_flow'],
            'summary'         => $data['summary'],
            'production_code' => $productionCode,
        ]);
    }

    /**
     * Endpoint AJAX untuk memuat detail satu form saat kartu dibuka (Lazy Load).
     */
    public function formDetails(Request $request, TraceabilityService $service)
    {
        $request->validate([
            'module'   => 'required|string',
            'form_key' => 'required|string',
            'search'   => 'required|string|min:2',
        ]);

        return response()->json(
            $service->getFormDetails(
                $request->input('module'),
                $request->input('form_key'),
                $request->input('search')
            )
        );
    }

    /**
     * Endpoint AJAX untuk pagination per modul.
     */
    public function paginateModule(Request $request, TraceabilityService $service)
    {
        $request->validate([
            'module'  => 'required|string',
            'search'  => 'required|string|min:2',
            'page'    => 'required|integer|min:1',
        ]);

        $result = $service->paginateModuleForms(
            $request->input('module'),
            $request->input('search'),
            $request->input('page', 1)
        );

        if ($result === null) {
            return response()->json(['error' => 'Modul tidak ditemukan.'], 404);
        }

        return response()->json($result);
    }
}

