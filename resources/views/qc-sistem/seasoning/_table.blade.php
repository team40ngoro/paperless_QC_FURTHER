@php
    $colspanTotal = 10;
    $rowNumber = $records->firstItem() ?? 1;
@endphp
@forelse($records as $groupKey => $groupItems)
    @foreach($groupItems as $item)
        <tr class="item-row {{ ($item->approved_by_qc && $item->approved_by_spv) ? 'row-verified' : '' }}">
            <td class="text-center align-middle">{{ $rowNumber }}</td>
            <td class="text-center align-middle">
                <span class="badge badge-outline-primary">Shift {{ $item->shift->shift ?? '-' }}</span>
            </td>
            <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
            <td class="text-center align-middle">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
            <td class="text-center align-middle"><code>{{ $item->kode_produksi ?? '-' }}</code></td>
            <td class="align-middle">
                <div class="font-weight-bold">{{ $item->nama_rm ?? '-' }}</div>
                <small class="text-muted">{{ $item->keterangan ?? '-' }}</small>
            </td>
            <td class="text-center align-middle">{{ $item->berat ?? '-' }} kg</td>
            <td class="text-center align-middle">
                <span class="small text-muted">{{ $item->createdBy->name ?? '-' }}</span>
            </td>
            <td class="align-middle">
                <div class="status-badges d-flex flex-column align-items-center">
                    <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                    </span>
                    <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                    </span>
                </div>
            </td>
            <td class="text-center align-middle">
                <x-action-buttons :item="$item" route-prefix="seasoning" class="tombol" :show-history="true" :show-view="true" />
                {{-- Global Audit Dropdown --}}
                @if(!isset($isAuditPage) || !$isAuditPage)
                <x-audit-dropdown :item="$item" class="tombol" route-prefix="seasoning" />
                @endif
            </td>
        </tr>
        @php $rowNumber++; @endphp
    @endforeach
@empty
    <tr>
        <td colspan="{{ $colspanTotal }}" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data seasoning ditemukan</p>
            </div>
        </td>
    </tr>
@endforelse
<style>
    /* Memastikan tombol aksi berbentuk kotak dan tidak gepeng */
    .tombol {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
    }
    .tombol i {
        font-size: 0.85rem !important;
    }
</style>