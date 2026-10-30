@props(['records', 'columns' => [], 'routePrefix' => 'chillroom', 'permissionPrefix' => null, 'dateColumn' => 'tanggal', 'timeColumn' => 'jam_kedatangan', 'shiftRelation' => 'datashift', 'shiftDisplay' => 'shift', 'groupByDateShift' => true])

@php
    $permissionPrefix = $permissionPrefix ?? $routePrefix;
    $userRole = auth()->user()->id_role ?? null;
@endphp

@forelse($records as $groupKey => $groupItems)
    @php
        $firstItem = $groupItems->first();
        $date = $firstItem->{$dateColumn} 
            ? (is_string($firstItem->{$dateColumn}) 
                ? \Carbon\Carbon::parse($firstItem->{$dateColumn})->format('Y-m-d') 
                : $firstItem->{$dateColumn}->format('Y-m-d')) 
            : null;
        $shiftId = $firstItem->shift_id ?? $firstItem->shift ?? null;
        $shiftName = $firstItem->{$shiftRelation}->{$shiftDisplay} ?? '-';
        $count = $groupItems->count();
        
        $allApprovedQc = $groupItems->every(fn($item) => $item->approved_by_qc ?? false);
        $allApprovedProduksi = $groupItems->every(fn($item) => $item->approved_by_produksi ?? false);
        $allApprovedSpv = $groupItems->every(fn($item) => $item->approved_by_spv ?? false);
    @endphp

    {{-- Group Header --}}
    <tr class="bg-light shadow-sm group-header" data-date="{{ $date }}" data-shift="{{ $shiftId }}">
        <td colspan="{{ count($columns) + 2 }}" class="text-left py-3 px-4">
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

                <div class="approval-actions-flex d-flex align-items-center mt-2 mt-md-0">
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

    {{-- Data Rows --}}
    @foreach($groupItems as $item)
        <tr class="item-row {{ ($item->approved_by_qc ?? false) && ($item->approved_by_produksi ?? false) && ($item->approved_by_spv ?? false) ? 'row-verified' : '' }}">
            <td class="text-center align-middle">{{ $loop->iteration }}</td>
            
            {{-- Dynamic columns from $columns prop --}}
            @foreach($columns as $col)
                @php
                    $value = data_get($item, $col['key']);
                    $formatter = $col['formatter'] ?? null;
                    $display = $formatter ? $formatter($item, $value) : $value;
                    $attrs = $col['attrs'] ?? '';
                @endphp
                <td class="{{ $col['class'] ?? 'align-middle' }}" {{ $attrs }}>
                    {{ $display }}
                </td>
            @endforeach

            {{-- Status Approval Badges --}}
            <td class="align-middle">
                <div class="status-badges d-flex flex-column align-items-center">
                    <span class="badge {{ ($item->approved_by_qc ?? false) ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ ($item->approved_by_qc ?? false) ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                    </span>
                    <span class="badge {{ ($item->approved_by_produksi ?? false) ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ ($item->approved_by_produksi ?? false) ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                    </span>
                    <span class="badge {{ ($item->approved_by_spv ?? false) ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                        <i class="fas {{ ($item->approved_by_spv ?? false) ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                    </span>
                </div>
            </td>

            {{-- Action Buttons --}}
            <td class="text-center align-middle">
                <x-action-buttons 
                    :item="$item" 
                    route-prefix="{{ $routePrefix }}" 
                    permission-prefix="{{ $permissionPrefix }}"
                    :show-history="true"
                    :show-view="true"
                    :show-print="false"
                    layout="horizontal"
                />
                <x-audit-dropdown :item="$item" route-prefix="{{ $routePrefix }}" />
            </td>
        </tr>
    @endforeach
@empty
    <tr>
        <td colspan="{{ count($columns) + 2 }}" class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Tidak ada data ditemukan.</p>
        </td>
    </tr>
@endforelse