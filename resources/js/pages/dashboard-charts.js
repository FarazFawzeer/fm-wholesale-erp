import ApexCharts from 'apexcharts';

const dark = () => document.documentElement.getAttribute('data-bs-theme') === 'dark';
const ink = () => (dark() ? '#f0f3f6' : '#111827');
const compact = (v) => (v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : v >= 1e3 ? Math.round(v / 1e3) + 'K' : v);
const rs = (v) => 'Rs ' + window.erpFmt(v);
const base = { fontFamily: 'inherit', toolbar: { show: false }, parentHeightOffset: 0, foreColor: '#98a2b3' };

const area = document.getElementById('sales-area');
if (area) {
    const data = JSON.parse(area.dataset.chart || '[]');
    new ApexCharts(area, {
        chart: { ...base, type: 'bar', height: 300, zoom: { enabled: false } },
        series: [
            { name: 'Sales', data: data.map((d) => d.sales) },
            { name: 'Profit', data: data.map((d) => d.profit) },
        ],
        colors: [ink(), '#22c55e'],
        plotOptions: { bar: { columnWidth: '62%', borderRadius: 4, borderRadiusApplication: 'end' } },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        fill: { opacity: 1 },
        dataLabels: { enabled: false },
        legend: { show: false },
        xaxis: { categories: data.map((d) => d.d), tickAmount: 6, labels: { rotate: 0, style: { fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { formatter: compact, style: { fontSize: '11px' } } },
        grid: { borderColor: 'rgba(148,163,184,0.18)', strokeDashArray: 4, padding: { left: 6, right: 6 } },
        tooltip: { shared: true, intersect: false, y: { formatter: rs } },
    }).render();
}

const donut = document.getElementById('methods-donut');
if (donut) {
    const data = JSON.parse(donut.dataset.chart || '[]');
    const total = data.reduce((s, d) => s + d.value, 0);
    new ApexCharts(donut, {
        chart: { ...base, type: 'donut', height: 230 },
        series: total ? data.map((d) => d.value) : [1],
        labels: total ? data.map((d) => d.label) : ['No payments yet'],
        colors: total ? [ink(), '#3b82f6', '#22c55e', '#f59e0b'] : ['#e5e7eb'],
        legend: { show: false },
        dataLabels: { enabled: false },
        stroke: { width: 3, colors: [dark() ? '#161b22' : '#ffffff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '74%',
                    labels: {
                        show: true,
                        name: { offsetY: 20, fontSize: '12px', color: '#98a2b3' },
                        value: { offsetY: -14, fontSize: '20px', fontWeight: 800, color: ink(), formatter: (v) => (total ? compact(+v) : '0') },
                        total: { show: true, label: 'Received', color: '#98a2b3', fontSize: '12px', formatter: () => compact(total) },
                    },
                },
            },
        },
        tooltip: { enabled: !!total, y: { formatter: rs } },
    }).render();
}
