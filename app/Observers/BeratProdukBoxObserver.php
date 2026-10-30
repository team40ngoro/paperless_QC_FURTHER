<?php

namespace App\Observers;

use App\Models\BeratProdukBox;
use App\Models\BeratProdukBoxLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BeratProdukBoxObserver
{
    /**
     * Handle the BeratProdukBox "updated" event.
     *
     * @param  \App\Models\BeratProdukBox  $beratProdukBox
     * @return void
     */
    public function updated(BeratProdukBox $beratProdukBox)
    {
        // Get the original (old) values
        $original = $beratProdukBox->getOriginal();
        
        // Get the current (new) values
        $changes = $beratProdukBox->getChanges();
        
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
            BeratProdukBoxLog::create([
                'berat_produk_box_id' => $beratProdukBox->id,
                'berat_produk_box_uuid' => $beratProdukBox->uuid,
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
