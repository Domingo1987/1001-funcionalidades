document.addEventListener('DOMContentLoaded', () => {
    animarContadores();
    aplicarEstrellas();
    renderizarProgresoPorCategoria();
    renderizarInteraccionesIA();
    renderizarEvolucionTemporal();
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
    // 🔍 Buscar el contenedor del gráfico y el loader
    const contenedor = document.querySelector('#grafico-categorias');
    const loader = document.querySelector('#grafico-categorias-loader');

    // 💡 Remover siempre el loader
    loader?.remove();

    // ❌ Si no hay contenedor o no hay datos, salimos
    if (!contenedor || typeof dashboardData === 'undefined') return;

    const categorias = dashboardData.progresoPorCategoria;
    if (!Array.isArray(categorias) || categorias.length === 0) return;

    // ✂️ Acortar nombres si son muy largos
    const aliasCategorias = {
        "Introducción a la programación de computadores": "Intro a la programación",
        "Conceptos generales de los lenguajes de programación": "Conceptos generales",
        "Presentación del lenguaje C": "Lenguaje C",
        "Procedimientos y funciones": "Procedimientos",
        "Tipos de datos definidos por el programador": "TD definidos",
        "Tipos de datos estructurados": "TD estructurados",
        "Definición de tipos de datos dinámicos": "TD dinámicos",
        "Archivos": "Archivos",
        "Punteros": "Punteros"
      };
      

    // 🔢 Series y etiquetas para el gráfico
    const series = categorias.map(c => parseFloat(c.porcentaje));
    const labels = categorias.map(c => aliasCategorias[c.categoria] || c.categoria);

    // 🎨 Colores personalizados (puede crecer si hay más categorías)
    const colors = [
        '#1ab7ea', '#0084ff', '#39539E', '#0077B5',
        '#e91e63', '#ffc107', '#4caf50', '#9c27b0', '#795548'
    ];


    // ⚙️ Opciones del gráfico
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
        colors: colors.slice(0, series.length),
        responsive: [{
            breakpoint: 480,
            options: {
                legend: { show: false }
            }
        }]
    };

    // 🚀 Crear y mostrar el gráfico
    const chart = new ApexCharts(contenedor, options);
    chart.render();
}

function renderizarInteraccionesIA() {
    const contenedor = document.querySelector('#grafico-publicaciones-ia');
    if (!contenedor || typeof dashboardData === 'undefined') return;

    const loader = document.querySelector('#grafico-publicaciones-ia-loader');

    // 💡 Remover siempre el loader
    loader?.remove();

    const dataOriginal = dashboardData.interaccionesIA;
    if (!Array.isArray(dataOriginal) || dataOriginal.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">Sin publicaciones IA con comentarios.</p>';
        return;
    }

    // Agrupar publicaciones por categoría
    const agrupado = {};

    dataOriginal.forEach(pub => {
        const categoria = pub.categoria || 'Sin categoría';
        const titulo = pub.titulo || `Publicación ${pub.publicacion_id}`;
        const comentarios = parseInt(pub.comentarios || 0) + 1;

        if (!agrupado[categoria]) agrupado[categoria] = [];
        agrupado[categoria].push({ x: titulo, y: comentarios });
    });

    const series = Object.entries(agrupado).map(([nombre, publicaciones]) => ({
        name: nombre,
        data: publicaciones
    }));


    const options = {
        series: series,
        chart: {
            type: 'treemap',
            height: 390
        },
        title: {
            text: 'Publicaciones IA y comentarios',
            align: 'center'
        },
        legend: {
            show: false
        },
        tooltip: {
            y: {
              formatter: (val, opts) => {
                // Le restamos 1 para mostrar el valor real
                return (val - 1) + ' comentarios';
              }
            }
          }
    };

    const chart = new ApexCharts(contenedor, options);
    chart.render();
}

function renderizarEvolucionTemporal() {
    const contenedor = document.querySelector('#grafico-evolucion-temporal');
    const loader = document.querySelector('#grafico-evolucion-temporal-loader');
    loader?.remove();

    if (!contenedor || typeof dashboardData === 'undefined') return;

    const data = dashboardData.heatmapData;
    const coloresCategorias = dashboardData.coloresCategorias || {};

    if (!Array.isArray(data) || data.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">No hay participación registrada en los últimos 12 meses.</p>';
        return;
    }

    // ✂️ Alias para mostrar nombres cortos
    const aliasCategorias = {
        "Introducción a la programación de computadores": "Intro a la programación",
        "Conceptos generales de los lenguajes de programación": "Conceptos generales",
        "Presentación del lenguaje C": "Lenguaje C",
        "Procedimientos y funciones": "Procedimientos",
        "Tipos de datos definidos por el programador": "TD definidos",
        "Tipos de datos estructurados": "TD estructurados",
        "Definición de tipos de datos dinámicos": "TD dinámicos",
        "Archivos": "Archivos",
        "Punteros": "Punteros",
        "IA": "IA"
    };

    // 🔁 Reemplazar los nombres largos por los alias en cada serie
    const series = data.map(serie => ({
        name: aliasCategorias[serie.name] || serie.name,
        data: serie.data
    }));

    // 📋 Mostrar orden y valores en la consola
    console.table(series.map(s => ({
        Categoría: s.name,
        ParticipacionesTotales: s.data.reduce((acc, punto) => acc + punto.y, 0)
    })));

    const options = {
        series: series,
        chart: {
            height: 450,
            type: 'heatmap'
        },
        dataLabels: { enabled: true },
        colors: series.map(s => coloresCategorias[s.name] || '#ccc'),
        title: {
            text: 'Evolución mensual por categoría',
            align: 'center'
        },
        xaxis: {
            type: 'category'
        },
        tooltip: {
            y: {
                formatter: val => val + ' participación' + (val !== 1 ? 'es' : '')
            }
        }
    };

    const chart = new ApexCharts(contenedor, options);
    chart.render();
}
