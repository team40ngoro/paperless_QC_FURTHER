<?php

namespace App\Observers;

use App\Models\BeratProdukBag;
use App\Models\BeratProdukBagLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BeratProdukBagObserver
{
    /**
     * Handle the BeratProdukBag "updated" event.
     *
     * @param  \App\Models\BeratProdukBag  $beratProdukBag
     * @return void
     */
    public function updated(BeratProdukBag $beratProdukBag)
    {
        // Get the original (old) values
        $original = $beratProdukBag->getOriginal();
        
        // Get the current (new) values
        $changes = $beratProdukBag->getChanges();
        
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
            BeratProdukBagLog::create([
                'berat_produk_bag_id' => $beratProdukBag->id,
                'berat_produk_bag_uuid' => $beratProdukBag->uuid,
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
