<?php

namespace App\Observers;

use App\Models\BahanBakuRoasting;
use App\Models\BahanBakuRoastingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BahanBakuRoastingObserver
{
    /**
     * Handle the BahanBakuRoasting "updated" event.
     *
     * @param  \App\Models\BahanBakuRoasting  $bahanBakuRoasting
     * @return void
     */
    public function updated(BahanBakuRoasting $bahanBakuRoasting)
    {
        // Get the original (old) values
        $original = $bahanBakuRoasting->getOriginal();
        
        // Get the current (new) values
        $changes = $bahanBakuRoasting->getChanges();
        
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
            BahanBakuRoastingLog::create([
                'bahan_baku_roasting_id' => $bahanBakuRoasting->id,
                'bahan_baku_roasting_uuid' => $bahanBakuRoasting->uuid,
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
