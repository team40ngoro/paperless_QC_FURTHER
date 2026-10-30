<?php

namespace App\Observers;

use App\Models\ProdukNonForming;
use App\Models\ProdukNonFormingLog;
use Illuminate\Support\Facades\Auth;

class ProdukNonFormingObserver
{
    /**
     * Handle the ProdukNonForming "updated" event.
     *
     * @param  \App\Models\ProdukNonForming  $produkNonForming
     * @return void
     */
    public function updated(ProdukNonForming $produkNonForming)
    {
        // Get the original attributes before the update
        $original = $produkNonForming->getOriginal();
        $changes = $produkNonForming->getChanges();

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

            ProdukNonFormingLog::create([
                'produk_non_forming_id' => $produkNonForming->id,
                'produk_non_forming_uuid' => $produkNonForming->uuid,
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
