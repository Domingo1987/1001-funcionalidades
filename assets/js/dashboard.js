document.addEventListener('DOMContentLoaded', () => {
    animarContadores();
    aplicarEstrellas();
    renderizarProgresoPorCategoria();
});

function animarContadores() {
    const contadores = document.querySelectorAll('.contador-animado');

    contadores.forEach(contador => {
        const final = parseInt(contador.dataset.valor, 10);
        let actual = 0;
        const duracion = 1000; // milisegundos
        const incremento = Math.max(1, Math.ceil(final / (duracion / 16)));

        const timer = setInterval(() => {
            actual += incremento;
            if (actual >= final) {
                actual = final;
                clearInterval(timer);
            }
            contador.textContent = actual;
        }, 16);
    });
}

function aplicarEstrellas() {
    document.querySelectorAll('.estrellas').forEach(el => {
        const promedio = parseFloat(el.dataset.promedio || 0);
        el.style.setProperty('--estrella-promedio', promedio);
    });
}

function renderizarProgresoPorCategoria() {
    const contenedor = document.querySelector('#grafico-categorias');
    if (!contenedor || typeof dashboardData === 'undefined') return;

    const categorias = dashboardData.progresoPorCategoria;
    if (!Array.isArray(categorias) || categorias.length === 0) return;

    const series = categorias.map(c => c.porcentaje);
    const labels = categorias.map(c => c.categoria);

    const colors = [
        '#1ab7ea', '#0084ff', '#39539E', '#0077B5',
        '#e91e63', '#ffc107', '#4caf50', '#9c27b0', '#795548'
    ]; // podés agregar más si hay más categorías

    const options = {
        series: series,
        chart: {
            height: 390,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: {
                    margin: 5,
                    size: '30%',
                    background: 'transparent',
                },
                dataLabels: {
                    name: { show: false },
                    value: { show: false }
                },
                barLabels: {
                    enabled: true,
                    useSeriesColors: true,
                    offsetX: -8,
                    fontSize: '16px',
                    formatter: function(seriesName, opts) {
                        return seriesName + ": " + opts.w.globals.series[opts.seriesIndex] + "%";
                    },
                },
            }
        },
        labels: labels,
        colors: colors.slice(0, series.length), // limitar a la cantidad de categorías
        responsive: [{
            breakpoint: 480,
            options: {
                legend: { show: false }
            }
        }]
    };

    const chart = new ApexCharts(contenedor, options);
    chart.render();
}
