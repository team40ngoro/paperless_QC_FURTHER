<?php

namespace App\Observers;

use App\Models\VerifikasiBeratProduk;
use App\Models\VerifikasiBeratLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class VerifikasiBeratObserver
{
    /**
     * Handle the VerifikasiBerat "updating" event.
     *
     * @param  \App\Models\VerifikasiBerat  $verifikasiBerat
     * @return void
     */
    public function updating(VerifikasiBeratProduk $verifikasiBerat)
    {
        // Ambil data original (sebelum diubah)
        $original = $verifikasiBerat->getOriginal();
        $changes = $verifikasiBerat->getDirty();
        
        // Jika ada perubahan
        if (!empty($changes)) {
            // Ambil field yang berubah
            $fieldsChanged = array_keys($changes);
            
            // Siapkan nilai lama dan baru
            $nilaiLama = [];
            $nilaiBaru = [];
            
            foreach ($fieldsChanged as $field) {
                $nilaiLama[$field] = $original[$field] ?? null;
                $nilaiBaru[$field] = $changes[$field] ?? null;
            }
            
            // Ambil informasi user
            $user = Auth::user();
            $userName = $user ? $user->name : 'System';
            $userRole = $user ? $user->role->nama_role ?? 'Unknown' : 'System';
            
            // Simpan log
            VerifikasiBeratLog::create([
                'verifikasi_berat_id' => $verifikasiBerat->id,
                'verifikasi_berat_uuid' => $verifikasiBerat->uuid,
                'user_id' => $user ? $user->id : null,
                'user_name' => $userName,
                'user_role' => $userRole,
                'aksi' => 'update',
                'field_yang_diubah' => $fieldsChanged,
                'nilai_lama' => $nilaiLama,
                'nilai_baru' => $nilaiBaru,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'keterangan' => 'Data verifikasi berat produk diperbarui'
            ]);
        }
    }

    /**
     * Handle the VerifikasiBerat "created" event.
     *
     * @param  \App\Models\VerifikasiBerat  $verifikasiBerat
     * @return void
     */
    public function created(VerifikasiBeratProduk $verifikasiBerat)
    {
        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userRole = $user ? $user->role->nama_role ?? 'Unknown' : 'System';
        
        // Ambil semua field yang diisi
        $allFields = array_keys($verifikasiBerat->getAttributes());
        $nilaiData = [];
        
        foreach ($allFields as $field) {
            if ($field !== 'id' && $field !== 'created_at' && $field !== 'updated_at') {
                $nilaiData[$field] = $verifikasiBerat->$field;
            }
        }
        
        // Simpan log untuk create
        VerifikasiBeratLog::create([
            'verifikasi_berat_id' => $verifikasiBerat->id,
            'verifikasi_berat_uuid' => $verifikasiBerat->uuid,
            'user_id' => $user ? $user->id : null,
            'user_name' => $userName,
            'user_role' => $userRole,
            'aksi' => 'create',
            'field_yang_diubah' => array_keys($nilaiData),
            'nilai_lama' => [],
            'nilai_baru' => $nilaiData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data verifikasi berat produk baru dibuat'
        ]);
    }

    /**
     * Handle the VerifikasiBerat "deleted" event.
     *
     * @param  \App\Models\VerifikasiBerat  $verifikasiBerat
     * @return void
     */
    public function deleted(VerifikasiBeratProduk $verifikasiBerat)
    {
        // Ambil informasi user
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';
        $userRole = $user ? $user->role->nama_role ?? 'Unknown' : 'System';
        
        // Ambil semua data yang akan dihapus
        $allData = $verifikasiBerat->getOriginal();
        
        // Simpan log untuk delete
        VerifikasiBeratLog::create([
            'verifikasi_berat_id' => $verifikasiBerat->id,
            'verifikasi_berat_uuid' => $verifikasiBerat->uuid,
            'user_id' => $user ? $user->id : null,
            'user_name' => $userName,
            'user_role' => $userRole,
            'aksi' => 'delete',
            'field_yang_diubah' => array_keys($allData),
            'nilai_lama' => $allData,
            'nilai_baru' => [],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'keterangan' => 'Data verifikasi berat produk dihapus'
        ]);
    }
}
