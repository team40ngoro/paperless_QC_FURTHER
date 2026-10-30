<?php

namespace App\Observers;

use App\Models\ProsesBreader;
use App\Models\ProsesBreaderLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProsesBreaderObserver
{
    /**
     * Handle the ProsesBreader "updated" event.
     *
     * @param  \App\Models\ProsesBreader  $prosesBreader
     * @return void
     */
    public function updated(ProsesBreader $prosesBreader)
    {
        // Get the original (old) values
        $original = $prosesBreader->getOriginal();
        
        // Get the current (new) values
        $changes = $prosesBreader->getChanges();
        
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
            ProsesBreaderLog::create([
                'proses_breader_id' => $prosesBreader->id,
                'proses_breader_uuid' => $prosesBreader->uuid,
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
