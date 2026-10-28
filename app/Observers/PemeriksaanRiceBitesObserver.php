<?php

namespace App\Observers;

use App\Models\PemeriksaanRiceBites;
use App\Models\PemeriksaanRiceBitesLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PemeriksaanRiceBitesObserver
{
    /**
     * Handle the PemeriksaanRiceBites "updated" event.
     */
    public function updated(PemeriksaanRiceBites $pemeriksaanRiceBites): void
    {
        // Hanya log jika ada perubahan yang sebenarnya
        if (!$pemeriksaanRiceBites->wasChanged()) {
            return;
        }

        // Ambil field yang berubah, kecuali updated_at dan created_at
        $changedFields = array_keys($pemeriksaanRiceBites->getChanges());
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
            $oldValues[$field] = $pemeriksaanRiceBites->getOriginal($field);
            $newValues[$field] = $pemeriksaanRiceBites->getAttribute($field);
        }

        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userId = $user ? $user->id : null;

        // Buat log entry
        PemeriksaanRiceBitesLog::create([
            'pemeriksaan_rice_bites_id' => $pemeriksaanRiceBites->id,
            'pemeriksaan_rice_bites_uuid' => $pemeriksaanRiceBites->uuid,
            'user_id' => $userId,
            'user_name' => $userName,
            'aksi' => 'update',
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data pemeriksaan rice bites diperbarui'
        ]);
    }
}
