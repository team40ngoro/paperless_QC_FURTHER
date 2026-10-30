@if($canAccess)
    <div class="btn-vertical mb-1">
        @if(!$hasGroupUuid)
            <a href="{{ route('penyimpanan-bahan.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                <i class="fas fa-edit"></i>
            </a>
        @endif
        
        <!-- Tombol Edit Per 2 Jam selalu muncul untuk role yang memiliki akses -->
        <a href="{{ route('penyimpanan-bahan.twohour.edit', $item->uuid) }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
            <i class="fas fa-clock"></i>
        </a>
        
        @if(!$hasGroupUuid)
            <a href="{{ route('penyimpanan-bahan.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                <i class="fas fa-history"></i>
            </a>
        @endif
        
        <form action="{{ route('penyimpanan-bahan.destroy', $item->uuid) . $queryString }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
            @csrf 
            @method('DELETE')
            <button class="btn btn-danger btn-sm" title="Hapus Data">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
@elseif($isViewOnlyRole)
    <!-- Role QC dan FM/FL bisa edit per 2 jam dan lihat riwayat -->
    <div class="btn-vertical mb-1">
        <!-- Tombol Edit Per 2 Jam untuk QC dan FM/FL -->
        <a href="{{ route('penyimpanan-bahan.twohour.edit', $item->uuid) }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
            <i class="fas fa-clock"></i>
        </a>
        
        @if(!$hasGroupUuid)
            <a href="{{ route('penyimpanan-bahan.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                <i class="fas fa-history"></i>
            </a>
        @endif
    </div>
@else
    <!-- Role lain tidak memiliki akses -->
    <span class="badge badge-secondary">
        <i class="fas fa-lock"></i> No Access
    </span>
@endif