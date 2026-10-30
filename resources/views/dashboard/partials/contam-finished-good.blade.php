<div class="card w-100 h-100 mb-4 d-flex flex-column">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title text-uppercase mb-0"><i class="fas fa-shield-virus me-2 text-danger"></i> Contam. Finished Good</h6>
    </div>
    <div class="card-body d-flex flex-column flex-grow-1" style="padding: 1.5rem !important;">
        <div id="chart-contam-finished-good" class="flex-grow-1" style="min-height: 250px;"></div>
        <div class="mt-auto text-center" style="background: #fff5f5; border-radius: 10px; padding: 8px 12px; border: 1px dashed #fec2c2;">
            <small class="text-danger fw-bold d-flex align-items-center justify-content-center gap-1" style="font-size: 0.72rem; color: #b91c1c !important;">
                <i class="fas fa-exclamation-triangle mr-1"></i> Notes: 1160 batch; 1148 OK; 12x Tidak OK
            </small>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var options = {
        series: [{
            name: 'Jumlah (pcs)',
            data: [1.0, 1.0, 0, 4.0, 1.0, 0]
        }],
        chart: {
            type: 'bar',
            height: 230,
            parentHeightOffset: 0,
            toolbar: { show: false }
        },
        colors: ['#ef4444'],
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 3,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                barHeight: '55%',
                distributed: false,
                dataLabels: { position: 'end' }
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: 16,
            style: {
                fontSize: '10px',
                fontWeight: 'bold',
                colors: ['#475569']
            },
            formatter: function(val) {
                return val > 0 ? val.toFixed(1) : '';
            }
        },
        xaxis: {
            categories: ['Autogrind', 'Bowl Cutter', 'Revo', 'Wiremess IQF', 'Metalcar', 'Rambut'],
            labels: {
                style: { fontSize: '9px', colors: '#64748b', fontWeight: 500 }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '9px', colors: '#475569', fontWeight: 600 }
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val + " pcs"
                }
            }
        }
    };

    var chartContainer = document.querySelector("#chart-contam-finished-good");
    if(chartContainer) {
        chartContainer.innerHTML = '';
        var chart = new ApexCharts(chartContainer, options);
        chart.render();
    }
});
</script>
@endpush
