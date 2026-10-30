@forelse($records as $groupKey => $group)
    @php
        $first = $group->first();
        $itemDate = $first->tanggal ? $first->tanggal->format('Y-m-d') : 'no_date';
        $shiftId = $first->shift_id ?? 'no_shift';
        $shiftName = $first->shift->shift ?? '-';
        $shiftGroupKey = $itemDate . '_' . $shiftId;
        static $currentGroup = null;
    @endphp

    @if($currentGroup !== $shiftGroupKey)
        <tr class="bg-light">
            <td colspan="11" class="text-left py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <strong>{{ $first->tanggal ? $first->tanggal->format('d/m/Y') : '-' }}</strong>
                        <span class="mx-2">|</span>
                        <i class="fas fa-clock mr-1"></i>
                        <strong>Shift {{ $shiftName }}</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small">
                            <i class="fas fa-layer-group mr-1"></i> {{ $group->count() }} Data
                        </span>
                    </div>
                </div>
            </td>
        </tr>
        @php $currentGroup = $shiftGroupKey; @endphp
    @endif

    @foreach($group as $item)
    <tr>
        <td>{{ $records->firstItem() + ($loop->parent->index * $group->count()) + $loop->index }}</td>
        <td>{{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}</td>
        <td>{{ $item->jam ?? '-' }}</td>
        <td>{{ $item->shift->shift ?? '-' }}</td>
        <td>{{ $item->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</td>
        <td><span class="badge badge-light border">{{ $item->penggorenganData->kode_produksi ?? '-' }}</span></td>
        <td>{{ $item->suhu_ruang_iqf ?? '-' }} °C</td>
        <td>{{ $item->holding_time ?? '-' }} menit</td>
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
                    <span class="badge badge-success mr-1 mb-1">{{ $suhuVal }} °C</span>
                @endforeach
            @else
                <span class="badge badge-light border">-</span>
            @endif
        </td>
        <td class="align-middle">
            <div class="status-badges d-flex flex-column align-items-center">
                <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                </span>
                <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                </span>
                <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                    <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                </span>
            </div>
        </td>
        <td class="text-center align-middle">
            <x-action-buttons 
                :item="$item" 
                route-prefix="wizard.proses2" 
                permission-prefix="wizard-proses2"
                :show-history="false"
                :show-view="true"
                :show-print="true" 
            />
        </td>
    </tr>
    @endforeach
@empty
    <tr>
        <td colspan="11" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
    </tr>
@endforelse
