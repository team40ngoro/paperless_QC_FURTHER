@if($layout === 'management')
    <!-- ==================== ROOM TEMPERATURE CARD FOR MANAGEMENT (DASHBOARD 1) ==================== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-thermometer-half me-2 text-primary"></i> Room Temp. (°C)</h6>
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            <div class="row">
                <!-- Room Temp Bar Chart -->
                <div class="col-md-6 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Temperatur per Area</span>
                    <div id="chart-room-temp-bar" style="min-height: 200px;"></div>
                </div>

                <!-- Chillroom Trend Line Chart -->
                <div class="col-md-6">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.72rem; letter-spacing: 0.5px;">Trend suhu chillroom</span>
                    <div id="chart-chillroom-trend-line" style="min-height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>

@elseif($layout === 'inspector')
    <!-- ==================== ROOM TEMP & KESESUAIAN AREA CARD FOR INSPECTOR (DASHBOARD 2) ==================== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title text-uppercase mb-0"><i class="fas fa-temperature-low me-2 text-primary"></i> Room Temp. & Kesesuaian Area</h6>
            <!-- <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.08); border-radius: 8px;">
                <i class="fas fa-temperature-low text-primary" style="font-size: 0.9rem;"></i>
            </div> -->
        </div>
        <div class="card-body" style="padding: 1.5rem !important;">
            <div class="row">
                <!-- Column 1: Room Temp Bar Chart -->
                <div class="col-md-4 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.7rem; letter-spacing: 0.5px;">Suhu Ruang (°C)</span>
                    <div id="chart-insp-room-temp" style="min-height: 180px;"></div>
                </div>

                <!-- Column 2: Chillroom Trend Line Chart -->
                <div class="col-md-4 border-end" style="border-right-color: #f1f5f9 !important;">
                    <span class="fw-bold text-uppercase d-block mb-3 text-secondary text-center" style="font-size: 0.7rem; letter-spacing: 0.5px;">Trend suhu chillroom</span>
                    <div id="chart-insp-chillroom-trend" style="min-height: 180px;"></div>
                </div>

                <!-- Column 3: Cleaning Area Bar Chart + Score -->
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                        <span class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cleaning Area</span>
                        <span class="fw-bold text-success" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i>94.5%</span>
                    </div>
                    <div id="chart-insp-cleaning" style="min-height: 150px;"></div>
                    <div class="mt-2 text-center" style="background: #f0fdf4; border-radius: 10px; padding: 6px 12px; border: 1px dashed #bbf7d0;">
                        <small class="text-success fw-bold d-block" style="font-size: 0.68rem; color: #15803d !important;">
                            <i class="fas fa-info-circle mr-1"></i> 189 of 200 checked points (Ruangan, mesin, peralatan)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Room Temp Bar Chart Configuration
    var roomTempBarOptions = {
        series: [{
            name: 'Suhu (°C)',
            data: [2, 13, 16, 20, 21, 11, 13]
        }],
        chart: {
            type: 'bar',
            height: 190,
            toolbar: { show: false }
        },
        colors: ['#0284c7'],
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
                barHeight: '55%',
                dataLabels: { position: 'top' } // top places it outside the bar in horizontal charts
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: 8, // positive offset to place it outside beautifully
            style: { fontSize: '11px', fontWeight: '800', colors: ['#334155'] }, // dark slate gray text
            formatter: function(val) { return val + "°C"; }
        },
        xaxis: {
            categories: ['Chillroom', 'Seasoning', 'Meatprep', 'Aging', 'Cooking', 'Packing Primer', 'Packing Sekunder'],
            labels: {
                style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' },
                formatter: function(val) { return val + "°C"; }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } }
        },
        tooltip: { theme: 'dark', y: { formatter: function(val) { return val + " °C"; } } }
    };

    // 2. Chillroom Trend Line Chart Configuration
    var chillroomLineOptions = {
        series: [{
            name: 'Suhu Chillroom',
            data: [2, 1, 2, 4, 3, 5, 2, 4]
        }],
        chart: {
            type: 'line',
            height: 190,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#0ea5e9'],
        grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
        stroke: { width: 3, curve: 'smooth' },
        markers: { size: 4 },
        xaxis: {
            categories: ['12.00', '14.00', '16.00', '18.00', '20.00', '22.00', '00.00', '02.00'],
            labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: -2,
            max: 6,
            tickAmount: 4,
            labels: {
                style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' },
                formatter: function(val) { return val + "°C"; }
            }
        },
        tooltip: { theme: 'dark', y: { formatter: function(val) { return val + " °C"; } } }
    };

    @if($layout === 'management')
        // Render for Management Dashboard
        var chartRoomBar = new ApexCharts(document.querySelector("#chart-room-temp-bar"), roomTempBarOptions);
        chartRoomBar.render();

        var chartChillLine = new ApexCharts(document.querySelector("#chart-chillroom-trend-line"), chillroomLineOptions);
        chartChillLine.render();

    @elseif($layout === 'inspector')
        // Render for Inspector Dashboard (Lower Height to fit columns)
        roomTempBarOptions.chart.height = 170;
        var chartInspRoomBar = new ApexCharts(document.querySelector("#chart-insp-room-temp"), roomTempBarOptions);
        chartInspRoomBar.render();

        chillroomLineOptions.chart.height = 170;
        var chartInspChillLine = new ApexCharts(document.querySelector("#chart-insp-chillroom-trend"), chillroomLineOptions);
        chartInspChillLine.render();

        // 3. Cleaning Area Horizontal Grouped Bar Chart (Closed vs Open)
        var cleaningBarOptions = {
            series: [
                { name: 'Closed', data: [14, 15, 15, 25, 12, 12] },
                { name: 'Open', data: [0, 0, 3, 0, 0, 3] }
            ],
            chart: {
                type: 'bar',
                height: 150,
                stacked: true,
                toolbar: { show: false }
            },
            colors: ['#0ea5e9', '#0f172a'],
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
                    barHeight: '55%'
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', fontWeight: '800', colors: ['#fff'] },
                formatter: function(val) { return val > 0 ? val : ''; } // hide 0 values from rendering
            },
            xaxis: {
                categories: ['Chillroom', 'Seasoning', 'Meatprep', 'Cooking', 'Packing Primer', 'Packing Sekunder'],
                labels: { style: { fontSize: '10px', colors: '#64748b', fontWeight: 'bold' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { fontSize: '10px', fontWeight: 'bold', colors: '#475569' } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '10px',
                fontWeight: 'bold',
                markers: { radius: 12 }
            },
            tooltip: { theme: 'dark' }
        };

        var chartInspCleaning = new ApexCharts(document.querySelector("#chart-insp-cleaning"), cleaningBarOptions);
        chartInspCleaning.render();
    @endif
});
</script>
@endpush
