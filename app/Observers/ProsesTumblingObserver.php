<?php

namespace App\Observers;

use App\Models\ProsesTumbling;
use App\Models\ProsesTumblingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProsesTumblingObserver
{
    /**
     * Handle the ProsesTumbling "updated" event.
     *
     * @param  \App\Models\ProsesTumbling  $prosesTumbling
     * @return void
     */
    public function updated(ProsesTumbling $prosesTumbling)
    {
        // Only log if there are actual changes and not during creation
        if ($prosesTumbling->wasRecentlyCreated || !$prosesTumbling->wasChanged()) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $changedFields = [];
        $oldValues = [];
        $newValues = [];

        foreach ($prosesTumbling->getChanges() as $field => $newValue) {
            // Skip timestamps and system fields
            if (in_array($field, ['updated_at', 'created_at'])) {
                continue;
            }

            $changedFields[] = $field;
            $oldValues[$field] = $prosesTumbling->getOriginal($field);
            $newValues[$field] = $newValue;
        }

        // Only create log if there are meaningful changes
        if (!empty($changedFields)) {
            ProsesTumblingLog::create([
                'proses_tumbling_id' => $prosesTumbling->id,
                'proses_tumbling_uuid' => $prosesTumbling->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'field_yang_diubah' => $changedFields,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
            ]);
        }
    }
}
