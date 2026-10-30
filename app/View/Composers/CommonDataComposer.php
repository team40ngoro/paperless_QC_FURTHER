<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\InputArea;
use Illuminate\Support\Facades\Cache;

class CommonDataComposer
{
    /**
     * Bind data ke view menggunakan cache agar tidak query DB setiap render.
     */
    public function compose(View $view): void
    {
        $user = auth()->user();

        // Cache shifts selama 10 menit (600 detik)
        $allShifts = Cache::remember('all_shifts', 600, function () {
            return DataShift::orderBy('shift')->get();
        });

        // Shifts per plan user (cache per user id_plan)
        if ($user && $user->role !== 'superadmin' && $user->id_plan) {
            $userShifts = Cache::remember("shifts_plan_{$user->id_plan}", 600, function () use ($user) {
                return DataShift::where('id_plan', $user->id_plan)->orderBy('shift')->get();
            });
        } else {
            $userShifts = $allShifts;
        }

        // Cache produk selama 10 menit
        $allProduk = Cache::remember('all_produk', 600, function () {
            return JenisProduk::orderBy('nama_produk')->get();
        });

        // Produk per plan user
        if ($user && $user->role !== 'superadmin' && $user->id_plan) {
            $userProduk = Cache::remember("produk_plan_{$user->id_plan}", 600, function () use ($user) {
                return JenisProduk::where('id_plan', $user->id_plan)->orderBy('nama_produk')->get();
            });
        } else {
            $userProduk = $allProduk;
        }

        // Cache InputArea selama 30 menit (jarang berubah)
        $allInputArea = Cache::remember('all_input_area', 1800, function () {
            return InputArea::orderBy('id')->get();
        });

        $view->with([
            'cachedShifts'     => $userShifts,
            'cachedAllShifts'  => $allShifts,
            'cachedProduk'     => $userProduk,
            'cachedAllProduk'  => $allProduk,
            'cachedInputArea'  => $allInputArea,
        ]);
    }
}
