<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mapping Master Standar Audit & Modul QC
    |--------------------------------------------------------------------------
    |
    | File konfigurasi ini menjadi "Single Source of Truth" untuk pemetaan
    | modul QC, tabel utama, tabel anak (relasi), serta daftar kolom (field)
    | yang mendukung fitur auto-koreksi audit.
    |
    | Field hanya dicantumkan jika BENAR-BENAR ada sebagai input di form
    | (create.blade.php) masing-masing modul.
    |
    */

    'modules' => [

        // --------------------------------------------------------------------
        // PEMERIKSAAN KEDATANGAN
        // --------------------------------------------------------------------
        'chillroom' => [
            'label'      => '[QF-01/00] Chillroom',
            'main_table' => 'chillroom',
            'fields'     => [
                'suhu'    => ['label' => 'Suhu Chillroom (°C)', 'type' => 'suhu_ruang'],
                'sensori' => ['label' => 'Sensori (OK/NG)', 'type' => 'status_checklist'],
                'kemasan' => ['label' => 'Kondisi Kemasan (OK/NG)', 'type' => 'status_checklist'],
            ],
        ],

        'seasoning' => [
            'label'      => '[QF-01/00] Seasoning',
            'main_table' => 'seasoning',
            'fields'     => [
                'sensori' => ['label' => 'Sensori (OK/NG)', 'type' => 'status_checklist'],
                'kemasan' => ['label' => 'Kondisi Kemasan (OK/NG)', 'type' => 'status_checklist'],
            
            ],
        ],

        'pemeriksaan_bahan_kemas' => [
            'label'      => '[QF-03/00] Kemasan',
            'main_table' => 'pemeriksaan_bahan_kemas',
            'fields'     => [
                'kondisi_bahan_kemasan' => ['label' => 'Kondisi Bahan Kemasan (OK/NG)', 'type' => 'status_checklist'],
            ],
        ],

        'rebox' => [
            'label'      => '[QF-34/00] Rebox',
            'main_table' => 'rebox',
            'fields'     => [
                'labelisasi' => ['label' => 'Kesesuaian Labelisasi (OK/NG)', 'type' => 'status_checklist'],
                'isi_jumlah' => ['label' => 'Kesesuaian Isi & Jumlah ', 'type' => 'status_checklist'],
            ],
        ],

        // --------------------------------------------------------------------
        // MEAT PREPARATION
        // --------------------------------------------------------------------
        'persiapan_bahan_forming' => [
            'label'      => '[QF-04/00] Cold Mixing',
            'main_table' => 'persiapan_bahan_forming',
            'fields'     => [
                'kondisi'                   => ['label' => 'Kondisi Penggilingan (OK/NG)', 'type' => 'status_checklist'],
                'kondisi_hasil_pencampuran' => ['label' => 'Kondisi Hasil Pencampuran (OK/NG)', 'type' => 'status_checklist'],
            ],
        ],

        // 'pembuatan_emulsi' => [
        //     'label'      => '[QF-05/00] Bahan Emulsi',
        //     'main_table' => 'pembuatan_emulsi',
        //     'fields'     => [
        //         'suhu' => ['label' => 'Suhu Emulsi (°C)', 'type' => 'suhu'],
        //         'hasil_emulsi' => ['label' => 'Hasil Emulsi (OK/NG)', 'type' => 'status_checklist'],
        //     ],
        // ],

        'persiapan_bahan_better' => [
            'label'      => '[QF-06/00] Bahan Better',
            'main_table' => 'persiapan_bahan_better',
            'fields'     => [
                'suhu_air' => ['label' => 'Suhu Air (°C)', 'type' => 'suhu'],
                
            ],
        ],

        // 'proses_tumbling' => [
        //     'label'      => '[QF-07/00] Verifikasi Proses Tumbling',
        //     'main_table' => 'proses_tumbling',
        //     'fields'     => [
        //         'suhu_ruang'  => ['label' => 'Suhu Ruangan (°C)', 'type' => 'suhu_ruang'],
        //         'suhu_daging' => ['label' => 'Suhu Daging (°C)', 'type' => 'suhu'],
        //         'suhu_hasil_tumbling' => ['label' => 'Suhu Hasil Tumbling (°C)', 'type' => 'suhu'],
        //         'sensori_hasil_tumbling' => ['label' => 'Sensori Hasil Tumbling (OK/NG)', 'type' => 'status_checklist'],
        //     ],
        // ],

        // --------------------------------------------------------------------
        // COOKING QC (WIZARDS)
        // --------------------------------------------------------------------
        'wizard_proses1' => [
            'label'      => '[QF-17/00] Proses Frying',
            'main_table' => 'pembekuan_iqf_penggorengan',
            'fields'     => [
                'suhu_ruang_iqf' => ['label' => 'Suhu Ruang IQF (°C)', 'type' => 'suhu_kondisi'],
            ],
            'relations'  => [
                'hasil_penggorengan' => [
                    'table'  => 'hasil_penggorengan',
                    'fields' => [
                        'aktual_suhu_pusat' => ['label' => 'Suhu Pusat Produk Frying (°C)', 'type' => 'suhu_pusat'],
                    ],
                ],
            ],
        ],

        'wizard_proses3' => [
            'label'      => '[QF-18/00] Verifikasi Proses Roasted Produk',
            'main_table' => 'pembekuan_iqf_roasting',
            'fields'     => [
                'suhu_ruang_iqf' => ['label' => 'Suhu Ruang IQF Roasting (°C)', 'type' => 'suhu_kondisi'],
            ],
            'relations'  => [
                'proses_roasting_fan' => [
                    'table'  => 'proses_roasting_fan',
                    'fields' => [
                        'suhu_pusat' => ['label' => 'Suhu Pusat Roasting (°C)', 'type' => 'suhu_pusat'],
                    ],
                ],
                'hasil_proses_roasting' => [
                    'table'  => 'hasil_proses_roasting',
                    'fields' => [
                        'aktual_suhu_pusat' => ['label' => 'Suhu Pusat Roasted Produk (°C)', 'type' => 'suhu_pusat'],
                    ],
                ],
            ],
        ],

        // Proses Thawing - sesuai proses_twahing/create.blade.php
        'proses_thawing' => [
            'label'      => '[QF-33/00] Proses Thawing',
            'main_table' => 'proses_twahing',
            'fields'     => [
                'suhu_rm'          => ['label' => 'Suhu RM (°C)', 'type' => 'suhu'],
                'suhu_ruang'       => ['label' => 'Suhu Ruang (°C)', 'type' => 'suhu_ruang'],
                'suhu_air_thawing' => ['label' => 'Suhu Air Thawing (°C)', 'type' => 'suhu'],
                'suhu_produk'      => ['label' => 'Suhu Produk (°C)', 'type' => 'suhu_pusat'],
                // 'kondisi_ruang'    => ['label' => 'Kondisi Ruangan', 'type' => 'status_checklist'],
                // 'kondisi_produk'   => ['label' => 'Kondisi Produk', 'type' => 'status_checklist'],
            ],
        ],

        // --------------------------------------------------------------------
        // PRODUK FLA
        // Sesuai pemeriksaan-produk-cooking-mixer-fla/create.blade.php
        // --------------------------------------------------------------------
        'pemeriksaan_produk_cooking_mixer_fla' => [
            'label'      => '[QF-12/00] Cooking Mixer FLA',
            'main_table' => 'pemeriksaan_produk_cooking_mixer_fla',
            'fields'     => [
                'sensori_kondisi' => ['label' => 'Sensori Kondisi', 'type' => 'suhu'],
                'temp_std_1'      => ['label' => 'Temperature Standard 1 (°C)', 'type' => 'suhu_pemasakan'],
                'temp_std_2'      => ['label' => 'Temperature Standard 2 (°C)', 'type' => 'suhu_pemasakan'],
                'temp_std_3'      => ['label' => 'Temperature Standard 3 (°C)', 'type' => 'suhu_pemasakan'],
                'organo_warna'    => ['label' => 'Organoleptic Warna (OK/Tidak OK)', 'type' => 'status_checklist'],
                'organo_aroma'    => ['label' => 'Organoleptic Aroma (OK/Tidak OK)', 'type' => 'status_checklist'],
                'organo_tekstur'  => ['label' => 'Organoleptic Tekstur (OK/Tidak OK)', 'type' => 'status_checklist'],
                'organo_rasa'     => ['label' => 'Organoleptic Rasa (OK/Tidak OK)', 'type' => 'status_checklist'],
            ],
        ],

        // --------------------------------------------------------------------
        // RHEON MACHINE
        // Sesuai pemeriksaan-rheon-machine/create.blade.php
        // Catatan: field berat (dough/filler/after forming/frying) bersifat array
        // dinamis dikirim sebagai JSON — dipetakan via rata-rata saja.
        // --------------------------------------------------------------------
        
        // 'pemeriksaan_rheon_machine' => [
        //     'label'      => '[QF-19/00] Rheon Machine',
        //     'main_table' => 'pemeriksaan_rheon_machine',
        //     'fields'     => [
        //         'rata_rata_dough'        => ['label' => 'Rata-rata Berat Dough/Adonan (g)', 'type' => 'suhu'],
        //         'rata_rata_filler'       => ['label' => 'Rata-rata Berat Filler Isi (g)', 'type' => 'suhu'],
        //         'rata_rata_after_forming' => ['label' => 'Rata-rata Berat After Forming (g)', 'type' => 'suhu'],
        //         'rata_rata_after_frying'  => ['label' => 'Rata-rata Berat After Frying (g)', 'type' => 'suhu'],
        //     ],
        // ],

        // --------------------------------------------------------------------
        // PRODUK RICE BITES
        // --------------------------------------------------------------------

        // Sesuai pemasakan_nasi/create.blade.php
        'pemasakan_nasi' => [
            'label'      => '[QF-13/00] Pemasakan Nasi',
            'main_table' => 'pemasakan_nasi',
            'fields'     => [
                'temp_std_1'      => ['label' => 'Temp Std 1 (°C)', 'type' => 'suhu_pemasakan'],
                'temp_std_2'      => ['label' => 'Temp Std 2 (°C)', 'type' => 'suhu_pemasakan'],
                'temp_std_3'      => ['label' => 'Temp Std 3 (°C)', 'type' => 'suhu_pemasakan'],
                'sensori_kondisi' => ['label' => 'Sensori Kondisi', 'type' => 'status_checklist'],
                'organo_warna'    => ['label' => 'Organo Warna', 'type' => 'status_checklist'],
                'organo_aroma'    => ['label' => 'Organo Aroma', 'type' => 'status_checklist'],
                'organo_rasa'     => ['label' => 'Organo Rasa', 'type' => 'status_checklist'],
                'organo_tekstur'  => ['label' => 'Organo Tekstur', 'type' => 'status_checklist'],
            ],
        ],

        // Sesuai pemeriksaan_rice_bites/create.blade.php
        'pemeriksaan_rice_bites' => [
            'label'      => '[QF-14/00] Pemeriksaan Produk Rice Bites',
            'main_table' => 'pemeriksaan_rice_bites',
            'fields'     => [
                'parameter_nitrogen'     => ['label' => 'Parameter Nitrogen', 'type' => 'status_checklist'],
                'jumlah_inject_nitrogen' => ['label' => 'Jumlah Inject Nitrogen', 'type' => 'status_checklist'],
                'rpm_cooking_cattle'     => ['label' => 'RPM Cooking Cattle', 'type' => 'status_checklist'],
              
                'rata_rata_suhu'         => ['label' => 'Rata-rata Suhu Pencampuran (°C)', 'type' => 'suhu'],
                'hasil_pencampuran'      => ['label' => 'Hasil Pencampuran', 'type' => 'status_checklist'],
            ],
        ],

        // --------------------------------------------------------------------
        // PACKING
        // --------------------------------------------------------------------
        // 'pembuatan_sample' => [
        //     'label'      => '[QF-17/00] Pembuatan Sample Produk',
        //     'main_table' => 'pembuatan_sample',
        //     'fields'     => [
        //         'status_checklist' => ['label' => 'Kondisi Sample Produk', 'type' => 'status_checklist'],
        //     ],
        // ],

        'input_metal_detector' => [
            'label'      => '[QF-18/00] Metal Detector',
            'main_table' => 'input_metal_detector',
            'fields'     => [
                'status_checklist' => ['label' => 'Status Metal Detector (Fe/Non-Fe/SS)', 'type' => 'status_checklist'],
            ],
        ],

        // Sesuai produk_yum/create.blade.php (array dinamis per row)
        'produk_yum' => [
            'label'      => '[QF-16/00] KPI Produk YUM',
            'main_table' => 'produk_yum',
            'fields'     => [
                'aktual_berat' => ['label' => 'Aktual Berat Per Pack (gram)', 'type' => 'suhu'],
                // 'jumlah_pcs'   => ['label' => 'Jumlah Isi per Pack (pcs)', 'type' => 'suhu'],
                // 'berat_pcs'    => ['label' => 'Berat Per Potong (gram)', 'type' => 'suhu'],
            ],
        ],

        // --------------------------------------------------------------------
        // NON PROSES PRODUKSI
        // --------------------------------------------------------------------
        'produk_forming' => [
            'label'      => '[QF-20/00] Produk Forming',
            'main_table' => 'produk_forming',
            'fields'     => [
                'status_checklist' => ['label' => 'Status Pergantian Produksi', 'type' => 'status_checklist'],
            ],
        ],

        'produk_non_forming' => [
            'label'      => '[QF-21/00] Produk Non Forming',
            'main_table' => 'produk_non_forming',
            'fields'     => [
                'status_checklist' => ['label' => 'Status Pergantian Produksi', 'type' => 'status_checklist'],
            ],
        ],

        // Sesuai ketidaksesuaian_plastik/create.blade.php
        // 'ketidaksesuaian_plastik' => [
        //     'label'      => '[QF-22/00] Ketidaksesuaian Plastik',
        //     'main_table' => 'ketidaksesuaian_plastik',
        //     'fields'     => [
        //         'nama_plastik'           => ['label' => 'Nama Plastik', 'type' => 'status_checklist'],
        //         'jumlah_ketidaksesuaian' => ['label' => 'Jumlah Ketidaksesuaian (Kg)', 'type' => 'suhu'],
        //         'alasan_hold'            => ['label' => 'Alasan Hold', 'type' => 'status_checklist'],
        //     ],
        // ],

        // Sesuai pemeriksaan_benda_asing/create.blade.php
        // 'pemeriksaan_benda_asing' => [
        //     'label'      => '[QF-23/00] Kontaminasi Benda Asing',
        //     'main_table' => 'pemeriksaan_benda_asing',
        //     'fields'     => [
        //         'jenis_kontaminasi'  => ['label' => 'Jenis Kontaminasi', 'type' => 'status_checklist'],
        //         'ukuran_kontaminasi' => ['label' => 'Ukuran Kontaminasi', 'type' => 'suhu'],
        //         'ditemukan'          => ['label' => 'Ditemukan (Lokasi/Cara)', 'type' => 'status_checklist'],
        //         'jumlah_terdampak'   => ['label' => 'Jumlah Produk/RM Terdampak', 'type' => 'suhu'],
        //     ],
        // ],

        // Sesuai pemeriksaan_proses_produksi/create.blade.php
        // 'pemeriksaan_proses_produksi' => [
        //     'label'      => '[QF-24/00] Ketidaksesuaian Proses Produksi',
        //     'main_table' => 'pemeriksaan_proses_produksi',
        //     'fields'     => [
        //         'ketidaksesuaian'     => ['label' => 'Jenis Ketidaksesuaian', 'type' => 'status_checklist'],
        //         'uraian_permasalahan' => ['label' => 'Uraian Permasalahan', 'type' => 'status_checklist'],
        //         'analisa_penyebab'    => ['label' => 'Analisa Penyebab', 'type' => 'status_checklist'],
        //         'disposisi'           => ['label' => 'Disposisi', 'type' => 'status_checklist'],
        //         'tindakan_koreksi'    => ['label' => 'Tindakan Koreksi', 'type' => 'status_checklist'],
        //     ],
        // ],

        // Sesuai area_proses/create.blade.php (array dinamis per baris area)
        'area_proses' => [
            'label'      => '[QF-25/00] Pemeriksaan Kondisi Area Kerja Selama Produksi',
            'main_table' => 'area_proses',
            'fields'     => [
                'kebersihan_ruangan'     => ['label' => 'Kebersihan Ruangan (OK/Kotor)', 'type' => 'status_checklist'],
                'kebersihan_karyawan'    => ['label' => 'Kebersihan Karyawan (OK/Kotor)', 'type' => 'status_checklist'],
                'pemeriksaan_suhu_ruang' => ['label' => 'Pemeriksaan Suhu Ruang (°C)', 'type' => 'suhu_ruang'],
            ],
        ],

        // Sesuai verif_peralatan/create.blade.php (checklist mesin per area, verifikasi Bersih/Kotor)
        'verif_peralatan' => [
            'label'      => '[QF-26/00] Verifikasi Kebersihan Ruangan Mesin & Peralatan',
            'main_table' => 'verif_peralatan',
            'fields'     => [
                'status_checklist' => ['label' => 'Kebersihan Peralatan (Bersih/Kotor)', 'type' => 'status_checklist'],
            ],
        ],

        // 'verif_cip' => [
        //     'label'      => '[QF-27/00] Pemeriksaan CIP',
        //     'main_table' => 'verif_cip',
        //     'fields'     => [
        //         'suhu_air'         => ['label' => 'Suhu Air CIP (°C)', 'type' => 'suhu'],
        //         'status_checklist' => ['label' => 'Status Kebersihan CIP', 'type' => 'status_checklist'],
        //     ],
        // ],

        // Sesuai gmp_karyawan/create.blade.php (array entries per karyawan)
        'gmp_karyawan' => [
            'label'      => '[QF-28/00] GMP Karyawan',
            'main_table' => 'gmp_karyawan',
            'fields'     => [
                // 'temuan_ketidaksesuaian' => ['label' => 'Temuan Ketidaksesuaian GMP', 'type' => 'status_checklist'],
                'verifikasi'             => ['label' => 'Verifikasi GMP (OK/Tidak OK)', 'type' => 'status_checklist'],
            ],
        ],

        // Sesuai kontrol_sanitasi/create.blade.php (array entries per pemeriksaan)
        'kontrol_sanitasi' => [
            'label'      => '[QF-29/00] Kontrol Sanitasi',
            'main_table' => 'kontrol_sanitasi',
            'fields'     => [
                'suhu_air'                => ['label' => 'Suhu Air (°C)', 'type' => 'suhu'],
                'kadar_klorin_food_basin' => ['label' => 'Kadar Klorin Foot Basin', 'type' => 'suhu'],
                'kadar_klorin_hand_basin' => ['label' => 'Kadar Klorin Hand Basin', 'type' => 'suhu'],
            ],
        ],

        // Sesuai barang_mudah_pecah/create.blade.php (array items per barang per area)
        'barang_mudah_pecah' => [
            'label'      => '[QF-30/00] Barang Mudah Pecah',
            'main_table' => 'barang_mudah_pecah',
            'fields'     => [
                'kondisi' => ['label' => 'Kondisi Barang (OK/Tidak OK)', 'type' => 'status_checklist'],
            ],
        ],
    ],
];
