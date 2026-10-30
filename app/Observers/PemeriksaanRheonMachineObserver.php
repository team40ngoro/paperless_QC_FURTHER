<?php

namespace App\Observers;

use App\Models\PemeriksaanRheonMachine;
use App\Models\PemeriksaanRheonMachineLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PemeriksaanRheonMachineObserver
{
    /**
     * Handle the PemeriksaanRheonMachine "updated" event.
     */
    public function updated(PemeriksaanRheonMachine $pemeriksaanRheonMachine): void
    {
        // Hanya log jika ada perubahan yang sebenarnya
        if (!$pemeriksaanRheonMachine->wasChanged()) {
            return;
        }

        // Ambil field yang berubah, kecuali updated_at dan created_at
        $changedFields = array_keys($pemeriksaanRheonMachine->getChanges());
        $changedFields = array_filter($changedFields, function($field) {
            return !in_array($field, ['updated_at', 'created_at']);
        });

        if (empty($changedFields)) {
            return;
        }

        // Ambil nilai lama dan baru
        $oldValues = [];
        $newValues = [];
        
        foreach ($changedFields as $field) {
            $oldValues[$field] = $pemeriksaanRheonMachine->getOriginal($field);
            $newValues[$field] = $pemeriksaanRheonMachine->getAttribute($field);
        }

        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userId = $user ? $user->id : null;

        // Buat log entry
        PemeriksaanRheonMachineLog::create([
            'pemeriksaan_rheon_machine_id' => $pemeriksaanRheonMachine->id,
            'pemeriksaan_rheon_machine_uuid' => $pemeriksaanRheonMachine->uuid,
            'user_id' => $userId,
            'user_name' => $userName,
            'aksi' => 'update',
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data pemeriksaan rheon machine diperbarui'
        ]);
    }
}
