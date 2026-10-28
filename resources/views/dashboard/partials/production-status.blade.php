@php
    $isCurrent = ($status === 'current');
    $title = $isCurrent ? 'Current Processing' : 'Produksi Sebelumnya';
    $icon = $isCurrent ? 'fa-spinner fa-spin text-primary' : 'fa-history text-success';
    
    // Mapping variables based on current/prev status
    $mixing = $isCurrent ? $currentMixing : $prevMixing;
    $batter = $isCurrent ? $currentBatter : $prevBatter;
    $frayer = $isCurrent ? $currentFrayer : $prevFrayer;
    $roast  = $isCurrent ? $currentRoasting : $prevRoasting;
    $pack   = $isCurrent ? $currentPacking : $prevPacking;
    $carton = $isCurrent ? $currentCartoning : $prevCartoning;
@endphp

<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title text-uppercase mb-0">
            <i class="fas {{ $icon }} me-2"></i> {{ $title }}
        </h6>
    </div>

    <div class="card-body" style="padding: 1.5rem !important;">
        <div class="row g-3">
            <!-- Step 1: Mixing -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold text-primary d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Mixing</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($mixing)
                            <li class="fw-semibold text-dark">{{ $mixing->display_name }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $mixing->display_code }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $mixing->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Step 2: Battering -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #f97316; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold d-block mb-2" style="color: #f97316; font-size: 0.72rem; letter-spacing: 0.5px;">Battering</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($batter)
                            <li class="fw-semibold text-dark">{{ $batter->produk->nama_produk ?? 'N/A' }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $batter->kode_produksi_produk ?? '-' }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $batter->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Step 3: Frayer -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold d-block mb-2" style="color: #3b82f6; font-size: 0.72rem; letter-spacing: 0.5px;">Frayer</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($frayer)
                            <li class="fw-semibold text-dark">{{ $frayer->produk->nama_produk ?? 'N/A' }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $frayer->kode_produksi ?? ($frayer->penggorengan->kode_produksi ?? '-') }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $frayer->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Step 4: Roasting/Steaming -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #6366f1; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold d-block mb-2" style="color: #6366f1; font-size: 0.72rem; letter-spacing: 0.5px;">Roasting / Steaming</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($roast)
                            <li class="fw-semibold text-dark">{{ $roast->produk->nama_produk ?? 'N/A' }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $roast->kode_produksi ?? ($roast->inputRoasting->kode_produksi ?? '-') }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $roast->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Step 5: Packing -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #ec4899; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold d-block mb-2" style="color: #ec4899; font-size: 0.72rem; letter-spacing: 0.5px;">Packing</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($pack)
                            <li class="fw-semibold text-dark">{{ $pack->pengemasanProduk->produk->nama_produk ?? 'N/A' }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $pack->pengemasanProduk->kode_produksi ?? '-' }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $pack->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Step 6: Cartooning -->
            <div class="col-6 mb-3">
                <div class="p-3 h-100" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; transition: all 0.2s ease;">
                    <span class="text-uppercase fw-bold d-block mb-2" style="color: #f59e0b; font-size: 0.72rem; letter-spacing: 0.5px;">Cartooning</span>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.72rem; color: #475569;">
                        @if($carton)
                            <li class="fw-semibold text-dark">{{ $carton->pengemasanProduk->produk->nama_produk ?? 'N/A' }}</li>
                            <li style="font-family: 'JetBrains Mono', monospace;">{{ $carton->pengemasanProduk->kode_produksi ?? '-' }}</li>
                            <li class="text-muted mt-1"><i class="far fa-clock mr-1"></i>{{ $carton->jam ?? '-' }}</li>
                        @else
                            <li class="text-muted italic">No Data</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
