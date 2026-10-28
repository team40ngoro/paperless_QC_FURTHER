<?php

namespace App\Observers;

use App\Models\PembuatanPredust;
use App\Models\PembuatanPredustLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PembuatanPredustObserver
{
    public function updated(PembuatanPredust $pembuatanPredust)
    {
        // Get original and changed values
        $original = $pembuatanPredust->getOriginal();
        $changes = $pembuatanPredust->getChanges();
        
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
            PembuatanPredustLog::create([
                'pembuatan_predust_id' => $pembuatanPredust->id,
                'pembuatan_predust_uuid' => $pembuatanPredust->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_role' => $user ? $user->role : 'System',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'aksi' => 'update',
                'keterangan' => 'Data pembuatan predust diperbarui',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
        }
    }
}
