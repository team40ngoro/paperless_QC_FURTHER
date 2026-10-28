<?php

namespace App\Observers;

use App\Models\Rebox;
use App\Models\ReboxLog;
use Illuminate\Support\Str;

class ReboxObserver
{
    /**
     * Handle the Rebox "updated" event.
     *
     * @param  \App\Models\Rebox  $rebox
     * @return void
     */
    public function updated(Rebox $rebox)
    {
        $changes = $rebox->getChanges();
        $original = $rebox->getOriginal();

        // Filter out timestamp fields - only log actual data changes
        unset($changes['updated_at'], $changes['created_at']);

        if (!empty($changes)) {
            $fieldNames = ReboxLog::getFieldNames();
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
                ReboxLog::create([
                    'uuid' => Str::uuid(),
                    'rebox_id' => $rebox->id,
                    'rebox_uuid' => $rebox->uuid,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? 'System',
                    'user_role' => auth()->user()->role ?? 'System',
                    'aksi' => 'UPDATE',
                    'field_yang_diubah' => $fieldsChanged,
                    'nilai_lama' => $oldValues,
                    'nilai_baru' => $newValues,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'keterangan' => 'Data rebox diperbarui'
                ]);
            }
        }
    }
}
