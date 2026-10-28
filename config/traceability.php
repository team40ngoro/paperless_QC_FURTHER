<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traceability Modules Configuration
    |--------------------------------------------------------------------------
    |
    | Pemetaan modul QC aktual ke dalam konfigurasi traceability.
    |
    */

    'modules' => [

        // ------------------------------------------------------------------
        // 1. CHILLROOM [QF-01/00]
        // ------------------------------------------------------------------
        'chillroom' => [
            'model'            => \App\Models\PenerimaanChillroom::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_rm', 'keterangan'],
            'search_relations' => [],
            'form_model'      => \App\Models\PenerimaanChillroom::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'chillroom.bulk-export-pdf',
            'index_route'     => 'chillroom.index',
            'label'           => '[QF-01/00] Kedatangan Chillroom',
            'display_fields'  => function ($detail) {
                return [
                    'Nama RM'       => $detail->nama_rm ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Berat'         => ($detail->berat ?? '-') . ' Kg',
                    'Suhu'          => ($detail->suhu ?? '-') . ' °C',
                    'Sensori'       => $detail->sensori ?? '-',
                    'Kemasan'       => $detail->kemasan ?? '-',
                    'Keterangan'    => $detail->keterangan ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 2. SEASONING [QF-01/00]
        // ------------------------------------------------------------------
        'seasoning' => [
            'model'            => \App\Models\Seasoning::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_rm', 'supplier'],
            'search_relations' => [],
            'form_model'      => \App\Models\Seasoning::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'seasoning.bulk-export-pdf',
            'index_route'     => 'seasoning.index',
            'label'           => '[QF-01/00] Kedatangan Seasoning',
            'display_fields'  => function ($detail) {
                return [
                    'Nama RM'       => $detail->nama_rm ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Supplier'      => $detail->supplier ?? '-',
                    'Jumlah (Zak)'  => $detail->jumlah_zak ?? '-',
                    'Berat'         => ($detail->berat ?? '-') . ' Kg',
                    'Keterangan'    => $detail->keterangan ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 3. SHOESTRING [QF-02/00]
        // ------------------------------------------------------------------
        'shoestring' => [
            'model'            => \App\Models\Shoestring::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_rm', 'supplier'],
            'search_relations' => [],
            'form_model'      => \App\Models\Shoestring::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'shoestring.bulk-export-pdf',
            'index_route'     => 'shoestring.index',
            'label'           => '[QF-02/00] Kedatangan Shoestring',
            'display_fields'  => function ($detail) {
                return [
                    'Nama RM'       => $detail->nama_rm ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Supplier'      => $detail->supplier ?? '-',
                    'Jumlah (Box)'  => $detail->jumlah_box ?? '-',
                    'Keterangan'    => $detail->keterangan ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 4. PEMERIKSAAN KEMASAN [QF-03/00]
        // ------------------------------------------------------------------
        'kemasan' => [
            'model'            => \App\Models\PemeriksaanBahanKemas::class,
            'column'           => 'kode_lot',
            'search_columns'   => ['kode_lot', 'nama_barang', 'supplier', 'nama_supplier'],
            'search_relations' => [],
            'form_model'      => \App\Models\PemeriksaanBahanKemas::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'pemeriksaan-bahan-kemas.bulk-export-pdf',
            'index_route'     => 'pemeriksaan-bahan-kemas.index',
            'label'           => '[QF-03/00] Pemeriksaan Bahan Kemas',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Barang'   => $detail->nama_barang ?? '-',
                    'Kode Lot'      => $detail->kode_lot ?? '-',
                    'Supplier'      => $detail->supplier ?? $detail->nama_supplier ?? '-',
                    'Hasil Check'   => $detail->hasil_pemeriksaan ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 5. REBOX [QF-34/00]
        // ------------------------------------------------------------------
        'rebox' => [
            'model'            => \App\Models\Rebox::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\Rebox::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'rebox.bulk-export-pdf',
            'index_route'     => 'rebox.index',
            'label'           => '[QF-34/00] Rebox',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Isi / Jumlah'  => $detail->isi_jumlah ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 6. PERSIAPAN BAHAN FORMING (COLD MIXING) [QF-04/00]
        // ------------------------------------------------------------------
        'persiapan_forming' => [
            'model'            => \App\Models\PersiapanBahanForming::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_bahan', 'supplier', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\PersiapanBahanForming::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'persiapan-bahan-forming.bulk-export-pdf',
            'index_route'     => 'persiapan-bahan-forming.index',
            'label'           => '[QF-04/00] Cold Mixing (Persiapan Forming)',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Nama Bahan'    => $detail->nama_bahan ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Berat (Kg)'    => $detail->berat ?? '-',
                    'Supplier'      => $detail->supplier ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 7. PERSIAPAN BAHAN NON FORMING [QF-04/00]
        // ------------------------------------------------------------------
        'persiapan_non_forming' => [
            'model'            => \App\Models\PersiapanBahanNonForming::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_bahan', 'supplier', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\PersiapanBahanNonForming::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'persiapan-bahan-non-forming.bulk-export-pdf',
            'index_route'     => 'persiapan-bahan-non-forming.index',
            'label'           => '[QF-04/00] Persiapan Bahan Non-Forming',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Nama Bahan'    => $detail->nama_bahan ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Berat (Kg)'    => $detail->berat ?? '-',
                    'Supplier'      => $detail->supplier ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 7b. VERIFIKASI PEMBUATAN EMULSI [QF-05/00]
        // ------------------------------------------------------------------
        'persiapan_emulsi' => [
            'model'            => \App\Models\PembuatanEmulsi::class,
            'column'           => 'kode_produksi_emulsi',
            'search_columns'   => ['kode_produksi_emulsi', 'kode_form'],
            'search_relations' => [
                'produk' => 'nama_produk',
            ],
            'form_model'      => \App\Models\PembuatanEmulsi::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'uuid',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'persiapan-bahan-emulsi.export-pdf',
            'index_route'     => 'persiapan-bahan-emulsi.index',
            'label'           => '[QF-05/00] Verifikasi Pembuatan Emulsi',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi_emulsi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 7c. PERSIAPAN BAHAN BETTER [QF-06/00]
        // ------------------------------------------------------------------
        'persiapan_better' => [
            'model'            => \App\Models\PersiapanBahanBetter::class,
            'column'           => 'kode_produksi_produk',
            'search_columns'   => ['kode_produksi_produk', 'kode_produksi_better', 'kode_form'],
            'search_relations' => [
                'produk' => 'nama_produk',
            ],
            'form_model'      => \App\Models\PersiapanBahanBetter::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'uuid',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'persiapan-bahan-better.export-pdf',
            'index_route'     => 'persiapan-bahan-better.index',
            'label'           => '[QF-06/00] Persiapan Bahan Better',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi_produk ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 8. PROSES PENGGORENGAN (WIZARD PROSES 2)
        // ------------------------------------------------------------------
        'penggorengan' => [
            'model'            => \App\Models\Penggorengan::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [
                'produk' => 'nama_produk'
            ],
            'form_model'      => \App\Models\Penggorengan::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizard.proses2.bulk-export-pdf',
            'index_route'     => 'wizard.proses2.index',
            'label'           => '[Wizard Proses 2] Proses Penggorengan',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 9. PROSES ROASTING (WIZARD PROSES 3)
        // ------------------------------------------------------------------
        'input_roasting' => [
            'model'            => \App\Models\InputRoasting::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [
                'produk' => 'nama_produk'
            ],
            'form_model'      => \App\Models\InputRoasting::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizard.proses3.bulk-export-pdf',
            'index_route'     => 'wizard.proses3.index',
            'label'           => '[Wizard Proses 3] Proses Roasting',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 10. PROSES TUMBLING (WIZARD PROSES 4)
        // ------------------------------------------------------------------
        'proses_tumbling' => [
            'model'            => \App\Models\ProsesTumbling::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [
                'produk' => 'nama_produk'
            ],
            'form_model'      => \App\Models\ProsesTumbling::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizard.proses4.bulk-export-pdf',
            'index_route'     => 'wizard.proses4.index',
            'label'           => '[Wizard Proses 4] Proses Tumbling',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 11. PENGEMASAN PLASTIK (WIZARD PACKING)
        // ------------------------------------------------------------------
        'pengemasan_plastik' => [
            'model'            => \App\Models\PengemasanPlastik::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'kode_kemasan_plastik', 'nomor_md'],
            'search_relations' => [
                'pengemasanProduk' => 'kode_produksi'
            ],
            'form_model'      => \App\Models\PengemasanPlastik::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizards.packing.bulk-export-pdf',
            'label'           => '[Wizard Packing] Pengemasan Plastik',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->pengemasanProduk?->produk?->nama_produk ?? '-',
                    'Kode Produksi' => $detail->pengemasanProduk?->kode_produksi ?? $detail->kode_produksi ?? '-',
                    'Kode Kemasan'  => $detail->kode_kemasan_plastik ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 12. PENGEMASAN KARTON (WIZARD PACKING)
        // ------------------------------------------------------------------
        'pengemasan_karton' => [
            'model'            => \App\Models\PengemasanKarton::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [
                'produk' => 'nama_produk'
            ],
            'form_model'      => \App\Models\PengemasanKarton::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizards.packing.bulk-export-pdf',
            'label'           => '[Wizard Packing] Pengemasan Karton',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 13. PROSES THAWING [QF-33/00]
        // ------------------------------------------------------------------
        'proses_thawing' => [
            'model'            => \App\Models\ProsesTwahing::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_rm', 'nama_bahan'],
            'search_relations' => [],
            'form_model'      => \App\Models\ProsesTwahing::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'proses-twahing.bulk-export-pdf',
            'index_route'     => 'proses-twahing.index',
            'label'           => '[QF-33/00] Proses Thawing',
            'display_fields'  => function ($detail) {
                return [
                    'Nama RM'       => $detail->nama_rm ?? $detail->nama_bahan ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Suhu Sebelum'  => ($detail->suhu_sebelum ?? '-') . ' °C',
                    'Suhu Sesudah'  => ($detail->suhu_sesudah ?? '-') . ' °C',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 14. VERIFIKASI PENGEMASAN [QF-15/00]
        // ------------------------------------------------------------------
        'verifikasi_pengemasan' => [
            'model'            => \App\Models\PengemasanProduk::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk', 'kode_produksi_2', 'kode_kemasan'],
            'search_relations' => [
                'produk'  => 'nama_produk',
                'produk2' => 'nama_produk',
            ],
            'form_model'      => \App\Models\PengemasanProduk::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'wizards.packing.bulk-export-pdf',
            'label'           => '[QF-15/00] Verifikasi Pengemasan',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->produk?->nama_produk ?? $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Nama Produk 2' => $detail->produk2?->nama_produk ?? '-',
                    'Kode Prod 2'   => $detail->kode_produksi_2 ?? '-',
                    'Keterangan'    => $detail->keterangan ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 15. PERGANTIAN PROSES FORMING [QF-20/00]
        // ------------------------------------------------------------------
        'produk_forming' => [
            'model'            => \App\Models\ProdukForming::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\ProdukForming::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'produk-forming.export-pdf',
            'label'           => '[QF-20/00] Pergantian Proses Produk Forming',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 16. PERGANTIAN PROSES NON-FORMING [QF-21/00]
        // ------------------------------------------------------------------
        'produk_non_forming' => [
            'model'            => \App\Models\ProdukNonForming::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\ProdukNonForming::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'produk-non-forming.export-pdf',
            'label'           => '[QF-21/00] Pergantian Proses Produk Non Forming',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 17. PEMBUATAN SAMPLE [QF-17/00]
        // ------------------------------------------------------------------
        'pembuatan_sample' => [
            'model'            => \App\Models\PembuatanSample::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\PembuatanSample::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'pembuatan-sample.export-pdf',
            'label'           => '[QF-17/00] Pembuatan Sample Produk',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Jumlah Sample' => $detail->jumlah_sample ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 18. METAL DETECTOR [QF-18/00]
        // ------------------------------------------------------------------
        'metal_detector' => [
            'model'            => \App\Models\InputMetalDetector::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\InputMetalDetector::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'input-metal-detector.export-pdf',
            'label'           => '[QF-18/00] Metal Detector',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                    'Hasil'         => $detail->hasil ?? '-',
                ];
            },
        ],

        // ------------------------------------------------------------------
        // 19. PRODUK YUM [QF-16/00]
        // ------------------------------------------------------------------
        'produk_yum' => [
            'model'            => \App\Models\ProdukYum::class,
            'column'           => 'kode_produksi',
            'search_columns'   => ['kode_produksi', 'nama_produk'],
            'search_relations' => [],
            'form_model'      => \App\Models\ProdukYum::class,
            'form_relation'   => 'self',
            'detail_relation' => null,
            'route_key_column' => 'id',
            'plan_column'      => 'id_plan',
            'pdf_route'       => 'produk-yum.export-pdf',
            'label'           => '[QF-16/00] KPI Produk YUM',
            'display_fields'  => function ($detail) {
                return [
                    'Nama Produk'   => $detail->nama_produk ?? '-',
                    'Kode Produksi' => $detail->kode_produksi ?? '-',
                ];
            },
        ],

    ],
];
