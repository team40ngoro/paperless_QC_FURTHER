<?php

namespace App\Observers;

use App\Models\ProsesRoastingFan;
use App\Models\ProsesRoastingFanLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProsesRoastingFanObserver
{
    /**
     * Handle the ProsesRoastingFan "updated" event.
     *
     * @param  \App\Models\ProsesRoastingFan  $prosesRoastingFan
     * @return void
     */
    public function updated(ProsesRoastingFan $prosesRoastingFan)
    {
        // Get the original (old) values
        $original = $prosesRoastingFan->getOriginal();
        
        // Get the current (new) values
        $changes = $prosesRoastingFan->getChanges();
        
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
            ProsesRoastingFanLog::create([
                'proses_roasting_fan_id' => $prosesRoastingFan->id,
                'proses_roasting_fan_uuid' => $prosesRoastingFan->uuid,
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
