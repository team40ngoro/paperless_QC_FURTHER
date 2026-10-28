<?php

namespace App\Observers;

use App\Models\PengemasanKarton;
use App\Models\PengemasanKartonLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PengemasanKartonObserver
{
    /**
     * Handle the PengemasanKarton "updated" event.
     *
     * @param  \App\Models\PengemasanKarton  $pengemasanKarton
     * @return void
     */
    public function updated(PengemasanKarton $pengemasanKarton)
    {
        // Get the original (old) values
        $original = $pengemasanKarton->getOriginal();
        
        // Get the current (new) values
        $changes = $pengemasanKarton->getChanges();
        
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
                $oldValues[$field] = $original[$field] ?? null;
                $newValues[$field] = $newValue;
            }
            
            // Create log entry
            PengemasanKartonLog::create([
                'pengemasan_karton_id' => $pengemasanKarton->id,
                'pengemasan_karton_uuid' => $pengemasanKarton->uuid,
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
