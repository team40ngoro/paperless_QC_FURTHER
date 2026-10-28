<?php

namespace App\Observers;

use App\Models\Seasoning;
use App\Models\SeasoningLog;
use Illuminate\Support\Str;

class SeasoningObserver
{
    /**
     * Handle the Seasoning "updated" event.
     *
     * @param  \App\Models\Seasoning  $seasoning
     * @return void
     */
    public function updated(Seasoning $seasoning)
    {
        
        $changes = $seasoning->getChanges();
        $original = $seasoning->getOriginal();

        // Filter out timestamp fields - only log actual data changes
        unset($changes['updated_at'], $changes['created_at']);

        if (!empty($changes)) {
            $fieldNames = SeasoningLog::getFieldNames();
            $fieldsChanged = [];
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $field => $newValue) {
                if (isset($original[$field]) && $original[$field] != $newValue) {
                    $fieldsChanged[] = $field; // Store field name, not display name
                    $oldValues[$field] = $original[$field]; // Use field as key
                    $newValues[$field] = $newValue; // Use field as key
                }
            }

            if (!empty($fieldsChanged)) {
                SeasoningLog::create([
                    'uuid' => Str::uuid(),
                    'seasoning_id' => $seasoning->id,
                    'seasoning_uuid' => $seasoning->uuid,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? 'System',
                    'user_role' => auth()->user()->role ?? 'System',
                    'aksi' => 'UPDATE',
                    'field_yang_diubah' => $fieldsChanged,
                    'nilai_lama' => $oldValues,
                    'nilai_baru' => $newValues,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'keterangan' => 'Data seasoning diperbarui'
                ]);
            }
        }
    }
}
