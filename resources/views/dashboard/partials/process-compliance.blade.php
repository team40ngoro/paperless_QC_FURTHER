@if($type === 'meatprep_cooking')
    <!-- ==================== MEATPREP & COOKING LAYOUT (DASHBOARD 1 MIDDLE) ==================== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-fire-alt me-2 text-primary"></i>Meatprep & Cooking Compliance</h6>
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            <div class="row">
                <!-- Meatprep Chart -->
                <div class="col-md-6 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Meatprep (Process: % Kesesuaian)</span>
                    <div id="chart-meatprep-compliance" style="min-height: 180px;"></div>
                </div>

                <!-- Cooking Chart -->
                <div class="col-md-6">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cooking (Cooking: % Kesesuaian)</span>
                    <div id="chart-cooking-compliance" style="min-height: 180px;"></div>
                </div>
            </div>

            <hr style="border-color: #f1f5f9; margin: 1.5rem 0;">

            <!-- Trend Line Charts -->
            <div class="row">
                <div class="col-md-6 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Trend Suhu Cold Mixing</span>
                    <div id="chart-cold-mixing-trend" style="min-height: 170px;"></div>
                </div>
                <div class="col-md-6">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Trend Suhu Frying</span>
                    <div id="chart-frying-trend" style="min-height: 170px;"></div>
                </div>
            </div>

            <hr style="border-color: #f1f5f9; margin: 1.5rem 0;">

            <!-- Footer Stats: Incoming RM & Core Temp -->
            <div class="row text-center">
                <div class="col-6 border-end d-flex align-items-center justify-content-center" style="border-right-color: #f1f5f9 !important;">
                    <div class="p-3 w-100" style="background: #f0fdf4; border-radius: 12px; border: 1px solid #dcfce7;">
                        <span class="fw-bold text-uppercase d-block text-success mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #059669 !important;">Incoming RM</span>
                        <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">Incoming 120x</span>
                        <small class="text-success fw-bold d-block" style="font-size: 0.72rem;"><i class="fas fa-check-circle mr-1"></i>120 OK; 0 Tidak OK</small>
                    </div>
                </div>
                <div class="col-6 d-flex align-items-center justify-content-center">
                    <div class="p-3 w-100" style="background: #fff5f5; border-radius: 12px; border: 1px solid #fee2e2;">
                        <span class="fw-bold text-uppercase d-block text-danger mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #b91c1c !important;">Core Temp.</span>
                        <span class="fw-extrabold text-danger d-block mb-1" style="font-size: 1.3rem; font-family: 'JetBrains Mono', monospace; line-height: 1;">142 °C</span>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">Current Temp. Cooking</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'packing')
    <!-- ==================== PACKING & CARTONING LAYOUT (DASHBOARD 1 RIGHT) ==================== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-box-open me-2 text-indigo"></i>Packing & Cartoning</h6>
            <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(99, 102, 241, 0.08); border-radius: 8px;">
                <i class="fas fa-sliders-h text-indigo" style="font-size: 0.9rem;"></i>
            </div>
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            <!-- Packing Compliance Chart -->
            <div class="mb-3">
                <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">% Kesesuaian Produk</span>
                <div id="chart-packing-compliance" style="min-height: 130px;"></div>
            </div>

            <hr style="border-color: #f1f5f9; margin: 1.5rem 0;">

            <!-- Trend Weight Line Charts -->
            <div class="row">
                <div class="col-md-6 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Trend Berat / pack</span>
                    <div id="chart-weight-pack-trend" style="min-height: 170px;"></div>
                </div>
                <div class="col-md-6">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Trend Berat / Box</span>
                    <div id="chart-weight-box-trend" style="min-height: 170px;"></div>
                </div>
            </div>

            <hr style="border-color: #f1f5f9; margin: 1.5rem 0;">

            <!-- Footer Stats: IQF -->
            <div class="p-3 text-center" style="background: #f0f9ff; border-radius: 14px; border: 1px solid #e0f2fe;">
                <span class="fw-bold text-uppercase d-block text-primary mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #0369a1 !important;">IQF Temperature</span>
                <span class="fw-extrabold text-primary d-block mb-1" style="font-size: 1.3rem; font-family: 'JetBrains Mono', monospace; line-height: 1;">-18.4 °C</span>
                <small class="text-muted d-block" style="font-size: 0.68rem;">Current Core Temp.</small>
            </div>
        </div>
    </div>

@elseif($type === 'management_combined')
    <!-- ===== UNIFIED 3-COLUMN COMPLIANCE CARD (DASHBOARD 1 - Management, SPV, Admin) ===== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-tasks me-2 text-primary"></i> Process Line Compliance</h6>
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            {{-- Use align-items-stretch so all columns stretch to the same height --}}
            <div class="row align-items-stretch">

                <!-- Column 1: Meatprep (d-flex flex-column so footer sticks to bottom) -->
                <div class="col-md-4 border-end d-flex flex-column" style="border-right-color: #f1f5f9 !important;">
                    <div class="text-center mb-2">
                        <span class="fw-bold text-dark d-block" style="font-size: 0.95rem; letter-spacing: 0.3px;">Meatprep</span>
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 500;">Process : % Kesesuaian</span>
                    </div>
                    <div id="chart-mgmt-meatprep" style="min-height: 190px;"></div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <span class="fw-bold text-uppercase d-block mb-2 text-secondary text-center" style="font-size: 0.68rem; letter-spacing: 0.5px;">Trend Suhu Cold Mixing</span>
                    <div id="chart-mgmt-cold-mixing" style="min-height: 170px;"></div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <!-- Footer: Incoming RM — mt-auto pushes it to the very bottom -->
                    <div class="p-3 mt-auto" style="background: #f0fdf4; border-radius: 12px; border: 1px solid #dcfce7;">
                        <span class="fw-bold text-uppercase d-block text-success mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #059669 !important;">Incoming RM</span>
                        <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">Incoming 120x;</span>
                        <small class="fw-bold d-block" style="font-size: 0.72rem;"><span class="text-primary">120 OK</span>; <span class="text-danger">0 Tidak OK</span></small>
                    </div>
                </div>

                <!-- Column 2: Cooking (d-flex flex-column so footer sticks to bottom) -->
                <div class="col-md-4 border-end d-flex flex-column" style="border-right-color: #f1f5f9 !important;">
                    <div class="text-center mb-2">
                        <span class="fw-bold text-dark d-block" style="font-size: 0.95rem; letter-spacing: 0.3px;">Cooking</span>
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 500;">Cooking : % Kesesuaian</span>
                    </div>
                    <div id="chart-mgmt-cooking" style="min-height: 190px;"></div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <span class="fw-bold text-uppercase d-block mb-2 text-secondary text-center" style="font-size: 0.68rem; letter-spacing: 0.5px;">Trend Suhu Frying</span>
                    <div id="chart-mgmt-frying" style="min-height: 170px;"></div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <!-- Footer: Core Temp — mt-auto pushes it to the very bottom -->
                    <div class="p-3 mt-auto" style="background: #fff5f5; border-radius: 12px; border: 1px solid #fee2e2;">
                        <span class="fw-bold text-uppercase d-block text-danger mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #b91c1c !important;">Core Temp.</span>
                        <span class="fw-extrabold text-danger d-block mb-1" style="font-size: 1.3rem; font-family: 'JetBrains Mono', monospace; line-height: 1;">142°C</span>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">Current Temp. Cooking</small>
                    </div>
                </div>

                <!-- Column 3: Packing & Cartoning (d-flex flex-column so footer sticks to bottom) -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-bold text-dark d-block" style="font-size: 0.95rem; letter-spacing: 0.3px;">Packing &amp; Cartoning</span>
                            <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 500;">% Kesesuaian Produk</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(99,102,241,0.08); border-radius: 8px; flex-shrink:0;">
                            <i class="fas fa-sliders-h" style="font-size: 0.9rem; color: #6366f1;"></i>
                        </div>
                    </div>
                    <div id="chart-mgmt-packing" style="min-height: 130px;"></div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <div class="row">
                        <div class="col-6 border-end" style="border-right-color: #f1f5f9 !important;">
                            <span class="fw-bold text-uppercase d-block mb-2 text-secondary text-center" style="font-size: 0.68rem; letter-spacing: 0.5px;">Trend Berat / pack</span>
                            <div id="chart-mgmt-weight-pack" style="min-height: 170px;"></div>
                        </div>
                        <div class="col-6">
                            <span class="fw-bold text-uppercase d-block mb-2 text-secondary text-center" style="font-size: 0.68rem; letter-spacing: 0.5px;">Trend Berat / Box</span>
                            <div id="chart-mgmt-weight-box" style="min-height: 170px;"></div>
                        </div>
                    </div>

                    <hr style="border-color: #f1f5f9; margin: 1rem 0;">

                    <!-- Footer: IQF — mt-auto pushes it to the very bottom -->
                    <div class="p-3 text-center mt-auto" style="background: #f0f9ff; border-radius: 14px; border: 1px solid #e0f2fe;">
                        <span class="fw-bold text-uppercase d-block text-primary mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #0369a1 !important;">IQF</span>
                        <span class="fw-extrabold text-primary d-block mb-1" style="font-size: 1.3rem; font-family: 'JetBrains Mono', monospace; line-height: 1;">-18.4°C</span>
                        <small class="text-muted d-block" style="font-size: 0.68rem;">Current Core Temp.</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

@elseif($type === 'all')
    <!-- ==================== COMBINED HORIZONTAL LAYOUT WITH CONTAM RM (DASHBOARD 2 - Gambar 2) ==================== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-tasks me-2 text-primary"></i> Process Line Compliance</h6>
            <!-- <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.08); border-radius: 8px;">
                <i class="fas fa-sliders-h text-primary" style="font-size: 0.9rem;"></i>
            </div> -->
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            <div class="row">
                <!-- Column 1: Contam. Raw Material -->
                <div class="col-md-3 border-end" style="border-right-color: #f1f5f9 !important;">
                    <div class="text-center mb-3">
                        <span class="fw-bold text-dark d-block text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Contam. Raw Material</span>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; font-weight: 500;"></span>
                    </div>
                    <div id="chart-all-contam-rm" style="min-height: 180px;"></div>
                </div>

                <!-- Column 2: Meatprep -->
                <div class="col-md-3 border-end" style="border-right-color: #f1f5f9 !important;">
                    <div class="text-center mb-3">
                        <span class="fw-bold text-dark d-block text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Meatprep</span>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; font-weight: 500;">Process : % Kesesuaian</span>
                    </div>
                    <div id="chart-all-meatprep" style="min-height: 180px;"></div>
                </div>

                <!-- Column 3: Cooking -->
                <div class="col-md-3 border-end" style="border-right-color: #f1f5f9 !important;">
                    <div class="text-center mb-3">
                        <span class="fw-bold text-dark d-block text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Cooking</span>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; font-weight: 500;">Cooking : % Kesesuaian</span>
                    </div>
                    <div id="chart-all-cooking" style="min-height: 180px;"></div>
                </div>

                <!-- Column 4: Packing & Cartoning -->
                <div class="col-md-3">
                    <div class="text-center mb-3">
                        <span class="fw-bold text-dark d-block text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Packing & Cartoning</span>
                        <span class="text-muted d-block mt-1" style="font-size: 0.72rem; font-weight: 500;">% Kesesuaian Produk</span>
                    </div>
                    <div id="chart-all-packing" style="min-height: 180px;"></div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var isDark = false; // standard theme

    // General options for Horizontal Bar Charts
    var barOptionsBase = {
        chart: { type: 'bar', toolbar: { show: false } },
        colors: ['#0ea5e9'],
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } }
        },
        plotOptions: {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '50%',
                dataLabels: { position: 'end' }
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: -6,
            style: { fontSize: '9px', fontWeight: 'bold', colors: ['#000000'] },
            formatter: function(val) { return val + "%"; }
        },
        xaxis: {
            min: 0,
            max: 100,
            tickAmount: 5,
            labels: {
                style: { fontSize: '8px', colors: '#64748b' },
                formatter: function(val) { return val + "%"; }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        tooltip: { theme: 'dark', y: { formatter: function(val) { return val + "% Kesesuaian"; } } }
    };

    // General options for Line Charts
    var lineOptionsBase = {
        chart: { type: 'line', toolbar: { show: false }, zoom: { enabled: false } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
        stroke: { width: 3, curve: 'smooth' },
        markers: { size: 4 },
        xaxis: {
            categories: ['B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7'],
            labels: { style: { fontSize: '8px', colors: '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { fontSize: '8px', colors: '#64748b' } } },
        tooltip: { theme: 'dark' }
    };

    @if($type === 'management_combined')
        // ---- MANAGEMENT COMBINED: Meatprep Chart ----
        var optMgmtMeat = JSON.parse(JSON.stringify(barOptionsBase));
        optMgmtMeat.series = [{ name: 'Kesesuaian', data: [{{ $persentaseMixing ?? 0 }}, {{ $persentaseEmulsi ?? 0 }}, {{ $persentaseBatter ?? 0 }}, {{ $persentaseThumbling ?? 0 }}, {{ $persentasePemasakNasi ?? 0 }}, {{ $persentaseCookingMixer ?? 0 }}] }];
        optMgmtMeat.colors = ['#38bdf8'];
        optMgmtMeat.chart.height = 180;
        optMgmtMeat.xaxis.categories = ['Mixing', 'Emulsi', 'Batter', 'Thumbling', 'Pemasak Nasi', 'Cooking Mixer'];
        optMgmtMeat.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optMgmtMeat.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optMgmtMeat.plotOptions = { bar: { borderRadius: 3, horizontal: true, barHeight: '60%', dataLabels: { position: 'end' } } };
        optMgmtMeat.dataLabels = { enabled: true, offsetX: -8, style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] }, formatter: function(val) { return val; } };
        new ApexCharts(document.querySelector("#chart-mgmt-meatprep"), optMgmtMeat).render();

        // ---- MANAGEMENT COMBINED: Cooking Chart ----
        var optMgmtCook = JSON.parse(JSON.stringify(barOptionsBase));
        optMgmtCook.series = [{ name: 'Kesesuaian', data: [90, 90] }];
        optMgmtCook.colors = ['#38bdf8'];
        optMgmtCook.chart.height = 180;
        optMgmtCook.xaxis.categories = ['Frying', 'Roasting'];
        optMgmtCook.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optMgmtCook.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optMgmtCook.plotOptions = { bar: { borderRadius: 3, horizontal: true, barHeight: '60%', dataLabels: { position: 'end' } } };
        optMgmtCook.dataLabels = { enabled: true, offsetX: -8, style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] }, formatter: function(val) { return val; } };
        new ApexCharts(document.querySelector("#chart-mgmt-cooking"), optMgmtCook).render();

        // ---- MANAGEMENT COMBINED: Packing Chart ----
        var optMgmtPack = JSON.parse(JSON.stringify(barOptionsBase));
        optMgmtPack.series = [{ name: 'Kesesuaian', data: [90, 90, 100] }];
        optMgmtPack.colors = ['#38bdf8'];
        optMgmtPack.chart.height = 130;
        optMgmtPack.xaxis.categories = ['Per Pack', 'Per Box', 'MD'];
        optMgmtPack.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optMgmtPack.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optMgmtPack.plotOptions = { bar: { borderRadius: 3, horizontal: true, barHeight: '60%', dataLabels: { position: 'end' } } };
        optMgmtPack.dataLabels = { enabled: true, offsetX: -8, style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] }, formatter: function(val) { return val; } };
        new ApexCharts(document.querySelector("#chart-mgmt-packing"), optMgmtPack).render();

        // ---- MANAGEMENT COMBINED: Cold Mixing Trend ----
        var optMgmtCM = JSON.parse(JSON.stringify(lineOptionsBase));
        optMgmtCM.series = [
            { name: 'Cold Mixing', data: [11, 8, 10, 11, 10, 11, 10] },
            { name: 'Emulsi', data: [16, 13, 14, 15, 14, 15, 14] }
        ];
        optMgmtCM.colors = ['#3b82f6', '#10b981'];
        optMgmtCM.chart.height = 170;
        optMgmtCM.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optMgmtCM.yaxis = { min: -5, max: 20, tickAmount: 5, labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } } };
        new ApexCharts(document.querySelector("#chart-mgmt-cold-mixing"), optMgmtCM).render();

        // ---- MANAGEMENT COMBINED: Frying Trend ----
        var optMgmtFry = JSON.parse(JSON.stringify(lineOptionsBase));
        optMgmtFry.series = [
            { name: 'Suhu Mesin', data: [15, 11, 12, 13, 14, 13, 14] },
            { name: 'Suhu Produk', data: [0, -2, -1, -2, -1, -2, -1] }
        ];
        optMgmtFry.colors = ['#0284c7', '#0f172a'];
        optMgmtFry.chart.height = 170;
        optMgmtFry.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optMgmtFry.yaxis = { min: -5, max: 20, tickAmount: 5, labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } } };
        new ApexCharts(document.querySelector("#chart-mgmt-frying"), optMgmtFry).render();

        // ---- MANAGEMENT COMBINED: Trend Berat Per Pack ----
        var optMgmtWP = JSON.parse(JSON.stringify(lineOptionsBase));
        optMgmtWP.series = [{ name: 'Berat Per Pack', data: [1000, 1002, 1010, 1005, 1008, 1012, 1005] }];
        optMgmtWP.colors = ['#3b82f6'];
        optMgmtWP.chart.height = 170;
        optMgmtWP.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optMgmtWP.yaxis = { min: 0, max: 1200, tickAmount: 4, labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } } };
        new ApexCharts(document.querySelector("#chart-mgmt-weight-pack"), optMgmtWP).render();

        // ---- MANAGEMENT COMBINED: Trend Berat Per Box ----
        var optMgmtWB = JSON.parse(JSON.stringify(lineOptionsBase));
        optMgmtWB.series = [{ name: 'Berat Per Box', data: [5000, 5010, 5020, 5005, 5015, 5030, 5010] }];
        optMgmtWB.colors = ['#0ea5e9'];
        optMgmtWB.chart.height = 170;
        optMgmtWB.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optMgmtWB.yaxis = { min: 0, max: 6000, tickAmount: 4, labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } } };
        new ApexCharts(document.querySelector("#chart-mgmt-weight-box"), optMgmtWB).render();

    @elseif($type === 'meatprep_cooking')
        // 1. Meatprep Chart
        var optMeatprep = JSON.parse(JSON.stringify(barOptionsBase));
        optMeatprep.series = [{ name: 'Kesesuaian', data: [{{ $persentaseMixing ?? 0 }}, {{ $persentaseEmulsi ?? 0 }}, {{ $persentaseBatter ?? 0 }}, {{ $persentaseThumbling ?? 0 }}, {{ $persentasePemasakNasi ?? 0 }}, {{ $persentaseCookingMixer ?? 0 }}] }];
        optMeatprep.colors = ['#38bdf8']; // Soft Sky Blue
        optMeatprep.chart.height = 170;
        optMeatprep.xaxis.categories = ['Mixing', 'Emulsi', 'Batter', 'Thumbling', 'Pemasak Nasi', 'Cooking Mixer'];
        optMeatprep.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optMeatprep.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optMeatprep.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' }
            }
        };
        optMeatprep.dataLabels = {
            enabled: true,
            offsetX: -8,
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] },
            formatter: function(val) { return val; } // only the number
        };
        var chartMeat = new ApexCharts(document.querySelector("#chart-meatprep-compliance"), optMeatprep);
        chartMeat.render();

        // 2. Cooking Chart
        var optCooking = JSON.parse(JSON.stringify(barOptionsBase));
        optCooking.series = [{ name: 'Kesesuaian', data: [90, 90] }];
        // ... (rest remains unchanged)
        optCooking.colors = ['#38bdf8']; // Soft Sky Blue
        optCooking.chart.height = 170;
        optCooking.xaxis.categories = ['Frying', 'Roasting'];
        optCooking.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optCooking.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optCooking.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' }
            }
        };
        optCooking.dataLabels = {
            enabled: true,
            offsetX: -8,
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] },
            formatter: function(val) { return val; }
        };
        var chartCook = new ApexCharts(document.querySelector("#chart-cooking-compliance"), optCooking);
        chartCook.render();

        // 3. Cold Mixing Line Chart
        var optColdMix = JSON.parse(JSON.stringify(lineOptionsBase));
        optColdMix.series = [
            { name: 'Cold Mixing', data: [11, 8, 10, 11, 10, 11, 10] },
            { name: 'Emulsi', data: [16, 13, 14, 15, 14, 15, 14] }
        ];
        optColdMix.chart.height = 170;
        optColdMix.colors = ['#3b82f6', '#10b981'];
        optColdMix.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optColdMix.yaxis = {
            min: -5,
            max: 20,
            tickAmount: 5,
            labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } }
        };
        var chartCM = new ApexCharts(document.querySelector("#chart-cold-mixing-trend"), optColdMix);
        chartCM.render();

        // 4. Frying Line Chart
        var optFryingTrend = JSON.parse(JSON.stringify(lineOptionsBase));
        optFryingTrend.series = [
            { name: 'Suhu Mesin', data: [15, 11, 12, 13, 14, 13, 14] },
            { name: 'Suhu Produk', data: [0, -2, -1, -2, -1, -2, -1] }
        ];
        optFryingTrend.chart.height = 170;
        optFryingTrend.colors = ['#0284c7', '#0f172a'];
        optFryingTrend.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optFryingTrend.yaxis = {
            min: -5,
            max: 20,
            tickAmount: 5,
            labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } }
        };
        var chartFT = new ApexCharts(document.querySelector("#chart-frying-trend"), optFryingTrend);
        chartFT.render();

    @elseif($type === 'packing')
        // 5. Packing & Cartoning Chart
        var optPacking = JSON.parse(JSON.stringify(barOptionsBase));
        optPacking.series = [{ name: 'Kesesuaian', data: [90, 90, 100] }];
        optPacking.colors = ['#38bdf8']; // Soft Sky Blue
        optPacking.chart.height = 140;
        optPacking.xaxis.categories = ['Per Pack', 'Per Box', 'MD'];
        optPacking.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optPacking.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optPacking.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' }
            }
        };
        optPacking.dataLabels = {
            enabled: true,
            offsetX: -8,
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] },
            formatter: function(val) { return val; } // only the number
        };
        var chartPack = new ApexCharts(document.querySelector("#chart-packing-compliance"), optPacking);
        chartPack.render();

        // 6. Trend Berat Per Pack Line Chart
        var optWeightPack = JSON.parse(JSON.stringify(lineOptionsBase));
        optWeightPack.series = [{ name: 'Berat Per Pack', data: [1000, 1002, 1010, 1005, 1008, 1012, 1005] }];
        optWeightPack.chart.height = 170;
        optWeightPack.colors = ['#3b82f6'];
        optWeightPack.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optWeightPack.yaxis = {
            min: 0,
            max: 1200,
            tickAmount: 4,
            labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } }
        };
        var chartWP = new ApexCharts(document.querySelector("#chart-weight-pack-trend"), optWeightPack);
        chartWP.render();

        // 7. Trend Berat Per Box Line Chart
        var optWeightBox = JSON.parse(JSON.stringify(lineOptionsBase));
        optWeightBox.series = [{ name: 'Berat Per Box', data: [5000, 5010, 5020, 5005, 5015, 5030, 5010] }];
        optWeightBox.chart.height = 170;
        optWeightBox.colors = ['#0ea5e9'];
        optWeightBox.xaxis.labels = { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } };
        optWeightBox.yaxis = {
            min: 0,
            max: 6000,
            tickAmount: 4,
            labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } }
        };
        var chartWB = new ApexCharts(document.querySelector("#chart-weight-box-trend"), optWeightBox);
        chartWB.render();

    @elseif($type === 'all')
        // 8. Contam. Raw Material Chart (Column 1)
        var optAllContam = JSON.parse(JSON.stringify(barOptionsBase));
        optAllContam.series = [{ name: 'Temuan (pcs)', data: [4.0, 1.0, 1.0, 1.0, 0, 0, 2.0, 1.0] }];
        // ... (rest remains unchanged)
        optAllContam.colors = ['#334155']; // Slate gray matching the mockup
        optAllContam.chart.height = 180;
        optAllContam.xaxis.categories = ['Tendon', 'Sayap', 'Leher Ayam', 'Trakhea', 'Pakan Ayam', 'Proventikulus', 'Sarung Tangan', 'Benang'];
        optAllContam.xaxis.min = 0;
        optAllContam.xaxis.max = 30;
        optAllContam.xaxis.tickAmount = 6;
        optAllContam.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optAllContam.xaxis.labels.formatter = function(val) { return val; };
        optAllContam.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optAllContam.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'top' } // top places it outside the bar in horizontal charts
            }
        };
        optAllContam.dataLabels = {
            enabled: true,
            offsetX: 8, // positive offset to place it beautifully outside
            style: { fontSize: '11px', fontWeight: '800', colors: ['#334155'] }, // dark slate gray text
            formatter: function(val) { return val > 0 ? val.toFixed(1) : ''; }
        };
        optAllContam.tooltip = { theme: 'dark', y: { formatter: function(val) { return val + " pcs"; } } };
        var chartAllContam = new ApexCharts(document.querySelector("#chart-all-contam-rm"), optAllContam);
        chartAllContam.render();

        // 9. Combined Meatprep Chart (Column 2)
        var optAllMeat = JSON.parse(JSON.stringify(barOptionsBase));
        optAllMeat.series = [{ name: 'Kesesuaian', data: [{{ $persentaseMixing ?? 0 }}, {{ $persentaseEmulsi ?? 0 }}, {{ $persentaseBatter ?? 0 }}, {{ $persentaseThumbling ?? 0 }}, {{ $persentasePemasakNasi ?? 0 }}, {{ $persentaseCookingMixer ?? 0 }}] }];
        optAllMeat.colors = ['#38bdf8']; // Soft Sky Blue
        optAllMeat.chart.height = 180;
        optAllMeat.xaxis.categories = ['Mixing', 'Emulsi', 'Batter', 'Thumbling', 'Pemasak Nasi', 'Cooking Mixer'];
        optAllMeat.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optAllMeat.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optAllMeat.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' } // inside at the right edge
            }
        };
        optAllMeat.dataLabels = {
            enabled: true,
            offsetX: -8, // negative offset to keep it inside
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] }, // readable bold black
            formatter: function(val) { return val; } // only the number, no %
        };
        var chartAllMeat = new ApexCharts(document.querySelector("#chart-all-meatprep"), optAllMeat);
        chartAllMeat.render();

        // 10. Combined Cooking Chart (Column 3)
        var optAllCook = JSON.parse(JSON.stringify(barOptionsBase));
        optAllCook.series = [{ name: 'Kesesuaian', data: [90, 90] }];
        optAllCook.colors = ['#38bdf8']; // Soft Sky Blue
        optAllCook.chart.height = 180;
        optAllCook.xaxis.categories = ['Frying', 'Roasting'];
        optAllCook.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optAllCook.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optAllCook.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' }
            }
        };
        optAllCook.dataLabels = {
            enabled: true,
            offsetX: -8,
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] },
            formatter: function(val) { return val; }
        };
        var chartAllCook = new ApexCharts(document.querySelector("#chart-all-cooking"), optAllCook);
        chartAllCook.render();

        // 11. Combined Packing Chart (Column 4)
        var optAllPack = JSON.parse(JSON.stringify(barOptionsBase));
        optAllPack.series = [{ name: 'Kesesuaian', data: [90, 90, 100] }];
        optAllPack.colors = ['#38bdf8']; // Soft Sky Blue
        optAllPack.chart.height = 180;
        optAllPack.xaxis.categories = ['Per Pack', 'Per Box', 'MD'];
        optAllPack.xaxis.labels = { style: { fontSize: '10px', fontWeight: 'bold', colors: '#64748b' } };
        optAllPack.yaxis = { labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } } };
        optAllPack.plotOptions = {
            bar: {
                borderRadius: 3,
                horizontal: true,
                barHeight: '60%',
                dataLabels: { position: 'end' }
            }
        };
        optAllPack.dataLabels = {
            enabled: true,
            offsetX: -8,
            style: { fontSize: '11px', fontWeight: '800', colors: ['#000000'] },
            formatter: function(val) { return val; }
        };
        var chartAllPack = new ApexCharts(document.querySelector("#chart-all-packing"), optAllPack);
        chartAllPack.render();
    @endif
});
</script>
@endpush
