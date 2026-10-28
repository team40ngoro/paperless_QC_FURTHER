<?php

namespace App\Observers;

use App\Models\PemasakanNasi;
use App\Models\PemasakanNasiLog;

class PemasakanNasiObserver
{
    /**
     * Handle the PemasakanNasi "updated" event.
     */
    public function updated(PemasakanNasi $pemasakanNasi)
    {
        $original = $pemasakanNasi->getOriginal();
        $changes = $pemasakanNasi->getChanges();
        
        // Hapus timestamps dari tracking
        unset($changes['updated_at']);
        
        if (empty($changes)) {
            return;
        }
        
        $fieldsChanged = [];
        $oldValues = [];
        $newValues = [];
        
        foreach ($changes as $field => $newValue) {
            if (isset($original[$field])) {
                // Simpan field asli (bukan yang sudah di-mapping) untuk field_yang_diubah
                $fieldsChanged[] = $field;
                // Simpan nilai asli dengan key field asli untuk nilai_lama dan nilai_baru
                $oldValues[$field] = $original[$field];
                $newValues[$field] = $newValue;
            }
        }
        
        if (!empty($fieldsChanged)) {
            $user = auth()->user();
            
            PemasakanNasiLog::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'pemasakan_nasi_id' => $pemasakanNasi->id,
                'pemasakan_nasi_uuid' => $pemasakanNasi->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_role' => $user ? $user->role : 'system',
                'aksi' => 'update',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Data diperbarui melalui sistem'
            ]);
        }
    }
}
