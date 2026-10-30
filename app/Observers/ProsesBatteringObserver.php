<?php

namespace App\Observers;

use App\Models\ProsesBattering;
use App\Models\ProsesBatteringLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProsesBatteringObserver
{
    public function updated(ProsesBattering $prosesBattering)
    {
        // Get original and changed values
        $original = $prosesBattering->getOriginal();
        $changes = $prosesBattering->getChanges();
        
        // Remove timestamps from tracking
        unset($changes['updated_at'], $changes['created_at']);
        
        // Only log if there are actual changes
        if (!empty($changes)) {
            $fieldsChanged = [];
            $oldValues = [];
            $newValues = [];
            
            foreach ($changes as $field => $newValue) {
                $fieldsChanged[] = $field;
                $oldValues[] = $original[$field] ?? null;
                $newValues[] = $newValue;
            }
            
            // Get current user
            $user = Auth::user();
            
            // Create log entry
            ProsesBatteringLog::create([
                'proses_battering_id' => $prosesBattering->id,
                'proses_battering_uuid' => $prosesBattering->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_role' => $user ? $user->role : 'System',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'aksi' => 'update',
                'keterangan' => 'Data proses battering diperbarui',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
        }
    }
}
