<?php

namespace App\Observers;

use App\Models\ProdukForming;
use App\Models\ProdukFormingLog;
use Illuminate\Support\Facades\Auth;

class ProdukFormingObserver
{
    /**
     * Handle the ProdukForming "updated" event.
     *
     * @param  \App\Models\ProdukForming  $produkForming
     * @return void
     */
    public function updated(ProdukForming $produkForming)
    {
        // Get the original attributes before the update
        $original = $produkForming->getOriginal();
        $changes = $produkForming->getChanges();

        // Remove timestamps and other fields we don't want to log
        unset($changes['updated_at']);
        unset($original['updated_at']);

        if (!empty($changes)) {
            $fieldsChanged = [];
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $field => $newValue) {
                $fieldsChanged[] = $field;
                $oldValues[] = $original[$field] ?? null;
                $newValues[] = $newValue;
            }

            ProdukFormingLog::create([
                'produk_forming_id' => $produkForming->id,
                'produk_forming_uuid' => $produkForming->uuid,
                'user_id' => Auth::id(),
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }
}
