<div class="card w-100 h-100 mb-4 d-flex flex-column">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title text-uppercase mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Overview</h5>
        <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.08); border-radius: 8px;">
            <i class="fas fa-clipboard-list text-primary" style="font-size: 1rem;"></i>
        </div>
    </div>
    <div class="card-body d-flex flex-column flex-grow-1" style="padding: 1.5rem !important;">
        <div class="row">
            
            <!-- Non-Conformity (Soft Red Theme) -->
            <div class="col-12 mb-3">
                <div class="p-3" style="background: #fff5f5; border-radius: 14px; border: 1px solid #fee2e2; border-left: 4px solid #ef4444;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-danger" style="font-size: 0.72rem; letter-spacing: 0.5px;">Non-Conformity</span>
                        <span class="badge text-danger fw-bold d-flex align-items-center gap-1" style="font-size: 0.65rem; background: rgba(239, 68, 68, 0.08); padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-circle text-danger mr-1" style="font-size: 6px; animation: pulse 1.5s infinite;"></i>Live
                        </span>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 border-end" style="border-right-color: #fee2e2 !important;">
                            <span class="text-muted d-block mb-1" style="font-size: 0.68rem; font-weight: 500;">Process</span>
                            <span class="fw-extrabold text-dark" style="font-size: 1.25rem; font-family: 'JetBrains Mono', monospace;">3.24%</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1" style="font-size: 0.68rem; font-weight: 500;">Non-Process</span>
                            <span class="fw-extrabold text-dark" style="font-size: 1.25rem; font-family: 'JetBrains Mono', monospace;">1.23%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contamination (Soft Amber Theme) -->
            <div class="col-12 mb-3">
                <div class="p-3" style="background: #fffbeb; border-radius: 14px; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b;">
                    <span class="text-uppercase fw-bold text-warning d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #d97706 !important;">Contamination</span>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                <i class="fas fa-radiation-alt mr-2 text-warning" style="font-size: 0.85rem;"></i> Logam - FG
                            </span>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem; font-family: 'JetBrains Mono', monospace;">0.084 gr</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                <i class="fas fa-box-open mr-2 text-warning" style="font-size: 0.85rem;"></i> Non FG
                            </span>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem; font-family: 'JetBrains Mono', monospace;">3 pcs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                <i class="fas fa-boxes mr-2 text-warning" style="font-size: 0.85rem;"></i> Raw Material
                            </span>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem; font-family: 'JetBrains Mono', monospace;">247 pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GMP Area (Soft Emerald Theme) -->
            <div class="col-12">
                <div class="p-3" style="background: #f0fdf4; border-radius: 14px; border: 1px solid #dcfce7; border-left: 4px solid #10b981;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-success" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #059669 !important;">GMP Area</span>
                        <span class="fw-extrabold text-success" style="font-size: 1.3rem; font-family: 'JetBrains Mono', monospace;">99.75%</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mt-2 pt-2" style="font-size: 0.68rem; border-top: 1px dashed #dcfce7;">
                        <span>800x Verifikasi</span>
                        <span class="fw-bold text-dark">799 OK; 1 Tidak OK</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.4; }
    50% { opacity: 1; }
    100% { opacity: 0.4; }
}
</style>
