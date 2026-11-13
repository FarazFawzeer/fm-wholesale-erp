import ApexCharts from 'apexcharts';

const box = document.getElementById('sales-chart');
if (box) {
    const data = JSON.parse(box.dataset.chart || '[]');
    const compact = (v) => (v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : v >= 1e3 ? Math.round(v / 1e3) + 'K' : v);
    new ApexCharts(box, {
        chart: { type: 'bar', height: Math.max(300, box.clientHeight), toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Sales', data: data.map((d) => d.sales) },
            { name: 'Gross profit', data: data.map((d) => d.profit) },
        ],
        xaxis: { categories: data.map((d) => d.d), labels: { rotate: -45, hideOverlappingLabels: true, style: { fontSize: '10px' } }, tickAmount: 10 },
        yaxis: { labels: { formatter: compact } },
        colors: ['#1a80f8', '#22c55e'],
        plotOptions: { bar: { columnWidth: '60%', borderRadius: 3 } },
        dataLabels: { enabled: false },
        legend: { position: 'top', horizontalAlign: 'right' },
        grid: { borderColor: 'rgba(148,163,184,0.2)' },
        tooltip: { y: { formatter: (v) => 'Rs ' + window.erpFmt(v) } },
    }).render();
}
