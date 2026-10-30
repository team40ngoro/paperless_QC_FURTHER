<?php

namespace App\Observers;

use App\Models\HasilPenggorengan;
use App\Models\HasilPenggorenganLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class HasilPenggorenganObserver
{
    /**
     * Handle the HasilPenggorengan "updated" event.
     *
     * @param  \App\Models\HasilPenggorengan  $hasilPenggorengan
     * @return void
     */
    public function updated(HasilPenggorengan $hasilPenggorengan)
    {
        // Get the original (old) values
        $original = $hasilPenggorengan->getOriginal();
        
        // Get the current (new) values
        $changes = $hasilPenggorengan->getChanges();
        
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
            HasilPenggorenganLog::create([
                'hasil_penggorengan_id' => $hasilPenggorengan->id,
                'hasil_penggorengan_uuid' => $hasilPenggorengan->uuid,
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
