<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title text-uppercase mb-0"><i class="fas fa-hand-sparkles me-2 text-primary"></i>Cleaning Area</h6>
        <!-- <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.08); border-radius: 8px;">
            <i class="fas fa-magic text-primary" style="font-size: 0.9rem;"></i>
        </div> -->
    </div>
    <div class="card-body" style="padding: 1.5rem !important;">
        <div class="row">
            <!-- Left Column: Chart -->
            <div class="col-md-7 border-end" style="border-right-color: #f1f5f9 !important;">
                <div id="chart-mgmt-cleaning" style="min-height: 180px;"></div>
            </div>

            <!-- Right Column: Info Score -->
            <div class="col-md-5 ps-md-3 d-flex flex-column justify-content-center align-items-center mt-3 mt-md-0 text-center">
                <span class="text-uppercase fw-bold text-secondary d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Ruangan, mesin, dan peralatan</span>
                <span class="fw-extrabold text-success d-block" style="font-size: 2.5rem; font-family: 'JetBrains Mono', monospace; line-height: 1;">94.5%</span>
                <small class="text-muted d-block mt-3" style="font-size: 0.72rem; font-weight: 500;">
                    <i class="fas fa-check-circle text-success mr-1"></i>189 of 200 points
                </small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var cleaningMgmtOptions = {
        series: [
            { name: 'Closed', data: [14, 15, 15, 25, 12, 12] },
            { name: 'Open', data: [0, 0, 3, 0, 0, 3] }
        ],
        chart: {
            type: 'bar',
            height: 180,
            stacked: true,
            parentHeightOffset: 0,
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
            style: { fontSize: '8px', colors: ['#fff'] }
        },
        xaxis: {
            categories: ['Chillroom', 'Seasoning', 'Meatprep', 'Cooking', 'Packing Primer', 'Packing Sekunder'],
            labels: { style: { fontSize: '8px', colors: '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { fontSize: '8px', fontWeight: 'bold', colors: '#475569' } }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontSize: '8.5px',
            markers: { radius: 12 }
        },
        tooltip: { theme: 'dark' }
    };

    var chartContainer = document.querySelector("#chart-mgmt-cleaning");
    if(chartContainer) {
        chartContainer.innerHTML = '';
        var chart = new ApexCharts(chartContainer, cleaningMgmtOptions);
        chart.render();
    }
});
</script>
@endpush
