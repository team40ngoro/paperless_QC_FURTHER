<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Proses Tumbling</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .form-info { margin-bottom: 15px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 3px 5px; border: 1px solid #000; font-size: 9px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }
        .section-title { font-weight: bold; font-size: 11px; margin-top: 15px; margin-bottom: 5px; color: #333; border-left: 3px solid #007bff; padding-left: 5px; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 5px; border: 1px solid #000; }
        .signature-box { height: 60px; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: right; font-size: 8px; }
        .badge { padding: 2px 5px; border-radius: 3px; border: 1px solid #000; }
        .signature-label { text-align: center; font-size: 10px; margin-bottom: 5px; }
        .signature-qr-area { height: 65px; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #000; margin-top: 5px; margin-bottom: 5px; }
        .signature-name { font-size: 10px; text-align: center; }
        .qr-code-img { max-height: 55px; max-width: 55px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @if($data->count() > 0)
        @foreach($data as $aging)
        <div class="header">
            <div class="company-logo">
                <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <div class="company-info">
                <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
                <div>FOOD DIVISION {{ strtoupper($aging->plan->nama_plan ?? auth()->user()->plan->nama_plan) }}</div>
                <div>{{ strtoupper($aging->plan->nama_plan ?? auth()->user()->plan->nama_plan) }} - INDONESIA</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="title">VERIFIKASI PROSES TUMBLING</div>
        <!-- @php
            $firstItem = $data->first();
            $shiftDisplay = $filters['shift_id'] 
                ? (\App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown')
                : 'Semua Shift';
            $periodeDisplay = \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y');
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td width="20%">Periode Tanggal</td>
                    <td width="30%">: {{ $periodeDisplay }}</td>
                    <td width="20%">Shift</td>
                    <td width="30%">: {{ $shiftDisplay }}</td>
                </tr>
            </table>
        </div> -->
        <div class="form-info">
            <table>
                <tr>
                    <td width="20%">Hari / Tanggal</td>
                    <td width="30%">: {{ \Carbon\Carbon::parse($aging->tanggal)->format('l, d-m-Y') }}</td>
                    <td width="20%">Produk</td>
                    <td width="30%">: {{ $aging->produk->nama_produk ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td>: 
                        @php
                            $shiftName = '-';
                            if ($aging->prosesTumbling && $aging->prosesTumbling->shift) {
                                $shiftName = $aging->prosesTumbling->shift->shift;
                            }
                        @endphp
                        {{ $shiftName }}
                    </td>
                    <td>Kode Produksi</td>
                    <td>: {{ $aging->kode_produksi ?? ($aging->prosesTumbling->kode_produksi ?? '-') }}</td>
                </tr>
            </table>
        </div>

        {{-- SECTION 1: BAHAN BAKU --}}
        @php
            $bahan = $aging->bahanBaku;
            if (!$bahan && $aging->prosesTumbling) {
                $bahan = $aging->prosesTumbling->bahanBakuTumbling;
            }
        @endphp
        
        @if($bahan)
        <div class="section-title">1. DATA BAHAN BAKU & PENCAMPURAN</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bahan Baku</th>
                    <th>Kode Produksi</th>
                    <th>Jumlah (Kg)</th>
                    <th>Suhu (°C)</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($bahan->manual_bahan_data))
                    @foreach($bahan->manual_bahan_data as $i => $b)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="text-align: left;">{{ $b['nama_bahan_baku'] ?? '-' }}</td>
                        <td>{{ $b['kode_produksi_bahan_baku'] ?? '-' }}</td>
                        <td>{{ $b['jumlah'] ?? '-' }}</td>
                        <td>{{ $b['suhu'] ?? '-' }}</td>
                        <td>{{ isset($b['kondisi_daging']) && ($b['kondisi_daging'] == '✓' || $b['kondisi_daging'] == 'OK') ? 'OK' : (($b['kondisi_daging'] ?? '') == '×' || ($b['kondisi_daging'] ?? '') == '✗' ? 'Tidak OK' : ($b['kondisi_daging'] ?? '-')) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="6">Data bahan baku tidak ditemukan.</td></tr>
                @endif
            </tbody>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-top: -15px; margin-bottom: 10px;">
            <tr>
                @if(!empty($bahan->gramase) && $bahan->gramase !== '-')
                <td style="border: 1px solid #000; padding: 5px; font-size: 8px;"><strong>Gramase:</strong> {{ $bahan->gramase }} g</td>
                @endif
                
                @if(!empty($bahan->salinity) && $bahan->salinity !== '-')
                <td style="border: 1px solid #000; padding: 5px; font-size: 8px;"><strong>Salinity:</strong> {{ $bahan->salinity }}</td>
                @endif
                
                @if(!empty($bahan->viskositas) && $bahan->viskositas !== '-')
                <td style="border: 1px solid #000; padding: 5px; font-size: 8px;"><strong>Brix:</strong> {{ $bahan->viskositas }}</td>
                @endif
                
                <td style="border: 1px solid #000; padding: 5px; font-size: 8px;"><strong>Hasil Pencampuran:</strong> {{ isset($bahan->hasil_pencampuran) && ($bahan->hasil_pencampuran == '✓' || $bahan->hasil_pencampuran == 'OK') ? 'OK' : ($bahan->hasil_pencampuran == '×' || $bahan->hasil_pencampuran == '✗' ? 'Tidak OK' : ($bahan->hasil_pencampuran ?? '-')) }}</td>
            </tr>
        </table>
        @endif

        {{-- SECTION 2: PROSES TUMBLING --}}
        @php
            $sectionNumber = '2';
            $sectionTitle = 'PROSES TUMBLING';
        @endphp
        <div class="section-title">{{ $sectionNumber }}. {{ $sectionTitle }}</div>
        @php
            $bahanUuid = $aging->bahan_baku_tumbling_uuid ?? ($aging->prosesTumbling->bahan_baku_tumbling_uuid ?? null);
            $allTumbling = \App\Models\ProsesTumbling::with(['dataTumbling'])
                ->where('bahan_baku_tumbling_uuid', $bahanUuid)
                ->get();
        @endphp

        @foreach($allTumbling as $tum)
        @php
            $checkVakum = [
                $tum->aktual_drum_on, $tum->aktual_drum_off, $tum->aktual_speed, 
                $tum->aktual_total_waktu, $tum->aktual_vakum, 
                $tum->waktu_mulai_tumbling, $tum->waktu_selesai_tumbling
            ];
            $checkNonVakum = [
                $tum->aktual_drum_on_non_vakum, $tum->aktual_drum_off_non_vakum, 
                $tum->aktual_speed_non_vakum, $tum->aktual_total_waktu_non_vakum, 
                $tum->aktual_tekanan_non_vakum, 
                $tum->waktu_mulai_tumbling_non_vakum, $tum->waktu_selesai_tumbling_non_vakum
            ];
            
            $hasVakum = false;
            foreach($checkVakum as $val) {
                if(!empty($val) && $val !== '-' && $val !== '---' && $val !== 'null') { $hasVakum = true; break; }
            }
            
            $hasNonVakum = false;
            foreach($checkNonVakum as $val) {
                if(!empty($val) && $val !== '-' && $val !== '---' && $val !== 'null') { $hasNonVakum = true; break; }
            }
            
            if(!$hasVakum && !$hasNonVakum) {
                $hasVakum = true; 
                $hasNonVakum = true;
            }
        @endphp
        <div style="margin-bottom: 20px; border: 1px solid #ffffffff; padding: 0;">
            <div style="background-color: #ffffffff; color: #fff; padding: 3px 10px; margin-bottom: 0;">
                <strong>{{ $tum->dataTumbling->nama_tumbling ?? 'MESIN TUMBLING' }}</strong>
            </div>
            
            <table class="main-table" style="margin-top: 0; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th rowspan="2" width="20%">PARAMETER</th>
                        @if($hasVakum)<th colspan="2">TUMBLING VAKUM</th>@endif
                        @if($hasNonVakum)<th colspan="2">TUMBLING NON-VAKUM</th>@endif
                    </tr>
                    <tr>
                        @if($hasVakum)
                        <th width="{{ $hasNonVakum ? '20%' : '40%' }}">STANDART</th>
                        <th width="{{ $hasNonVakum ? '20%' : '40%' }}">AKTUAL</th>
                        @endif
                        @if($hasNonVakum)
                        <th width="{{ $hasVakum ? '20%' : '40%' }}">STANDART</th>
                        <th width="{{ $hasVakum ? '20%' : '40%' }}">AKTUAL</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: left;">Drum On</td>
                        @if($hasVakum)
                        <td>{{ $tum->dataTumbling->drum_on ?? '-' }}</td>
                        <td>{{ $tum->aktual_drum_on ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td>{{ $tum->dataTumbling->drum_on_non_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_drum_on_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Drum Off</td>
                        @if($hasVakum)
                        <td>{{ $tum->dataTumbling->drum_off ?? '-' }}</td>
                        <td>{{ $tum->aktual_drum_off ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td>{{ $tum->dataTumbling->drum_off_non_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_drum_off_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Speed (Rpm)</td>
                        @if($hasVakum)
                        <td>{{ $tum->dataTumbling->drum_speed ?? '-' }}</td>
                        <td>{{ $tum->aktual_speed ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td>{{ $tum->dataTumbling->drum_speed_non_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_speed_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Total Waktu</td>
                        @if($hasVakum)
                        <td>{{ $tum->dataTumbling->total_waktu ?? '-' }}</td>
                        <td>{{ $tum->aktual_total_waktu ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td>{{ $tum->dataTumbling->total_waktu_non_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_total_waktu_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Tekanan / Vakum</td>
                        @if($hasVakum)
                        <td>{{ $tum->dataTumbling->tekanan_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_vakum ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td>{{ $tum->dataTumbling->tekanan_non_vakum ?? '-' }}</td>
                        <td>{{ $tum->aktual_tekanan_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Waktu (Start - End)</td>
                        @if($hasVakum)
                        <td colspan="2">{{ $tum->waktu_mulai_tumbling ?? '-' }} - {{ $tum->waktu_selesai_tumbling ?? '-' }}</td>
                        @endif
                        @if($hasNonVakum)
                        <td colspan="2">{{ $tum->waktu_mulai_tumbling_non_vakum ?? '-' }} - {{ $tum->waktu_selesai_tumbling_non_vakum ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left;">Suhu Hasil Tumbling</td>
                        <td colspan="{{ ($hasVakum && $hasNonVakum) ? '4' : '2' }}">{{ $tum->suhu_hasil_tumbling ?? '-' }} °C</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Sensori</td>
                        <td colspan="{{ ($hasVakum && $hasNonVakum) ? '4' : '2' }}">{{ strtoupper($tum->sensori_hasil_tumbling ?? '-') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- SECTION 3: PROSES AGING (Only show if aging is actually used) --}}
        @php
            $isAgingUsed = !($aging->is_dummy_aging ?? false);
            
            if ($isAgingUsed) {
                $waktuMulai = trim($aging->waktu_mulai_aging ?? '');
                $waktuSelesai = trim($aging->waktu_selesai_aging ?? '');
                $suhuProduk = trim($aging->suhu_produk ?? '');
                
                if (empty($waktuMulai) && empty($waktuSelesai) && empty($suhuProduk)) {
                    $isAgingUsed = false;
                }
            }
        @endphp
        
        @if($isAgingUsed)
        <div class="section-title">3. HASIL PROSES AGING</div>
        @php
            $waktuMulaiArray = array_map('trim', explode(',', $aging->waktu_mulai_aging ?? ''));
            $suhuKondisiArray = array_map('trim', explode(',', $aging->suhu_produk ?? ''));
            $maxCount = max(count($waktuMulaiArray), count($suhuKondisiArray));
        @endphp
        
        <table class="main-table">
            <thead>
                <tr>
                    <th width="10%">No</th>
                    <th width="30%">Waktu Mulai Aging</th>
                    <th width="30%">Suhu Produk</th>
                    <th width="30%">Kondisi Produk</th>
                </tr>
            </thead>
            <tbody>
                @for($index = 0; $index < $maxCount; $index++)
                    @php
                        $waktu = $waktuMulaiArray[$index] ?? '';
                        $suhuKondisi = $suhuKondisiArray[$index] ?? '';
                        
                        if (preg_match('/^(.+?)\s*\((.+?)\)$/', $suhuKondisi, $matches)) {
                            $suhu = trim($matches[1]);
                            $kondisi = trim($matches[2]);
                        } else {
                            $suhu = $suhuKondisi;
                            $kondisi = $aging->kondisi_produk ?? '✓';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $waktu }}</strong></td>
                        <td><strong>{{ $suhu }}</strong></td>
                        <td>{{ ($kondisi == '✓') ? 'OK' : 'Tidak OK' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 3px;">
            <tr>
                <td style="border: 1px solid #000; padding: 8px; font-size: 10px; text-align: center; background: #e8f5e9;">
                    <strong>Waktu Selesai Aging:</strong>
                    <span>{{ $aging->waktu_selesai_aging }}</span>
                </td>
            </tr>
        </table>
        @endif
        
        <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            {{ $kode_form ?? 'QF 12/00' }}
        </div>

        {{-- SIGNATURES WITH QR CODE --}}
        @php
            $isDummyAging = ($aging->is_dummy_aging ?? false);
            $approvalSource = $isDummyAging && isset($aging->prosesTumbling) ? $aging->prosesTumbling : $aging;
            
            $qcApprover = null;
            $qcApproverName = 'QC';
            $showQcQr = false;
            if($approvalSource->approved_by_qc && $approvalSource->qc_approved_by && $approvalSource->qc_approved_at) {
                $qcApprover = \App\Models\User::find($approvalSource->qc_approved_by);
                if($qcApprover) { 
                    $qcApproverName = $qcApprover->name;
                } else {
                    $qcApproverName = 'QC (User ID: ' . $approvalSource->qc_approved_by . ')';
                }
                $showQcQr = true;
            }
            
            $produksiApprover = null;
            $produksiApproverName = 'FM/FL PRODUKSI';
            $showProduksiQr = false;
            if($approvalSource->approved_by_produksi && $approvalSource->produksi_approved_by && $approvalSource->produksi_approved_at) {
                $produksiApprover = \App\Models\User::find($approvalSource->produksi_approved_by);
                if($produksiApprover) { 
                    $produksiApproverName = $produksiApprover->name;
                } else {
                    $produksiApproverName = 'PRODUKSI (User ID: ' . $approvalSource->produksi_approved_by . ')';
                }
                $showProduksiQr = true;
            }
            
            $spvApprover = null;
            $spvApproverName = 'SPV';
            $showSpvQr = false;
            if($approvalSource->approved_by_spv && $approvalSource->spv_approved_by && $approvalSource->spv_approved_at) {
                $spvApprover = \App\Models\User::find($approvalSource->spv_approved_by);
                if($spvApprover) { 
                    $spvApproverName = $spvApprover->name;
                } else {
                    $spvApproverName = 'SPV (User ID: ' . $approvalSource->spv_approved_by . ')';
                }
                $showSpvQr = true;
            }

            $base64QcSvg = null;
            if($showQcQr) {
                $qcApprovalDate = is_string($approvalSource->qc_approved_at) 
                    ? \Carbon\Carbon::parse($approvalSource->qc_approved_at)->format('Y-m-d')
                    : $approvalSource->qc_approved_at->format('Y-m-d');
                $qcVerifierName = $qcApprover ? $qcApprover->name : $qcApproverName;
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcVerifierName} (Tim QC) pada {$qcApprovalDate}"));
            }
            
            $base64ProduksiSvg = null;
            if($showProduksiQr) {
                $produksiApprovalDate = is_string($approvalSource->produksi_approved_at)
                    ? \Carbon\Carbon::parse($approvalSource->produksi_approved_at)->format('Y-m-d')
                    : $approvalSource->produksi_approved_at->format('Y-m-d');
                $produksiVerifierName = $produksiApprover ? $produksiApprover->name : $produksiApproverName;
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiVerifierName} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
            
            $base64SpvSvg = null;
            if($showSpvQr) {
                $spvApprovalDate = is_string($approvalSource->spv_approved_at)
                    ? \Carbon\Carbon::parse($approvalSource->spv_approved_at)->format('Y-m-d')
                    : $approvalSource->spv_approved_at->format('Y-m-d');
                $spvVerifierName = $spvApprover ? $spvApprover->name : $spvApproverName;
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvVerifierName} (SPV) pada {$spvApprovalDate}"));
            }
        @endphp

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
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
            </tr>
        </table>

        @if(!$loop->last)
        <div class="page-break"></div>
        @endif
        @endforeach

    @else
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>