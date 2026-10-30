<?php

namespace App\Observers;

use App\Models\BarangMudahPecah;
use App\Models\BarangMudahPecahLog;

class BarangMudahPecahObserver
{
    /**
     * Handle the BarangMudahPecah "updated" event.
     */
    public function updated(BarangMudahPecah $barangMudahPecah)
    {
        $original = $barangMudahPecah->getOriginal();
        $changes = $barangMudahPecah->getChanges();
        
        // Hapus timestamps dari tracking
        unset($changes['updated_at']);
        
        if (empty($changes)) {
            return;
        }
        
        $fieldsChanged = [];
        $oldValues = [];
        $newValues = [];
        
        foreach ($changes as $field => $newValue) {
            if (isset($original[$field])) {
                // Simpan field asli (bukan yang sudah di-mapping) untuk field_yang_diubah
                $fieldsChanged[] = $field;
                // Simpan nilai asli dengan key field asli untuk nilai_lama dan nilai_baru
                $oldValues[$field] = $original[$field];
                $newValues[$field] = $newValue;
            }
        }
        
        if (!empty($fieldsChanged)) {
            $user = auth()->user();
            
            BarangMudahPecahLog::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'barang_mudah_pecah_id' => $barangMudahPecah->id,
                'barang_mudah_pecah_uuid' => $barangMudahPecah->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_role' => $user ? $user->role : 'system',
                'aksi' => 'update',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $oldValues,
                'nilai_baru' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Data diperbarui melalui sistem'
            ]);
        }
    }
}
