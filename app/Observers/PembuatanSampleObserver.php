<?php

namespace App\Observers;

use App\Models\PembuatanSample;
use App\Models\PembuatanSampleLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PembuatanSampleObserver
{
    /**
     * Handle the PembuatanSample "updated" event.
     */
    public function updated(PembuatanSample $pembuatanSample): void
    {
        // Hanya log jika ada perubahan yang sebenarnya
        if (!$pembuatanSample->wasChanged()) {
            return;
        }

        // Ambil field yang berubah, kecuali updated_at dan created_at
        $changedFields = array_keys($pembuatanSample->getChanges());
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
            $oldValues[$field] = $pembuatanSample->getOriginal($field);
            $newValues[$field] = $pembuatanSample->getAttribute($field);
        }

        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userId = $user ? $user->id : null;

        // Buat log entry
        PembuatanSampleLog::create([
            'pembuatan_sample_id' => $pembuatanSample->id,
            'pembuatan_sample_uuid' => $pembuatanSample->uuid,
            'user_id' => $userId,
            'user_name' => $userName,
            'aksi' => 'update',
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data pembuatan sample diperbarui'
        ]);
    }
}
