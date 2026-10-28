<?php

namespace App\Observers;

use App\Models\ProsesTwahing;
use App\Models\ProsesTwahingLog;

class ProsesTwahingObserver
{
    /**
     * Handle the ProsesTwahing "updated" event.
     */
    public function updated(ProsesTwahing $prosesTwahing)
    {
        $original = $prosesTwahing->getOriginal();
        $changes = $prosesTwahing->getChanges();
        
        // Hapus timestamps dari tracking
        unset($changes['updated_at']);
        
        if (empty($changes)) {
            return;
        }
        
        $fieldsChanged = [];
        $oldValues = [];
        $newValues = [];
        
        foreach ($changes as $field => $newValue) {
            if (array_key_exists($field, $original)) {
                // Simpan field asli
                $fieldsChanged[] = $field;
                // Simpan nilai asli
                $oldValues[$field] = $original[$field];
                $newValues[$field] = $newValue;
            }
        }
        
        if (!empty($fieldsChanged)) {
            $user = auth()->user();
            
            ProsesTwahingLog::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'proses_twahing_id' => $prosesTwahing->id,
                'proses_twahing_uuid' => $prosesTwahing->uuid,
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
