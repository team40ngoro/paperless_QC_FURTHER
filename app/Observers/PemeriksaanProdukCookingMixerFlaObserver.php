<?php

namespace App\Observers;

use App\Models\PemeriksaanProdukCookingMixerFla;
use App\Models\PemeriksaanProdukCookingMixerFlaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PemeriksaanProdukCookingMixerFlaObserver
{
    /**
     * Handle the PemeriksaanProdukCookingMixerFla "updated" event.
     */
    public function updated(PemeriksaanProdukCookingMixerFla $pemeriksaanProdukCookingMixerFla): void
    {
        // Hanya log jika ada perubahan yang sebenarnya
        if (!$pemeriksaanProdukCookingMixerFla->wasChanged()) {
            return;
        }

        // Ambil field yang berubah, kecuali updated_at dan created_at
        $changedFields = array_keys($pemeriksaanProdukCookingMixerFla->getChanges());
        $changedFields = array_filter($changedFields, function($field) {
            return !in_array($field, ['updated_at', 'created_at']);
        });

        if (empty($changedFields)) {
            return;
        }

        // Ambil nilai lama dan baru
        $oldValues = [];
        $newValues = [];
        
        foreach ($changedFields as $field) {
            $oldValues[$field] = $pemeriksaanProdukCookingMixerFla->getOriginal($field);
            $newValues[$field] = $pemeriksaanProdukCookingMixerFla->getAttribute($field);
        }

        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userId = $user ? $user->id : null;

        // Buat log entry
        PemeriksaanProdukCookingMixerFlaLog::create([
            'pemeriksaan_produk_cooking_mixer_fla_id' => $pemeriksaanProdukCookingMixerFla->id,
            'pemeriksaan_produk_cooking_mixer_fla_uuid' => $pemeriksaanProdukCookingMixerFla->uuid,
            'user_id' => $userId,
            'user_name' => $userName,
            'aksi' => 'update',
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $oldValues,
            'nilai_baru' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data pemeriksaan produk cooking mixer fla diperbarui'
        ]);
    }
}
