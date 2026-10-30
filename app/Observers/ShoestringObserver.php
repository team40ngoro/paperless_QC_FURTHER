<?php

namespace App\Observers;

use App\Models\Shoestring;
use App\Models\ShoestringLog;
use Illuminate\Support\Str;

class ShoestringObserver
{
    /**
     * Handle the Shoestring "updated" event.
     *
     * @param  \App\Models\Shoestring  $shoestring
     * @return void
     */
    public function updated(Shoestring $shoestring)
    {
        $changes = $shoestring->getChanges();
        $original = $shoestring->getOriginal();

        // Filter out timestamp fields - only log actual data changes
        unset($changes['updated_at'], $changes['created_at']);

        if (!empty($changes)) {
            $fieldNames = ShoestringLog::getFieldNames();
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
                ShoestringLog::create([
                    'uuid' => Str::uuid(),
                    'shoestring_id' => $shoestring->id,
                    'shoestring_uuid' => $shoestring->uuid,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? 'System',
                    'user_role' => auth()->user()->role ?? 'System',
                    'aksi' => 'UPDATE',
                    'field_yang_diubah' => $fieldsChanged,
                    'nilai_lama' => $oldValues,
                    'nilai_baru' => $newValues,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                    'keterangan' => 'Data shoestring diperbarui'
                ]);
            }
        }
    }
}
