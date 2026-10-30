<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Pengemasan - Wizard Packing</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; padding-top: 10px; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0; text-decoration: underline; }
        .form-info { margin-bottom: 12px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 3px 5px; border: 1px solid #000; font-size: 9px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 15px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }
        .section-title { font-weight: bold; font-size: 11px; margin-top: 12px; margin-bottom: 4px; color: #000; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 5px; border: 1px solid #000; }
        .signature-box { height: 60px; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: right; font-size: 8px; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 3px 5px; border: none; }
        .signature-label { text-align: center; font-size: 8px; margin-bottom: 3px; }
        .signature-qr-area { height: 55px; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #000; margin-top: 3px; margin-bottom: 3px; }
        .signature-name { font-size: 8px; text-align: center; }
        .qr-code-img { max-height: 45px; max-width: 45px; }
    </style>
</head>
<body>
    @if(isset($data))
    @foreach($data as $item)
        @php
            $pembekuan = $item['pembekuan'];
            $semuaBeratProdukBag = $item['semuaBeratProdukBag'];
        @endphp
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper($pembekuan->plan->nama_plan ?? auth()->user()->plan->nama_plan ?? '') }}</div>
            <div>{{ strtoupper($pembekuan->plan->nama_plan ?? auth()->user()->plan->nama_plan ?? '') }} - INDONESIA</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="title">VERIFIKASI PROSES PENGEMASAN</div>

    <div class="form-info">
        <table>
            <tr>
                <td width="20%">Hari / Tanggal</td>
                <td width="30%">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
                <td width="20%">Produk 1</td>
                <td width="30%">: {{ $pembekuan->pengemasanProduk->produk->nama_produk ?? '-' }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td>: {{ $pembekuan->shift->shift ?? '-' }}</td>
                <td>Kode Produksi 1</td>
                <td>: {{ $pembekuan->pengemasanProduk->kode_produksi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Waktu Pengecekan After IQF</td>
                <td>: {{ $pembekuan->pengemasanProduk->waktu_awal_packing ?? '-' }}</td>
                <td>Berat Produk 1 (gr)</td>
                <td>: {{ $pembekuan->pengemasanProduk->berat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tgl Expired 1</td>
                <td>: {{ $pembekuan->pengemasanProduk->tanggal_expired ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired)->format('d-m-Y') : '-' }}</td>
                <td>Box Kombinasi</td>
                <td>: <strong>{{ ($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi) ? 'Ya (2 Produk dalam 1 Box)' : 'Tidak' }}</strong></td>
            </tr>
            @if($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi)
            <tr style="background-color: #fff8e1;">
                <td><strong>Produk 2</strong></td>
                <td>: {{ $pembekuan->pengemasanProduk->produk2->nama_produk ?? '-' }}</td>
                <td><strong>Berat Produk 2 (gr)</strong></td>
                <td>: {{ $pembekuan->pengemasanProduk->berat_produk_2 ?? '-' }}</td>
            </tr>
            <tr style="background-color: #fff8e1;">
                <td><strong>Kode Produksi 2</strong></td>
                <td>: {{ $pembekuan->pengemasanProduk->kode_produksi_2 ?? '-' }}</td>
                <td><strong>Tgl Expired 2</strong></td>
                <td>: {{ $pembekuan->pengemasanProduk->tanggal_expired_2 ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired_2)->format('d-m-Y') : '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- A. PENGEMASAN PRODUK --}}
    @if($pembekuan->pengemasanProduk)
    <div class="section-title">A. PEMBEKUAN PRODUK (LANGKAH 2)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Std Suhu IQF (&deg;C)</th>
                <th>Aktual Suhu Produk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pembekuan->pengemasanProduk->std_suhu_produk_iqf ?? '-' }}</td>
                <td>
                    @php
                        $suhus = explode(',', $pembekuan->pengemasanProduk->aktual_suhu_produk ?? '');
                        $suhus = array_filter(array_map('trim', $suhus));
                        echo !empty($suhus) ? implode(' &deg;C, ', $suhus) . ' &deg;C' : '-';
                    @endphp
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- B. PENGEMASAN PLASTIK --}}
    @if($pembekuan->pengemasanPlastik)
    <div class="section-title">B. PENGEMASAN PLASTIK (LANGKAH 3)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Line</th>
                <th>Waktu Pengemasan</th>
                <th>Identitas Produk</th>
                <th>Kesesuaian Label Plastik</th>
                <th>Kode Kemasan</th>
                <th>Kekuatan Seal</th>
                <th>Proses Penimbangan</th>
                <th>Proses Sealing</th>
                <th>Nomor MD</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pembekuan->pengemasanPlastik->line ?? '-' }}</td>
                <td>{{ $pembekuan->pengemasanPlastik->waktu_pengemasan ?? '-' }}</td>
                <td>{{ $pembekuan->pengemasanPlastik->identitas_produk == '✔' ? 'OK' : 'Tidak Sesuai' }}</td>
                <td>{{ $pembekuan->pengemasanPlastik->kesesuaian_label_plastik ?? '-' }}</td>
                <td>{{ $pembekuan->pengemasanPlastik->kode_kemasan_plastik ?? '-' }}</td>
                <td>{{ $pembekuan->pengemasanPlastik->kekuatan_seal == '✔' ? 'OK' : 'Tidak Sesuai' }}</td>
                <td>{{ strtoupper($pembekuan->pengemasanPlastik->proses_penimbangan ?? '-') }}</td>
                <td>{{ strtoupper($pembekuan->pengemasanPlastik->proses_sealing ?? '-') }}</td>
                <td style="text-align:center;">
                    @if($pembekuan->pengemasanPlastik->nomor_md)
                        <img src="{{ public_path('storage/' . $pembekuan->pengemasanPlastik->nomor_md) }}" style="max-height:60px;max-width:80px;">
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- C. BERAT PRODUK BAG --}}
    @if($semuaBeratProdukBag->count() > 0)
    <div class="section-title">C. BERAT PRODUK BAG (LANGKAH 4)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Line</th>
                <th>Nilai Standar Pack</th>
                <th>Aktual (gr)</th>
                <th>Rata-rata (gr)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semuaBeratProdukBag as $bag)
            <tr>
                <td>{{ $bag->line ?? '-' }}</td>
                <td>{{ $bag->data_bag ? $bag->data_bag->std_bag : '-' }}</td>
                <td>
                    @php
                        $berats = is_string($bag->berat_aktual) ? json_decode($bag->berat_aktual, true) : $bag->berat_aktual;
                    @endphp
                    @if(is_array($berats) && count($berats) > 0)
                        {{ implode(', ', $berats) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $bag->rata_rata_berat ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- D. BERAT PRODUK BOX --}}
    @if($pembekuan->beratProdukBox)
    <div class="section-title">D. BERAT PRODUK BOX (LANGKAH 5)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Nilai Standar Box</th>
                <th>Aktual (gr)</th>
                <th>Rata-rata (gr)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pembekuan->beratProdukBox->data_box ? $pembekuan->beratProdukBox->data_box->std_box : '-' }}</td>
                <td>
                    @php
                        $beratsBox = is_string($pembekuan->beratProdukBox->berat_aktual) ? json_decode($pembekuan->beratProdukBox->berat_aktual, true) : $pembekuan->beratProdukBox->berat_aktual;
                    @endphp
                    @if(is_array($beratsBox) && count($beratsBox) > 0)
                        {{ implode(', ', $beratsBox) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $pembekuan->beratProdukBox->rata_rata_berat ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- E. PENGEMASAN KARTON --}}
    @if($pembekuan->pengemasanKarton)
    <div class="section-title">E. PENGEMASAN KARTON (LANGKAH 6)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>Kesesuaian Label Karton</th>
                <th>Standar isi pack dalam karton</th>
                <th>Aktual isi pack dalam karton</th>
                <th>Kesesuaian Standar Isi Pack</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pembekuan->pengemasanKarton->identitas_produk_pada_karton == '✔' ? 'OK' : 'Tidak Sesuai' }}</td>
                <td>{{ $pembekuan->pengemasanKarton->standar_jumlah_karton ?? '-' }}</td>
                <td>{{ $pembekuan->pengemasanKarton->aktual_jumlah_karton ?? '-' }}</td>
                <td>
                    @php
                        $isiPack = $pembekuan->pengemasanKarton->kesesuaian_isi_pack;
                        if (is_string($isiPack)) $isiPack = json_decode($isiPack, true);
                        if (is_array($isiPack)) {
                            $isiPack = array_map(fn($v) => $v === '✔' ? 'OK' : ($v === '✘' ? 'Tidak OK' : $v), $isiPack);
                        }
                    @endphp
                    @if(is_array($isiPack) && count($isiPack) > 0)
                        {{ implode(', ', $isiPack) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

{{-- F. DOKUMENTASI (LANGKAH 7) --}}
    <div class="section-title">F. DOKUMENTASI (LANGKAH 7)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width:20%;">Foto Kode Produksi Primer & Best Before</th>
                <th style="width:20%;">Foto Kode Produksi Sekunder & Best Before</th>
                <th style="width:20%;">QR Code</th>
                <th style="width:20%;">Foto Label Polyroll</th>
                <th style="width:20%;">Label Supplier</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height:80px;">
                <td style="text-align:center;vertical-align:middle;">
                    @if($pembekuan->foto_kode_produksi)
                        <img src="{{ public_path('storage/' . $pembekuan->foto_kode_produksi) }}" style="max-height:70px;max-width:140px;">
                    @else
                        <span style="color:#aaa;">Tidak ada foto</span>
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @php $sekunderVal = $pembekuan->foto_kode_produksi_sekunder; @endphp
                    @if(in_array($sekunderVal, ['OK', 'Not OK']))
                        <span style="font-weight:bold;color:{{ $sekunderVal === 'OK' ? 'green' : 'red' }};">{{ $sekunderVal }}</span>
                    @elseif($sekunderVal && $sekunderVal !== '-')
                        <img src="{{ public_path('storage/' . $sekunderVal) }}" style="max-height:70px;max-width:140px;">
                    @else
                        <span style="color:#aaa;">Tidak ada foto</span>
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($pembekuan->qr_code)
                        <img src="{{ public_path('storage/' . $pembekuan->qr_code) }}" style="max-height:70px;max-width:140px;">
                    @else
                        <span style="color:#aaa;">Tidak ada foto</span>
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($pembekuan->label_polyroll)
                        <img src="{{ public_path('storage/' . $pembekuan->label_polyroll) }}" style="max-height:70px;max-width:140px;">
                    @else
                        <span style="color:#aaa;">Tidak ada foto</span>
                    @endif
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    @if($pembekuan->label_supplier)
                        <img src="{{ public_path('storage/' . $pembekuan->label_supplier) }}" style="max-height:70px;max-width:140px;">
                    @else
                        <span style="color:#aaa;">Tidak ada foto</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:left; vertical-align:top; height:40px;">
                    <strong>Keterangan:</strong><br>
                    {{ $pembekuan->pengemasanKarton->keterangan ?? '-' }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left; vertical-align:top; height:30px;">
                    <strong>Status Release Produk:</strong><br>
                    @if(($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'ya')
                        Ya
                    @elseif(($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'tidak')
                        Tidak
                    @else
                        -
                    @endif
                </td>
                <td colspan="2" style="text-align:left; vertical-align:top; height:30px;">
                    <strong>Tindakan Koreksi:</strong><br>
                    {{ $pembekuan->pengemasanProduk->tindakan_koreksi ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">{{ $kode_form ?? 'QF 15/00' }}</div>

    {{-- TANDA TANGAN DENGAN QR CODE --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC'; $base64QcSvg = null;
        if ($pembekuan->approved_by_qc && $pembekuan->qc_approved_by) {
            $qcApprover = \App\Models\User::find($pembekuan->qc_approved_by);
            if ($qcApprover) {
                $qcApproverName = $qcApprover->name;
                $qcApprovalDate = $pembekuan->qc_approved_at ? \Carbon\Carbon::parse($pembekuan->qc_approved_at)->format('Y-m-d') : '';
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
        }
        $spvApprover = null; $spvApproverName = 'SUPERVISOR QC'; $base64SpvSvg = null;
        if ($pembekuan->approved_by_spv && $pembekuan->spv_approved_by) {
            $spvApprover = \App\Models\User::find($pembekuan->spv_approved_by);
            if ($spvApprover) {
                $spvApproverName = $spvApprover->name;
                $spvApprovalDate = $pembekuan->spv_approved_at ? \Carbon\Carbon::parse($pembekuan->spv_approved_at)->format('Y-m-d') : '';
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
        }
        $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI'; $base64ProduksiSvg = null;
        if ($pembekuan->approved_by_produksi && $pembekuan->produksi_approved_by) {
            $produksiApprover = \App\Models\User::find($pembekuan->produksi_approved_by);
            if ($produksiApprover) {
                $produksiApproverName = $produksiApprover->name;
                $produksiApprovalDate = $pembekuan->produksi_approved_at ? \Carbon\Carbon::parse($pembekuan->produksi_approved_at)->format('Y-m-d') : '';
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
        }
    @endphp
    <table class="signature-table">
        <tr>
            <td class="signature-label">Dibuat Oleh:</td>
            <td class="signature-label">Diperiksa Oleh:</td>
            <td class="signature-label">Diketahui Oleh:</td>
        </tr>
        <tr>
            <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
            <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
            <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
        </tr>
        <tr>
            <td><div class="signature-line"></div></td>
            <td><div class="signature-line"></div></td>
            <td><div class="signature-line"></div></td>
        </tr>
        <tr>
            <td class="signature-name">{{ $qcApproverName }}</td>
            <td class="signature-name">{{ $spvApproverName }}</td>
            <td class="signature-name">{{ $produksiApproverName }}</td>
        </tr>
    </table>
    
    @if(!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
    @endforeach
    @else
        <div style="text-align: center; margin-top: 50px;">
            <h3>Tidak ada data untuk diexport.</h3>
        </div>
    @endif

</body>
</html>
