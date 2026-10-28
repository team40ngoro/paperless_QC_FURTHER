<?php

namespace App\Observers;

use App\Models\PembekuanIqfPenggorengan;
use App\Models\PembekuanIqfPenggorenganLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PembekuanIqfPenggorenganObserver
{
    /**
     * Handle the PembekuanIqfPenggorengan "updated" event.
     *
     * @param  \App\Models\PembekuanIqfPenggorengan  $pembekuanIqfPenggorengan
     * @return void
     */
    public function updated(PembekuanIqfPenggorengan $pembekuanIqfPenggorengan)
    {
        // Get the original (old) values
        $original = $pembekuanIqfPenggorengan->getOriginal();
        
        // Get the current (new) values
        $changes = $pembekuanIqfPenggorengan->getChanges();
        
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
            
            // Create log entry
            PembekuanIqfPenggorenganLog::create([
                'pembekuan_iqf_penggorengan_id' => $pembekuanIqfPenggorengan->id,
                'pembekuan_iqf_penggorengan_uuid' => $pembekuanIqfPenggorengan->uuid,
                'user_id' => Auth::id(),
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
        }
    }
}
