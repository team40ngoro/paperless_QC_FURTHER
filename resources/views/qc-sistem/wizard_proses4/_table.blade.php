@forelse($records as $groupKey => $group)
    @php
        $first = $group->first();
        $itemDate = $first->tanggal ? $first->tanggal->format('Y-m-d') : 'no_date';
        $shiftId = $first->bahanBaku->shift_id ?? 'no_shift';
        $shiftName = $first->bahanBaku->shift->shift ?? '-';
        $shiftGroupKey = $itemDate . '_' . $shiftId;
        static $currentGroup = null;
    @endphp

    @if($currentGroup !== $shiftGroupKey)
        <tr class="bg-light">
            <td colspan="10" class="text-left py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <strong>{{ $first->tanggal ? $first->tanggal->format('d/m/Y') : '-' }}</strong>
                        <span class="mx-2">|</span>
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
        <td>{{ $item->bahanBaku->shift->shift ?? '-' }}</td>
        <td>{{ $item->prosesTumbling->produk->nama_produk ?? '-' }}</td>
        <td>{{ $item->bahanBaku->kode_produksi ?? '-' }}</td>
        <td>
            {{ $item->waktu_mulai_aging }} - {{ $item->waktu_selesai_aging }}
        </td>
        <td>
            @php
                $suhuClean = preg_replace('/\s*\([✓✘]\)/u', '', $item->suhu_produk);
            @endphp
            {{ $suhuClean }}
        </td>
        <td>
            @if($item->kondisi_produk == '✓')
                <span class="badge badge-success"><i class="fas fa-check"></i> OK</span>
            @else
                <span class="badge badge-danger"><i class="fas fa-times"></i> Not OK</span>
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
                route-prefix="wizard.proses4"
                permission-prefix="wizard-proses4"
                :show-view="true"
                :show-print="true"
                :show-history="false"
            />
        </td>
    </tr>
    @endforeach
@empty
    <tr>
        <td colspan="10" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
    </tr>
@endforelse