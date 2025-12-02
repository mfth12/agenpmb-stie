document.addEventListener("DOMContentLoaded", function () {
    if (window.ApexCharts && document.getElementById("chart-pendaftaran") && window.pendaftaranChartData) {
        const options = {
            chart: {
                type: "line",
                fontFamily: "inherit",
                height: 240,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                },
            },
            stroke: {
                width: 2,
                lineCap: "round",
                curve: "smooth",
            },
            series: [
                {
                    name: "Pendaftar",
                    data: window.pendaftaranChartData.data,
                },
            ],
            tooltip: {
                theme: "dark",
            },
            grid: {
                padding: {
                    top: -20,
                    right: 0,
                    left: -4,
                    bottom: -4,
                },
                strokeDashArray: 4,
            },
            xaxis: {
                labels: {
                    padding: 0,
                },
                tooltip: {
                    enabled: false,
                },
                categories: window.pendaftaranChartData.labels,
            },
            yaxis: {
                labels: {
                    padding: 4,
                },
            },
            colors: ["var(--tblr-primary)"],
        };

        new ApexCharts(document.getElementById("chart-pendaftaran"), options).render();
    }
});