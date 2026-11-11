@php
    // Kolom checkbox hanya tampil di halaman operasional, bukan di halaman audit.
    $colspanTotal = (isset($isAuditPage) && $isAuditPage) ? 11 : 12;
    $rowNumber = $records->firstItem() ?? 1;
@endphp
@forelse($records as $groupKey => $groupItems)
    @foreach($groupItems as $item)
        <tr class="item-row {{ ($item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv) ? 'row-verified' : '' }}">
            @unless(isset($isAuditPage) && $isAuditPage)
                <td class="text-center align-middle">
                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                </td>
            @endunless
            <td class="text-center align-middle">{{ $rowNumber }}</td>
            <td class="text-center align-middle">
                <span class="badge badge-outline-primary">Shift {{ $item->shift->shift ?? '-' }}</span>
            </td>
            <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
            <td class="text-center align-middle">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
            <td class="text-center align-middle"><code>{{ $item->kode_produksi ?? '-' }}</code></td>
            <td class="align-middle">
                <div class="font-weight-bold">{{ $item->nama_produk ?? '-' }}</div>
                <small class="text-muted">BB: {{ $item->best_before ? \Carbon\Carbon::parse($item->best_before)->format('d/m/Y') : '-' }}</small>
            </td>
            <td class="text-center align-middle">{{ $item->isi_jumlah ?? '-' }}</td>
            <td class="text-center align-middle">
                <span class="">{{ $item->labelisasi ?? '-' }}</span>
            </td>
            <td class="align-middle">
                <div class="small">
                    <i class="fas fa-user-edit text-muted mr-1"></i>{{ $item->createdBy->name ?? '-' }}
                    @if($item->createdBy)
                        <span class="text-muted">({{ $item->createdBy->roleModel->role ?? $item->createdBy->role ?? '-' }})</span>
                    @endif
                </div>
                <div class="small">
                    @if($item->approved_by_produksi)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->produksiApprover->name ?? 'SPV Produksi' }}</span>
                        @if($item->produksiApprover)
                            <span class="text-muted">({{ $item->produksiApprover->roleModel->role ?? $item->produksiApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum diketahui</span>
                    @endif
                </div>
            </td>
            <td class="align-middle">
                <div class="small">
                    @if($item->approved_by_qc)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->qcApprover->name ?? 'QC Inspector' }}</span>
                        @if($item->qcApprover)
                            <span class="text-muted">({{ $item->qcApprover->roleModel->role ?? $item->qcApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum verifikasi</span>
                    @endif
                </div>
                <div class="small">
                    @if($item->approved_by_spv)
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $item->spvApprover->name ?? 'SPV QC' }}</span>
                        @if($item->spvApprover)
                            <span class="text-muted">({{ $item->spvApprover->roleModel->role ?? $item->spvApprover->role ?? '-' }})</span>
                        @endif
                    @else
                        <span class="text-muted"><i class="far fa-clock mr-1"></i>Belum disetujui</span>
                    @endif
                </div>
            </td>
            <td class="text-center align-middle">
                <x-action-buttons :item="$item" route-prefix="rebox" class="tombol" :show-history="true" :show-view="true" />
                @if(!isset($isAuditPage) || !$isAuditPage)
                <x-audit-dropdown :item="$item" class="tombol" route-prefix="rebox" />
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
                <p class="text-muted">Tidak ada data rebox ditemukan</p>
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