<?php

namespace App\Observers;

use App\Models\Penggorengan;
use App\Models\PenggorenganLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PenggorenganObserver
{
    /**
     * Handle the Penggorengan "updated" event.
     *
     * @param  \App\Models\Penggorengan  $penggorengan
     * @return void
     */
    public function updated(Penggorengan $penggorengan)
    {
        // Get the original (old) values
        $original = $penggorengan->getOriginal();
        
        // Get the current (new) values
        $changes = $penggorengan->getChanges();
        
        // Remove timestamps from tracking
        unset($changes['updated_at']);
        unset($changes['created_at']);
        
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
            PenggorenganLog::create([
                'penggorengan_id' => $penggorengan->id,
                'penggorengan_uuid' => $penggorengan->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'aksi' => 'update',
                'keterangan' => 'Data penggorengan diperbarui',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
        }
    }
}
