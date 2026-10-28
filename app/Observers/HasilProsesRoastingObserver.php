<?php

namespace App\Observers;

use App\Models\HasilProsesRoasting;
use App\Models\HasilProsesRoastingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class HasilProsesRoastingObserver
{
    /**
     * Handle the HasilProsesRoasting "updated" event.
     */
    public function updated(HasilProsesRoasting $model)
    {
        $original = $model->getOriginal();
        $changes = $model->getChanges();

        unset($changes['updated_at'], $changes['created_at']);

        if (!empty($changes)) {
            $fieldsChanged = [];
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $field => $newValue) {
                $fieldsChanged[] = $field;
                $oldValues[] = $original[$field] ?? null;
                $newValues[] = $newValue;
            }

            HasilProsesRoastingLog::create([
                'hasil_proses_roasting_id' => $model->id,
                'hasil_proses_roasting_uuid' => $model->uuid,
                'user_id' => Auth::id(),
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
