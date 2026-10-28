@forelse($records as $groupKey => $groupItems)
    @php
        $firstItem = $groupItems->first();
        $date = $firstItem->tanggal ? (is_string($firstItem->tanggal) ? \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d') : $firstItem->tanggal->format('Y-m-d')) : null;
        $shiftId = $firstItem->id_shift;
        $shiftName = $firstItem->shift->shift ?? '-';
        $count = $groupItems->count();
        
        $allApprovedQc = $groupItems->every(fn($item) => $item->approved_by_qc);
        $allApprovedProduksi = $groupItems->every(fn($item) => $item->approved_by_produksi);
        $allApprovedSpv = $groupItems->every(fn($item) => $item->approved_by_spv);
    @endphp

    <!-- Group Header -->
    <tr class="bg-light shadow-sm group-header" data-date="{{ $date }}" data-shift="{{ $shiftId }}">
        <td colspan="9" class="text-left py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="group-info d-flex align-items-center">
                    <div class="date-badge mr-3">
                        <span class="badge badge-dark p-2" style="font-size: 0.95rem;">
                            <i class="fas fa-calendar-alt mr-2 text-warning"></i>
                            {{ $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="shift-badge mr-3">
                        <span class="badge badge-info p-2" style="font-size: 0.95rem; box-shadow: 0 2px 4px rgba(23,162,184,0.3);">
                            <i class="fas fa-clock mr-2 text-white"></i>
                            Shift: {{ $shiftName }}
                        </span>
                    </div>
                    <div class="count-badge">
                        <span class="badge badge-secondary p-2" style="font-size: 0.85rem;">
                            {{ $count }} Baris
                        </span>
                    </div>
                </div>

                <div class="approval-actions d-flex align-items-center mt-2 mt-md-0">
                    @php
                        $userRole = auth()->user()->id_role ?? null;
                    @endphp
                    <span class="mr-2 text-muted font-weight-bold small text-uppercase">VERIFIKASI GROUP:</span>
                    <div class="btn-group shadow-sm" role="group">
                        @if(in_array($userRole, [1, 3, 5]))
                            <button type="button" 
                                    class="btn btn-sm {{ $allApprovedQc ? 'btn-success' : 'btn-outline-success' }} bulk-approve-btn"
                                    data-type="qc" data-tanggal="{{ $date }}" data-shift-id="{{ $shiftId }}"
                                    {{ $allApprovedQc ? 'disabled' : '' }}
                                    style="font-weight: 600; font-size: 0.75rem;">
                                <i class="fas {{ $allApprovedQc ? 'fa-check-circle' : 'fa-check' }} mr-1"></i> QC
                            </button>
                        @endif

                        @if(in_array($userRole, [1, 2, 5]))
                            <button type="button" 
                                    class="btn btn-sm {{ $allApprovedProduksi ? 'btn-primary' : 'btn-outline-primary' }} bulk-approve-btn"
                                    data-type="produksi" data-tanggal="{{ $date }}" data-shift-id="{{ $shiftId }}"
                                    {{ $allApprovedProduksi || !$allApprovedQc ? 'disabled' : '' }}
                                    title="{{ !$allApprovedQc ? 'Menunggu QC' : '' }}"
                                    style="font-weight: 600; font-size: 0.75rem;">
                                <i class="fas {{ $allApprovedProduksi ? 'fa-check-circle' : 'fa-check' }} mr-1"></i> PRODUKSI
                            </button>
                        @endif

                        @if(in_array($userRole, [1, 4, 5]))
                            <button type="button" 
                                    class="btn btn-sm {{ $allApprovedSpv ? 'btn-dark' : 'btn-outline-dark' }} bulk-approve-btn"
                                    data-type="spv" data-tanggal="{{ $date }}" data-shift-id="{{ $shiftId }}"
                                    {{ $allApprovedSpv || !$allApprovedProduksi ? 'disabled' : '' }}
                                    title="{{ !$allApprovedProduksi ? 'Menunggu Produksi' : '' }}"
                                    style="font-weight: 600; font-size: 0.75rem;">
                                <i class="fas {{ $allApprovedSpv ? 'fa-check-circle' : 'fa-check' }} mr-1"></i> SPV
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </td>
    </tr>

    @foreach($groupItems as $item)
        <tr class="item-row {{ $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv ? 'row-verified' : '' }}">
            <td class="text-center align-middle">{{ $loop->iteration }}</td>
            <td class="text-center align-middle">
                <span class="badge badge-outline-info">Shift {{ $item->shift->shift ?? '-' }}</span>
            </td>
            <td class="text-center align-middle">{{ $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : $item->tanggal->format('d/m/Y')) : '-' }}</td>
            <td class="text-center align-middle">{{ $item->jam ?? '-' }}</td>
            <td class="align-middle">
                <div class="font-weight-bold">{{ $item->produk->nama_produk ?? '-' }}</div>
            </td>
            <td class="text-center align-middle">{{ $item->suhu_iqf }} ℃ / {{ $item->suhu_produk }} ℃</td>
            <td class="text-center align-middle">
                @if($item->status == 'Release')
                    <span class="badge badge-success">Release</span>
                @else
                    <span class="badge badge-danger">Tidak Release</span>
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
                <div class="row-actions">
                    <x-action-buttons :item="$item" route-prefix="release-produk" :show-print="true"/>
                    <x-audit-dropdown :item="$item" route-prefix="release-produk" permission-prefix="release-produk" :uuid="$item->uuid" />
                </div>
            </td>
        </tr>
    @endforeach
@empty
    <tr>
        <td colspan="9" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data release produk ditemukan</p>
            </div>
        </td>
    </tr>
@endforelse
