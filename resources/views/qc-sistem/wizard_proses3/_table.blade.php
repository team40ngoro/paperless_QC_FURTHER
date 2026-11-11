@forelse($records->getCollection()->flatten(1) as $item)
    <tr>
        <td class="align-middle">
            <input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
        </td>
        <td>{{ $records->firstItem() + $loop->index }}</td>
        <td>{{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}</td>
        <td>{{ $item->inputRoastingData->jam ?? ($item->jam ?? '-') }}</td>
        <td>{{ $item->shift->shift ?? '-' }}</td>
        <td class="text-left"><strong>{{ $item->inputRoastingData->produk->nama_produk ?? ($item->prosesRoastingFanData->produk->nama_produk ?? ($item->hasilProsesRoastingData->produk->nama_produk ?? '-')) }}</strong></td>
        <td><span class="badge badge-light border">{{ $item->inputRoastingData->kode_produksi ?? '-' }}</span></td>
        <td>{{ $item->prosesRoastingFanData->aktual_lama_proses ?? '-' }}</td>
        <td>
            @php
                $suhuPusatRaw = $item->hasilProsesRoastingData->aktual_suhu_pusat ?? '';
                $suhuPusatList = [];
                
                if (is_array($suhuPusatRaw)) {
                    foreach ($suhuPusatRaw as $val) {
                        if (is_string($val) && $val !== '') {
                            preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                            foreach ($matches[0] as $match) {
                                $suhuPusatList[] = (float) str_replace(',', '.', $match);
                            }
                        } elseif (is_numeric($val)) {
                            $suhuPusatList[] = (float) str_replace(',', '.', $val);
                        }
                    }
                } elseif (is_string($suhuPusatRaw) && $suhuPusatRaw !== '') {
                    preg_match_all('/\d+[.,]\d+|\d+/', $suhuPusatRaw, $matches);
                    $suhuPusatList = array_map(fn($v) => (float) str_replace(',', '.', $v), $matches[0]);
                }
            @endphp
            @if(!empty($suhuPusatList))
                @foreach($suhuPusatList as $suhuVal)
                    <span class="badge badge-success mb-1">{{ $suhuVal }} °C</span><br>
                @endforeach
            @else
                <span class="badge badge-secondary">-</span>
            @endif
        </td>
        <td class="align-middle">
            <div class="status-badges d-flex flex-column align-items-center">
                @if($item->hasilProsesRoastingData)
                    <span class="badge badge-info mb-1 w-100" style="font-size: 0.7rem;" title="Verifikasi Hasil Akhir oleh QC sudah diisi">
                        <i class="fas fa-check-double mr-1"></i> Input QC OK
                    </span>
                @else
                    <span class="badge badge-warning mb-1 w-100" style="font-size: 0.7rem;" title="Menunggu QC mengisi Verifikasi Hasil Akhir via ikon Mata">
                        <i class="fas fa-hourglass-half mr-1"></i> Menunggu QC
                    </span>
                @endif
                <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> 2. QC
                </span>
                <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> 3. PROD
                </span>
                <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> 4. SPV
                </span>
            </div>
        </td>
        <td class="text-center align-middle">
            <x-action-buttons 
                :item="$item" 
                route-prefix="wizard.proses3" 
                permission-prefix="wizard-proses3"
                :show-history="false"
                :show-view="true"
                :show-print="true" 
            />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
    </tr>
@endforelse
