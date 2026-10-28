@forelse($records as $item)
<tr>
    <td>{{ $records->firstItem() + $loop->index }}</td>
    <td class="text-left">
        <div>
            <span class="badge badge-outline-secondary mr-2">
                {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
            </span>
            <strong>{{ $item->produk->nama_produk ?? '-' }}</strong>
            <span class="mx-2 text-muted">|</span>
            <span class="text-info">{{ $item->kode_produksi }}</span>
            <span class="mx-2 text-muted">|</span>
            <small class="text-muted">Gramase: <b>{{ $item->gramase }}g</b></small>
        </div>
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
        <x-action-buttons :item="$item" route-prefix="verifikasi-berat-produk" :show-print="true" />
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
</tr>
@endforelse