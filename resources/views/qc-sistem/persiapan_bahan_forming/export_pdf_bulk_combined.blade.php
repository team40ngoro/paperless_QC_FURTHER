<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan (Forming & Non Forming)</title>
    <style>
        /* Setup Halaman Landscape agar kolom Muat dengan Rapi */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body { 
            font-family: Arial, sans-serif; 
            font-size: 8px; 
            margin: 0; 
            padding: 0; 
            color: #000;
        }

        /* Header Fix */
        .header { 
            width: 100%;
            margin-bottom: 15px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 5px; 
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
        }
        .header-table td {
            border: none !important;
            padding: 0;
            vertical-align: middle;
        }
        .company-name { font-weight: bold; font-size: 11px; }
        .company-info { font-size: 9px; }

        .title { text-align: center; font-size: 12px; font-weight: bold; margin: 10px 0; text-transform: uppercase; }
        .section-title { font-size: 10px; font-weight: bold; margin: 10px 0 5px 0; }
        
        /* Table Styling Fix */
        table.main-table, table.sub-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; /* Mencegah kolom meluap */
            margin-bottom: 10px;
        }
        
        table.main-table th, table.main-table td,
        table.sub-table th, table.sub-table td { 
            border: 1px solid #000; 
            padding: 3px 2px; 
            font-size: 7.5px; 
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word; /* Mencegah teks panjang merusak kolom */
        }
        
        th { background: #f0f0f0; font-weight: bold; }

        /* === PERBAIKAN UTAMA ===
           Baris "LARUTAN BUMBU" tidak lagi pakai rowspan lintas <tr>.
           Sekarang setiap item forming = SATU <tr> luar, dan daftar
           bahan RM (jumlahnya variabel per item) dirender sebagai
           tabel nested DI DALAM satu <td>. Dengan begitu tidak ada lagi
           sel rowspan yang bisa "hilang" saat halaman terpotong. */
        table.nested-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.nested-table th {
            border: none;
            border-bottom: 1px solid #000;
            background: #f5f5f5;
            padding: 2px 1px;
            font-size: 7px;
            text-align: center;
        }
        table.nested-table td {
            border: none;
            border-bottom: 1px solid #ddd;
            padding: 2px 1px;
            font-size: 7.5px;
            text-align: center;
            word-wrap: break-word;
        }
        table.nested-table tr:last-child td { border-bottom: none; }

        /* Filter Info Styling */
        .filter-info { margin-bottom: 10px; padding: 6px; background-color: #f9f9f9; border: 1px solid #ddd; }
        .filter-info h4 { margin: 0 0 4px 0; font-size: 9px; font-weight: bold; }
        .filter-info p { margin: 0; font-size: 8.5px; }

        /* Signature Table Fix */
        .signature-container {
            page-break-inside: avoid;
            margin-top: 15px;
        }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 0 10px; border: none !important; }
        .signature-label { font-size: 9px; font-weight: bold; margin-bottom: 5px; }
        .signature-qr-area { height: 60px; text-align: center; vertical-align: middle; }
        .signature-line { border-top: 1px solid #000; margin: 4px auto; width: 80%; }
        .signature-name { font-size: 8.5px; font-weight: bold; }
        .qr-code-img { height: 50px; width: 50px; }
    </style>
</head>
<body>

    <!-- Header menggunakan Table agar posisi logo & teks stabil di Dompdf -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70px;">
                    <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 55px; height: auto;">
                </td>
                <td>
                    <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
                    <div class="company-info">FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
                    <div class="company-info">{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">
       VERIFIKASI PERSIAPAN COLD MIXING
    </div>

    @if(isset($filterInfo))
    <div class="filter-info">
        <h4>Informasi Data</h4>
        <p>
            <strong>Tanggal:</strong> {{ $filterInfo['tanggal_mulai'] }} s/d {{ $filterInfo['tanggal_akhir'] }} | 
            <strong>Shift:</strong> {{ $filterInfo['shift'] }} | 
            <strong>Produk:</strong> {{ $filterInfo['produk'] }}
        </p>
    </div>
    @endif

    <div class="section-title">A. Persiapan Bahan Forming</div>
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2.5%;">No</th>
                <th rowspan="2" style="width: 4.5%;">Waktu</th>
                <th rowspan="2" style="width: 6%;">Kode Produksi</th>
                <th rowspan="2" style="width: 6.5%;">Kode Produksi Emulsi OIL</th>
                <th rowspan="2" style="width: 6%;">Nomor Formula</th>
                <th rowspan="2" style="width: 4.5%;">Gramase (g)</th>
                <th rowspan="2" style="width: 27.5%;">LARUTAN BUMBU</th>
                <th rowspan="2" style="width: 5.5%;">Suhu Std Adonan</th>
                <th rowspan="2" style="width: 4%;">PRODUK REWORK</th>
                <th rowspan="2" style="width: 4.5%;">REWORK (kg)</th>
                <th colspan="6">SUHU AKTUAL (°C)</th>
                <th rowspan="2" style="width: 6.5%;">Waktu Mixing</th>
                <th rowspan="2" style="width: 4.5%;">Hasil Penggilingan</th>
                <th rowspan="2" style="width: 4.5%;">Hasil Pencampuran</th>
                <th rowspan="2" style="width: 7%;">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 3%;">1</th>
                <th style="width: 3%;">2</th>
                <th style="width: 3%;">3</th>
                <th style="width: 3%;">4</th>
                <th style="width: 3%;">5</th>
                <th style="width: 4%;">Total</th>
            </tr>
        </thead>
        <tbody>
        @php
            $no = 1;
            // Pre-load semua produk rework untuk menghindari N+1
            $allReworkIds = $dataForming->flatMap(fn($r) => (array)($r->produk_rework_id ?? []))->filter()->unique()->values()->toArray();
            $reworkProdukMap = \App\Models\JenisProduk::whereIn('id', $allReworkIds)->get()->keyBy('id');
        @endphp
        @forelse($dataForming as $item)
            <tr style="page-break-inside: avoid;">
                <td>{{ $no++ }}</td>
                <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                <td>{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                <td>{{ is_array($item->kode_produksi_emulsi_oil) ? implode(', ', $item->kode_produksi_emulsi_oil) : ($item->kode_produksi_emulsi_oil ?? '-') }}</td>
                <td>{{ $item->formula->nomor_formula ?? '-' }}</td>
                <td>{{ $item->berat_sampling ?? '-' }}</td>

                {{-- Kolom LARUTAN BUMBU: 1 sel, isinya tabel nested --}}
                <td style="padding: 0;">
                    @if(count($item->suhuForming) > 0)
                    <table class="nested-table">
                        <thead>
                            <tr>
                                <th style="width: 22%;">Bahan RM</th>
                                <th style="width: 16%;">Berat RM</th>
                                <th style="width: 22%;">Kode Produksi</th>
                                <th style="width: 12%;">Suhu</th>
                                <th style="width: 13%;">Sensori</th>
                                <th style="width: 15%;">Ket.</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($item->suhuForming as $suhu)
                            <tr>
                                <td style="text-align: left;">{{ $suhu->bahanForming->nama_rm ?? '-' }}</td>
                                <td>{{ $suhu->bahanForming->berat_rm ?? '-' }}</td>
                                <td>{{ $suhu->kode_produksi_bahan ?? '-' }}</td>
                                <td>{{ isset($suhu->suhu) ? $suhu->suhu . '°C' : '-' }}</td>
                                <td>{{ $suhu->sensori ?? '-' }}</td>
                                <td>{{ $suhu->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        @php
                            $getFloatPdf = function($val) {
                                if (is_null($val) || $val === '' || is_array($val)) return 0.0;
                                $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string)$val));
                                return is_numeric($clean) ? (float)$clean : 0.0;
                            };
                            $beratListPdf = $item->suhuForming->map(fn($s) => $getFloatPdf($s->bahanForming->berat_rm ?? 0));
                            $totalBeratPdf = $beratListPdf->sum();
                            $avgBeratPdf   = $beratListPdf->count() > 0 ? $beratListPdf->avg() : 0;
                        @endphp
                        <tfoot>
                            <tr>
                                <td style="font-weight:bold; text-align:right;">Tot:</td>
                                <td style="font-weight:bold;">{{ number_format($totalBeratPdf, 3) }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold; text-align:right;">Avg:</td>
                                <td style="font-weight:bold;">{{ number_format($avgBeratPdf, 3) }}</td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                        -
                    @endif
                </td>

                <td>{{ $item->suhuAdonan->std_suhu ?? '-' }}</td>
                @php
                    $reworkNamaBulk = '-';
                    if (!empty($item->produk_rework_id)) {
                        $names = collect((array) $item->produk_rework_id)
                            ->map(fn($id) => $reworkProdukMap->get($id)?->nama_produk)
                            ->filter()
                            ->implode(', ');
                        $reworkNamaBulk = $names ?: '-';
                    }
                @endphp
                <td>{{ $reworkNamaBulk }}</td>
                <td>{{ $item->rework ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan->aktual_suhu_1 ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan->aktual_suhu_2 ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan->aktual_suhu_3 ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan->aktual_suhu_4 ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan->aktual_suhu_5 ?? '-' }}</td>
                <td>{{ $item->aktualSuhuAdonan ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                <td>{{ $item->waktu_mulai_mixing ?? '-' }} - {{ $item->waktu_selesai_mixing ?? '-' }}</td>
                <td>{{ $item->kondisi == '✔' ? 'OK' : 'Not OK' }}</td>
                <td>{{ $item->kondisi_hasil_pencampuran == '✔' ? 'OK' : 'Not OK' }}</td>
                <td>{{ $item->catatan ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="20" style="text-align:center;">Tidak ada data forming untuk filter ini</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">B. Persiapan Bahan Non Forming</div>
    <table class="sub-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 6%;">Jam</th>
                <th style="width: 8%;">Shift</th>
                <th>Produk</th>
                <th style="width: 12%;">Kode Produksi</th>
                <th style="width: 12%;">Nomor Formula</th>
                <th style="width: 8%;">Kondisi</th>
                <th style="width: 14%;">Dibuat Oleh</th>
                <th style="width: 16%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no2 = 1; @endphp
            @forelse($dataNonForming as $item)
                <tr style="page-break-inside: avoid;">
                    <td>{{ $no2++ }}</td>
                    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                    <td>{{ $item->shift->shift ?? '-' }}</td>
                    <td style="text-align: left;">{{ $item->formulaNonForming->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->kode_produksi ?? '-' }}</td>
                    <td>{{ $item->formulaNonForming->nomor_formula ?? '-' }}</td>
                    <td>
                        @if($item->kondisi == '✔' || $item->kondisi == '&#10004;')
                            OK
                        @elseif($item->kondisi == '✘' || $item->kondisi == '&#10008;')
                            Not OK
                        @else
                            {{ $item->kondisi ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td style="text-align: left;">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">Tidak ada data non-forming untuk filter ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 15px;">{{ $kode_form ?? '-' }}</div>

    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC';
        $spvApprover = null; $spvApproverName = 'SPV';
        $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
        $qcApprovalDate = ''; $spvApprovalDate = ''; $produksiApprovalDate = '';

        $combinedData = $dataForming->concat($dataNonForming);

        foreach($combinedData as $item) {
            if($item->approved_by_qc && $item->qc_approved_by) {
                $qcApprover = \App\Models\User::find($item->qc_approved_by);
                if($qcApprover) { $qcApproverName = $qcApprover->name; $qcApprovalDate = optional($item->qc_approved_at)->format('Y-m-d'); break; }
            }
        }
        foreach($combinedData as $item) {
            if($item->approved_by_spv && $item->spv_approved_by) {
                $spvApprover = \App\Models\User::find($item->spv_approved_by);
                if($spvApprover) { $spvApproverName = $spvApprover->name; $spvApprovalDate = optional($item->spv_approved_at)->format('Y-m-d'); break; }
            }
        }
        foreach($combinedData as $item) {
            if($item->approved_by_produksi && $item->produksi_approved_by) {
                $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                if($produksiApprover) { $produksiApproverName = $produksiApprover->name; $produksiApprovalDate = optional($item->produksi_approved_at)->format('Y-m-d'); break; }
            }
        }

        $base64QcSvg = null;
        if($qcApprover) {
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover) {
            $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover) {
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
        }
    @endphp

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
                <td class="signature-label">Diketahui Oleh: Produksi</td>
                <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
            </tr>
            <tr>
                <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
                <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
            </tr>
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
            </tr>
        </table>
    </div>

</body>
</html>