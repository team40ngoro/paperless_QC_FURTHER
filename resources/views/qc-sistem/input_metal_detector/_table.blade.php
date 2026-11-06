@php
use Illuminate\Support\Facades\Schema;
@endphp

@if(isset($metalDetector) && count($metalDetector))
<div class="table-responsive">
    <table style="white-space: nowrap;" class="text-center table table-bordered table-hover table-striped">
        <thead class="bg-light text-uppercase small font-weight-bold border-top">
            <tr>
                @if(!(isset($isAuditPage) && $isAuditPage))
                <th class="text-center align-middle" rowspan="2" style="width: 40px;">
                    <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                </th>
                @endif
                <th class="text-center align-middle" rowspan="2">No</th>
                <th class="text-center align-middle" rowspan="2">Shift</th>
                <th class="text-center align-middle" rowspan="2">Line</th>
                <th class="text-center align-middle" rowspan="2">Tanggal</th>
                <th class="text-center align-middle" rowspan="2">Jam</th>
                <!-- <th class="text-center align-middle" rowspan="2">Plan</th> -->
                <th class="text-center align-middle" rowspan="2">Nama Produk</th>
                <th class="text-center align-middle" rowspan="2">Kode Produksi</th>
                <!-- <th class="text-center bg-info" colspan="3">Fe 1.5 mm</th>
                <th class="text-center bg-success" colspan="3">Non Fe 2 mm</th>
                <th class="text-center bg-warning" colspan="3">SUS 316 2.5 mm</th> -->
                <!-- <th class="text-center align-middle" rowspan="2">Keterangan</th> -->
                <th class="text-center align-middle" rowspan="2">Dibuat Oleh</th>
                <th class="text-center align-middle" rowspan="2">Verifikasi QC</th>
                <th class="text-center align-middle" rowspan="2">Status Appr</th>
                <th class="text-center align-middle" rowspan="2">Aksi</th>
            </tr>
            <!-- <tr>
                <th class="text-center bg-info">Depan</th>
                <th class="text-center bg-info">Tengah</th>
                <th class="text-center bg-info">Belakang</th>
                <th class="text-center bg-success">Depan</th>
                <th class="text-center bg-success">Tengah</th>
                <th class="text-center bg-success">Belakang</th>
                <th class="text-center bg-warning">Depan</th>
                <th class="text-center bg-warning">Tengah</th>
                <th class="text-center bg-warning">Belakang</th>
            </tr> -->
        </thead>
        <tbody>
            @php
                $rows = collect();
                if ($metalDetector instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
                    $rows = collect($metalDetector->items());
                } elseif ($metalDetector instanceof \Illuminate\Support\Collection) {
                    $rows = $metalDetector;
                } elseif (is_array($metalDetector)) {
                    $rows = collect($metalDetector);
                }

                if ($rows->isNotEmpty() && $rows->first() instanceof \Illuminate\Support\Collection) {
                    $rows = $rows->flatten();
                }

                $no = $metalDetector instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
                    ? ($metalDetector->firstItem() ?? 1)
                    : 1;
                $userRole = auth()->user()->id_role ?? null;
            @endphp

            @forelse($rows as $item)
                @php
                    $itemUuid = is_object($item) ? (data_get($item, 'uuid') ?? '') : data_get($item, 'uuid', '');
                @endphp
                <tr class="item-row">
                @if(!(isset($isAuditPage) && $isAuditPage))
                <td class="text-center align-middle">
                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $itemUuid }}">
                </td>
                @endif
                <td class="text-center align-middle">{{ $no++ }}</td>
                    <td class="align-middle">
                        @if(data_get($item, 'shift.shift') == 1 || data_get($item, 'id_shift') == 1)
                            <span class="badge bg-primary">Shift 1</span>
                        @elseif(data_get($item, 'shift.shift') == 2 || data_get($item, 'id_shift') == 2)
                            <span class="badge bg-success">Shift 2</span>
                        @elseif(data_get($item, 'shift.shift') == 3 || data_get($item, 'id_shift') == 3)
                            <span class="badge bg-secondary">Shift 3</span>
                        @else
                            <span class="badge bg-info">{{ data_get($item, 'shift.shift', '-') }}</span>
                        @endif
                    </td>
                    <td class="text-center align-middle"><span class="badge badge-warning">Line {{ data_get($item, 'line', '-') }}</span></td>
                    <td class="text-center align-middle">
                        @if($userRole == 2 || $userRole == 3)
                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse(data_get($item, 'tanggal'))->format('d-m-Y') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse(data_get($item, 'tanggal'))->format('d-m-Y H:i:s') }}</span>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge badge-secondary">{{ data_get($item, 'jam') ? \Carbon\Carbon::parse(data_get($item, 'jam'))->format('H:i') : '-' }}</span>
                    </td>
                    <td class="text-center align-middle">{{ data_get($item, 'produk.nama_produk', '-') }} {{ data_get($item, 'berat_produk', '-') }} gram</td>
                    <td class="text-center align-middle">{{ data_get($item, 'kode_produksi', '-') }}</td>
                    <td class="text-center align-middle">{{ data_get($item, 'user.name', '-') }}</td>
                    <td class="text-center align-middle">
                        @if(data_get($item, 'verifikasi_detail') && data_get($item, 'verifikasi_hasil_proses'))
                            <span class="badge badge-success" title="Terverifikasi QC">
                                <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                            </span>
                            @if(data_get($item, 'nama_qc'))
                                <br><small class="text-muted">{{ data_get($item, 'nama_qc') }}</small>
                            @endif
                        @else
                            <span class="badge badge-warning" title="Menunggu Uji Spesimen & Verifikasi QC">
                                <i class="fas fa-hourglass-half mr-1"></i> Menunggu QC
                            </span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <div class="status-badges d-flex flex-column align-items-center">
                            <span class="badge {{ data_get($item, 'approved_by_qc') ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                <i class="fas {{ data_get($item, 'approved_by_qc') ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                            </span>
                            <span class="badge {{ data_get($item, 'approved_by_produksi') ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                <i class="fas {{ data_get($item, 'approved_by_produksi') ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                            </span>
                            <span class="badge {{ data_get($item, 'approved_by_spv') ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                                <i class="fas {{ data_get($item, 'approved_by_spv') ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                            </span>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <x-action-buttons :item="$item" route-prefix="input-metal-detector" :show-view="true" :show-print="false" :show-history="true" />
                        @if(!(isset($isAuditPage) && $isAuditPage))
                            <div class="mt-2 d-flex justify-content-center">
                                <x-audit-dropdown :item="$item" route-prefix="input-metal-detector" :uuid="$itemUuid" />
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ isset($isAuditPage) && $isAuditPage ? 11 : 12 }}" class="text-center align-middle py-4">
                        <em class="text-muted">Tidak ada data</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <h5>Belum ada data Metal Detector</h5>
    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
</div>
@endif