<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Edit Time Limit Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi global untuk membatasi waktu pengeditan data berdasarkan role.
    | Setelah melewati batas waktu (dalam jam) sejak data dibuat (created_at),
    | role yang dibatasi tidak bisa lagi mengedit data tersebut.
    |
    */

    // Daftar role_id yang dibatasi waktu editnya
    'restricted_role_ids' => [
        3, // QC Inspector
    ],

    // Batas waktu edit dalam jam (dihitung dari created_at)
    'time_limit_hours' => 48,

    // Pesan error yang ditampilkan saat waktu edit habis
    'error_message' => 'Waktu edit sudah habis. Data hanya dapat diedit dalam 48 jam setelah dibuat.',

    // Daftar modul yang menerapkan pembatasan ini
    // Format sederhana: 'nama_modul' => Model::class (UUID di segment(3) default)
    // Format wizard: 'nama_modul' => ['model' => Model::class, 'uuid_segment' => 5]
    // Tambahkan modul baru di sini untuk menerapkan pembatasan secara global
    'modules' => [
        'chillroom' => \App\Models\PenerimaanChillroom::class,
        'seasoning' => \App\Models\Seasoning::class,
        'rebox' => \App\Models\Rebox::class,
        'pemeriksaan-bahan-kemas' => \App\Models\PemeriksaanBahanKemas::class,
        'shoestring' => \App\Models\Shoestring::class,
        'persiapan-bahan-forming' => \App\Models\PersiapanBahanForming::class,
        'persiapan-bahan-non-forming' => \App\Models\PersiapanBahanNonForming::class,
        'persiapan-bahan-emulsi' => \App\Models\PembuatanEmulsi::class,
        'persiapan-bahan-better' => \App\Models\PersiapanBahanBetter::class,
        'verifikasi-berat-produk' => \App\Models\VerifikasiBeratProduk::class,

        // Wizard routes: URL = /qc-sistem/wizard/proses-X/update/{uuid}
        // segment(2)=wizard, segment(3)=proses-X, uuid_segment=5
        'wizard.proses1' => ['model' => \App\Models\PembekuanIqfPenggorengan::class, 'uuid_segment' => 5],
        'wizard.proses2' => ['model' => \App\Models\PembekuanIqfRoasting::class, 'uuid_segment' => 5],
        'wizard.proses3' => ['model' => \App\Models\PembekuanIqfRoasting::class, 'uuid_segment' => 5],
        'wizard.proses4' => ['model' => \App\Models\ProsesAging::class, 'uuid_segment' => 5],
        'wizards.packing' => \App\Models\Dokumentasi::class,

        // Other modules
        'label-pemeriksaan' => \App\Models\LabelStartUp::class,
        'input-metal-detector' => \App\Models\InputMetalDetector::class,
        'pembuatan-sample' => \App\Models\PembuatanSample::class,
        'proses-twahing' => \App\Models\ProsesTwahing::class,
        'produk-yum' => \App\Models\ProdukYum::class,
        'release-produk' => \App\Models\ReleaseProdukFinishGood::class,
        'pemeriksaan-produk-cooking-mixer-fla' => \App\Models\PemeriksaanProdukCookingMixerFla::class,
        'pemeriksaan-rheon-machine' => \App\Models\PemeriksaanRheonMachine::class,
        'pemasakan-nasi' => \App\Models\PemasakanNasi::class,
        'pemeriksaan-rice-bites' => \App\Models\PemeriksaanRiceBites::class,
        'produk-forming' => \App\Models\ProdukForming::class,
        'produk-non-forming' => \App\Models\ProdukNonForming::class,
        'gmp-karyawan' => \App\Models\GmpKaryawan::class,
        'ketidaksesuaian-plastik' => \App\Models\KetidaksesuaianPlastik::class,
        'pemeriksaan-benda-asing' => \App\Models\PemeriksaanBendaAsing::class,
        'pemeriksaan-proses-produksi' => \App\Models\PemeriksaanProsesProduksi::class,
        'kontrol-sanitasi' => \App\Models\KontrolSanitasi::class,
        'verif-peralatan' => \App\Models\VerifPeralatan::class,
        'verif-cip' => \App\Models\VerifCip::class,
        'barang-mudah-pecah' => \App\Models\BarangMudahPecah::class,
        'timbangan' => \App\Models\Timbangan::class,
        'thermometer' => \App\Models\Thermometer::class,
        
    ],

];
