<?php

namespace App\Observers;

use App\Models\ProdukYum;
use App\Models\ProdukYumLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProdukYumObserver
{
    /**
     * Handle the ProdukYum "updated" event.
     *
     * @param  \App\Models\ProdukYum  $produkYum
     * @return void
     */
    public function updated(ProdukYum $produkYum)
    {
        // Get the original (old) values
        $original = $produkYum->getOriginal();
        
        // Get the current (new) values
        $changes = $produkYum->getChanges();
        
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
            ProdukYumLog::create([
                'produk_yum_id' => $produkYum->id,
                'produk_yum_uuid' => $produkYum->uuid,
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
