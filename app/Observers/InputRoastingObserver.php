<?php

namespace App\Observers;

use App\Models\InputRoasting;
use App\Models\InputRoastingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class InputRoastingObserver
{
    /**
     * Handle the InputRoasting "updated" event.
     *
     * @param  \App\Models\InputRoasting  $inputRoasting
     * @return void
     */
    public function updated(InputRoasting $inputRoasting)
    {
        // Get the original (old) values
        $original = $inputRoasting->getOriginal();
        
        // Get the current (new) values
        $changes = $inputRoasting->getChanges();
        
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
            InputRoastingLog::create([
                'input_roasting_id' => $inputRoasting->id,
                'input_roasting_uuid' => $inputRoasting->uuid,
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
