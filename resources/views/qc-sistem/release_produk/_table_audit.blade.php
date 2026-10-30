@forelse($records as $item)
<tr class="item-row">
    <td class="text-center align-middle">{{ $records->firstItem() + $loop->index }}</td>
    <td class="text-center align-middle">
        <span class="badge badge-outline-info">Shift {{ $item->shift->shift ?? '-' }}</span>
    </td>
    <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
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
        <x-action-buttons :item="$item" route-prefix="release-produk" :show-print="true"/>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center py-5">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <p class="text-muted">Tidak ada data ditemukan</p>
    </td>
</tr>
@endforelse