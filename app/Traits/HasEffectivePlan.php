<?php

namespace App\Traits;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

trait HasEffectivePlan
{
    /**
     * Dapatkan ID plan yang sedang aktif (untuk query filter)
     */
    protected function getActivePlanId($user = null): ?int
    {
        $user = $user ?? Auth::user();
        return $user ? $user->getEffectivePlanId() : null;
    }

    /**
     * Dapatkan objek Plan yang sedang aktif
     */
    protected function getActivePlan($user = null): ?Plan
    {
        $user = $user ?? Auth::user();
        return $user ? $user->getEffectivePlan() : null;
    }

    /**
     * Cek apakah user yang login adalah Manager
     */
    protected function isManagerUser($user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user ? $user->isManager() : false;
    }
}
