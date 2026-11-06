@forelse($records as $item)
<tr>
    <td>{{ $records->firstItem() + $loop->index }}</td>
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
    <td>{{ $item->shift->shift ?? '-' }}</td>
    <td>{{ $item->plan->nama_plan ?? '-' }}</td>
    <td>
        <div>
            <strong>{{ $item->details->first()->produk->nama_produk ?? '-' }}</strong>
            @if($item->details->first() && $item->details->first()->berat)
                <span class="badge badge-secondary ml-1">{{ rtrim(rtrim(number_format($item->details->first()->berat, 2), '0'), '.') }} gram</span>
            @endif
        </div>
        @php
            $hasUnverified = $item->details->contains(function($d) { return empty($d->hasil_pengecekan); });
        @endphp
        <div class="mt-1">
            @if($hasUnverified)
                <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fas fa-clock mr-1"></i>Menunggu Verifikasi QC</span>
            @else
                <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fas fa-check-circle mr-1"></i>Terverifikasi QC</span>
            @endif
        </div>
    </td>
    <td>
        @if($item->details->first() && $item->details->first()->label_produk)
            <span class="badge badge-info">{{ $item->details->first()->label_produk }}</span>
        @else
            -
        @endif
    </td>
    <td>{{ $item->user->name ?? '-' }}</td>
    <td>{{ $item->details->first()->jam ?? '-' }}</td>
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
        <x-action-buttons :item="$item" route-prefix="label-pemeriksaan" :show-print="true" :isAuditPage="$isAuditPage ?? false" />
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
</tr>
@endforelse
