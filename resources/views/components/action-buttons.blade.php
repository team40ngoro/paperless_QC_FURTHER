@if($canAccess())
    <div class="d-flex {{ $layout === 'vertical' ? 'flex-column' : 'flex-row flex-nowrap justify-content-center' }} align-items-center" style="gap: 5px;">
        @if($showView && $hasRoute($routePrefix . '.show'))
            <a href="{{ route($routePrefix . '.show', $item->uuid) }}" 
               {{ $attributes->merge(['class' => 'btn btn-info btn-sm']) }}
               data-toggle="tooltip" title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>
        @endif

        @if($showQcVerificationLog && $hasRoute($routePrefix . '.qc-verifications.index'))
            <a href="{{ route($routePrefix . '.qc-verifications.index', $item->uuid) }}"
               {{ $attributes->merge(['class' => 'btn btn-success btn-sm']) }}
               data-toggle="tooltip" title="Log Verifikasi QC">
                <i class="fas fa-clipboard-check"></i>
            </a>
        @endif
        
        @if($hasRoute($routePrefix . '.edit') && $hasEditPermission())
            @php
                $remaining = $getRemainingTime();
                $deadline = $getEditDeadline();
                $editTitle = $isAuditPage
                    ? 'Edit Data Audit (auto-copy jika belum ada)'
                    : ($remaining ? "Edit ($remaining, Deadline: $deadline)" : "Edit");
            @endphp
            
            @if($canEdit())
                @if($isAuditPage && $hasRoute($routePrefix . '.copy-to-audit'))
                    {{-- Di halaman Audit: arahkan ke copy-to-audit (Opsi C) --}}
                    <a href="{{ route($routePrefix . '.copy-to-audit', $item->uuid) }}" 
                       {{ $attributes->merge(['class' => 'btn btn-warning btn-sm']) }}
                       data-toggle="tooltip" data-placement="top" title="{{ $editTitle }}">
                        <i class="fas fa-edit"></i>
                    </a>
                @else
                    {{-- Di halaman Operasional: arahkan ke edit biasa --}}
                    <a href="{{ route($routePrefix . '.edit', $item->uuid) }}" 
                       {{ $attributes->merge(['class' => 'btn btn-warning btn-sm']) }}
                       data-toggle="tooltip" data-placement="top" title="{{ $editTitle }}">
                        <i class="fas fa-edit"></i>
                    </a>
                @endif
            @else
                <button {{ $attributes->merge(['class' => 'btn btn-secondary btn-sm']) }} disabled 
                        data-toggle="tooltip" data-placement="top" title="Waktu edit habis (Deadline: {{ $deadline }})">
                    <i class="fas fa-lock"></i>
                </button>
            @endif
        @endif

        <!-- @if($showPrint)
            @if($hasRoute($routePrefix . '.export-pdf'))
                <a href="{{ route($routePrefix . '.export-pdf', $item->uuid) }}" 
                   {{ $attributes->merge(['class' => 'btn btn-danger btn-sm']) }}
                   data-toggle="tooltip" title="Cetak PDF" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
            @elseif($hasRoute($routePrefix . '.pdf'))
                <a href="{{ route($routePrefix . '.pdf', $item->uuid) }}" 
                   {{ $attributes->merge(['class' => 'btn btn-danger btn-sm']) }}
                   data-toggle="tooltip" title="Cetak PDF" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
            @endif
        @endif -->
        
        @if($shouldShowHistory() && $hasRoute($routePrefix . '.logs'))
            <a href="{{ route($routePrefix . '.logs', $item->uuid) }}" 
               {{ $attributes->merge(['class' => 'btn btn-secondary btn-sm']) }}
               data-toggle="tooltip" title="History">
                <i class="fas fa-history"></i>
            </a>
        @endif
        
        @if($hasRoute($routePrefix . '.destroy') && $hasDeletePermission())
            <form action="{{ route($routePrefix . '.destroy', $item->uuid) }}" method="POST" 
                  class="p-0 m-0 d-inline-flex" 
                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" {{ $attributes->merge(['class' => 'btn btn-danger btn-sm']) }} 
                        data-toggle="tooltip" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endif
        
        {{ $slot }}
    </div>
    
    {{-- Display time badge BELOW buttons --}}
    @if($hasRoute($routePrefix . '.edit') && $hasEditPermission())
        @php
            $remaining = $getRemainingTime();
            $deadline = $getEditDeadline();
        @endphp
        
        @if($canEdit() && $remaining)
            <div class="mt-2 text-center">
                <span class="badge badge-secondary" style="font-size: 0.75rem;" 
                      data-toggle="tooltip" data-placement="top" title="Waktu edit tersisa sampai {{ $deadline }}">
                    <i class="far fa-clock"></i> Waktu Edit Tersisa : {{ $remaining }}
                </span>
            </div>
        @elseif($canEdit() && !$remaining)
            <div class="mt-2 text-center">
                <span class="badge badge-success" style="font-size: 0.75rem;" 
                      data-toggle="tooltip" data-placement="top" title="Tidak ada batas waktu edit">
                    <i class="fas fa-infinity"></i> Unlimited Edit
                </span>
            </div>
        @elseif(!$canEdit())
            <div class="mt-2 text-center">
                <span class="badge badge-danger" style="font-size: 0.75rem;">
                    <i class="fas fa-ban"></i> Expired To Edit
                </span>
            </div>
        @endif
    @endif
    
    @elseif($isViewOnlyRole())
        <!-- Role QC dan Fm/fl hanya bisa lihat -->
        @if($showView && $hasRoute($routePrefix . '.show'))
            <a href="{{ route($routePrefix . '.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                <i class="fas fa-eye"></i> Lihat
            </a>
            @if($showQcVerificationLog && $hasRoute($routePrefix . '.qc-verifications.index'))
                <a href="{{ route($routePrefix . '.qc-verifications.index', $item->uuid) }}" class="btn btn-success btn-sm" title="Log Verifikasi QC">
                    <i class="fas fa-clipboard-check"></i>
                </a>
            @endif
        @elseif($shouldShowHistory() && $hasRoute($routePrefix . '.logs'))
            <a href="{{ route($routePrefix . '.logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="Lihat History">
                <i class="fas fa-history"></i> History
            </a>
        @else
            <span class="badge badge-info">
                <i class="fas fa-eye"></i> View Only ({{ Auth::user()->role }})
            </span>
        @endif
    @else
        <!-- Role lain tidak memiliki akses -->
        <span class="badge badge-secondary">
            <i class="fas fa-lock"></i> No Access
        </span>
    @endif