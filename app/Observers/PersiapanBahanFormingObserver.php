<?php

namespace App\Observers;

use App\Models\PersiapanBahanForming;
use App\Models\PersiapanBahanFormingLog;
use Illuminate\Support\Facades\Auth;

class PersiapanBahanFormingObserver
{
    /**
     * Handle the PersiapanBahanForming "updated" event.
     * Event ini akan dipanggil otomatis setiap kali data diupdate
     */
    public function updated(PersiapanBahanForming $persiapanBahanForming)
    {
        // Pastikan ada user yang login
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        // Ambil data yang berubah
        $dataAsli = $persiapanBahanForming->getOriginal(); // Data sebelum diubah
        $dataBaru = $persiapanBahanForming->getAttributes(); // Data setelah diubah
        
        // Cari field yang berubah
        $fieldYangBerubah = [];
        $nilaiLama = [];
        $nilaiBaru = [];
        
        foreach ($dataBaru as $field => $nilai) {
            // Skip field yang tidak perlu di-log
            if (in_array($field, ['id', 'uuid', 'created_at', 'updated_at'])) {
                continue;
            }
            
            $nilaiLamaField = $dataAsli[$field] ?? null;
            $nilaiBaruField = $nilai;
            
            // Jika nilai berubah
            if ($nilaiLamaField != $nilaiBaruField) {
                $fieldYangBerubah[] = $field;
                $nilaiLama[$field] = $this->formatNilai($nilaiLamaField);
                $nilaiBaru[$field] = $this->formatNilai($nilaiBaruField);
            }
        }
        
        // Jika ada perubahan, buat log
        if (!empty($fieldYangBerubah)) {
            $this->buatLog($persiapanBahanForming, $user, $fieldYangBerubah, $nilaiLama, $nilaiBaru);
        }
    }

    /**
     * Method untuk membuat log perubahan
     */
    private function buatLog($persiapanBahanForming, $user, $fieldYangBerubah, $nilaiLama, $nilaiBaru)
    {
        PersiapanBahanFormingLog::create([
            'persiapan_bahan_forming_id' => $persiapanBahanForming->id,
            'persiapan_bahan_forming_uuid' => $persiapanBahanForming->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'update',
            'field_yang_diubah' => $fieldYangBerubah,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $nilaiBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => 'Data diperbarui melalui Observer'
        ]);
    }

    /**
     * Format nilai untuk ditampilkan dengan lebih baik
     */
    /**
 * Format nilai untuk ditampilkan dengan lebih baik
 */
    private function formatNilai($nilai)
    {
        if (is_null($nilai)) {
            return 'Kosong';
        }
        
        if (is_bool($nilai)) {
            return $nilai ? 'Ya' : 'Tidak';
        }
        
        // TAMBAH: Handle array
        if (is_array($nilai)) {
            return json_encode($nilai);
        }
        
        if (is_numeric($nilai)) {
            return (string) $nilai;
        }
        
        return (string) $nilai;
    }
}
