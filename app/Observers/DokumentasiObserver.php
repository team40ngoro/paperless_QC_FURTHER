<?php

namespace App\Observers;

use App\Models\Dokumentasi;
use App\Models\DokumentasiLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DokumentasiObserver
{
    /**
     * Handle the Dokumentasi "updated" event.
     */
    public function updated(Dokumentasi $dokumentasi): void
    {
        // Hanya log jika ada perubahan yang sebenarnya
        if (!$dokumentasi->wasChanged()) {
            return;
        }

        // Ambil field yang berubah, kecuali updated_at dan created_at
        $changedFields = array_keys($dokumentasi->getChanges());
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
            $oldValues[$field] = $dokumentasi->getOriginal($field);
            $newValues[$field] = $dokumentasi->getAttribute($field);
        }

        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        // $userRole = $user && $user->role ? $user->role->nama_role : 'Unknown';
        $userId = $user ? $user->id : null;

        // Buat log entry
        DokumentasiLog::create([
            'dokumentasi_id' => $dokumentasi->id,
            'dokumentasi_uuid' => $dokumentasi->uuid,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'user_role' => $user ? $user->role : 'system',
            'aksi' => 'update',
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data dokumentasi diperbarui'
        ]);
    }
}
