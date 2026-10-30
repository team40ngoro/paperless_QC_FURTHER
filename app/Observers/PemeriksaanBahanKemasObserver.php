<?php

namespace App\Observers;

use App\Models\PemeriksaanBahanKemas;
use App\Models\PemeriksaanBahanKemasLog;
use Illuminate\Support\Str;

class PemeriksaanBahanKemasObserver
{
    /**
     * Handle the PemeriksaanBahanKemas "updated" event.
     */
    public function updated(PemeriksaanBahanKemas $item)
    {
        $changes = $item->getChanges();
        $original = $item->getOriginal();

        unset($changes['updated_at'], $changes['created_at']);

        if (empty($changes)) {
            return;
        }

        $fieldsChanged = [];
        $oldValues = [];
        $newValues = [];

        foreach ($changes as $field => $newValue) {
            if (array_key_exists($field, $original) && $original[$field] != $newValue) {
                $fieldsChanged[] = $field;
                $oldValues[$field] = $original[$field];
                $newValues[$field] = $newValue;
            }
        }

        if (empty($fieldsChanged)) {
            return;
        }

        PemeriksaanBahanKemasLog::create([
            'uuid' => Str::uuid(),
            'pemeriksaan_bahan_kemas_id' => $item->id,
            'pemeriksaan_bahan_kemas_uuid' => $item->uuid,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'user_role' => auth()->user()->role ?? 'System',
            'aksi' => 'UPDATE',
            'field_yang_diubah' => $fieldsChanged,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'keterangan' => 'Data pemeriksaan bahan kemas diperbarui'
        ]);
    }
}
