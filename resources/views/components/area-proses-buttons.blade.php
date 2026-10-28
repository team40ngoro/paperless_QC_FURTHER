@if($canAccess())
    <div class="btn-vertical">
        @if(!$hasGroupUuid && Auth::user()->hasPermissionTo('edit-area-proses'))
            <a href="{{ route('area-proses.edit', $item->uuid) . ($isAuditPage ? '?is_audit=1' : '') }}" class="btn btn-warning btn-sm" title="Edit Data">
                <i class="fas fa-edit"></i>
            </a>
        @endif
        
        <!-- Tombol Edit Per 2 Jam selalu muncul untuk role yang memiliki akses -->
        @if(Auth::user()->hasPermissionTo('edit-area-proses'))
        <a href="{{ route('area-proses.twohour.edit', $item->uuid) . ($isAuditPage ? '?is_audit=1' : '') }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
            <i class="fas fa-clock"></i>
        </a>
        @endif
        
        @if(!$hasGroupUuid && Auth::user()->hasPermissionTo('view-area-proses'))
            <a href="{{ route('area-proses.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                <i class="fas fa-history"></i>
            </a>
            <a href="{{ route('area-proses.show', $item->uuid) }}" class="btn btn-primary btn-sm" title="Lihat Detail (Riwayat)">
                <i class="fas fa-eye"></i>
            </a>
            <!-- <a href="{{ route('area-proses.export-pdf', $item->uuid) }}" class="btn btn-primary btn-sm" title="Cetak PDF (Grup)" target="_blank">
                <i class="fas fa-file-pdf"></i>
            </a> -->
        @endif
        
        @if(Auth::user()->hasPermissionTo('delete-area-proses'))
        <form action="{{ route('area-proses.destroy', $item->uuid) . $queryString }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
            @csrf 
            @method('DELETE')
            <button class="btn btn-danger btn-sm" title="Hapus Data">
                <i class="fas fa-trash"></i>
            </button>
        </form>
        @endif
    </div>
@elseif($isViewOnlyRole())
    <!-- Role QC dan FM/FL bisa edit per 2 jam dan lihat riwayat -->
    <div class="btn-vertical">
        <!-- Tombol Edit Per 2 Jam untuk QC dan FM/FL -->
        <a href="{{ route('area-proses.twohour.edit', $item->uuid) . ($isAuditPage ? '?is_audit=1' : '') }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
            <i class="fas fa-clock"></i>
        </a>
        
        @if(!$hasGroupUuid)
            <a href="{{ route('area-proses.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                <i class="fas fa-history"></i>
            </a>
            <a href="{{ route('area-proses.show', $item->uuid) }}" class="btn btn-primary btn-sm" title="Lihat Detail (Riwayat)">
                <i class="fas fa-eye"></i>
            </a>
            <!-- <a href="{{ route('area-proses.export-pdf', $item->uuid) }}" class="btn btn-primary btn-sm" title="Cetak PDF (Grup)" target="_blank">
                <i class="fas fa-print"></i>
            </a> -->
        @endif
    </div>
@else
    <!-- Role lain tidak memiliki akses -->
    <span class="badge badge-secondary">
        <i class="fas fa-lock"></i> No Access
    </span>
@endif
