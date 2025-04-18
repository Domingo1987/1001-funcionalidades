document.addEventListener('DOMContentLoaded', () => {
  // Manejadores para cada sección con su callback específica

  document.querySelector('details[data-seccion="resumen-general"]')
    ?.addEventListener('toggle', () => {
      cargarSeccionDashboard('resumen-general', animarContadores);
    });

  document.querySelector('details[data-seccion="progreso-categorias"]')
    ?.addEventListener('toggle', () => {
      cargarSeccionDashboard('progreso-categorias', renderizarProgresoCategorias);
    });

    document.querySelector('details[data-seccion="publicaciones-ia"]')
    ?.addEventListener('toggle', () => {
      cargarSeccionDashboard('publicaciones-ia', renderizarPublicacionesIA);
    });

    document.querySelector('details[data-seccion="progreso-competencias"]')
  ?.addEventListener('toggle', () => {
    cargarSeccionDashboard('progreso-competencias', renderizarRadarCompetencias);
  });

  document.querySelector('details[data-seccion="medallas"]')
  ?.addEventListener('toggle', () => {
    cargarSeccionDashboard('medallas', renderizarMedallas);
  });

  document.querySelector('details[data-seccion="interacciones-sociales"]')
  ?.addEventListener('toggle', () => {
    cargarSeccionDashboard('interacciones-sociales');
  });

  document.querySelector('details[data-seccion="evolucion-temporal"]')
  ?.addEventListener('toggle', () => {
    cargarSeccionDashboard('evolucion-temporal', renderizarEvolucionTemporal);
  });


  
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

function cargarSeccionDashboard(seccion, callback) {
  const detalle = document.querySelector(`details[data-seccion="${seccion}"]`);

  if (!detalle || detalle.dataset.cargado) return;

  const contenedor = detalle.querySelector('.contenido-seccion');

  fetch(ajaxurl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      action: 'dashboard_cargar_seccion',
      seccion: seccion
    })
  })
    .then(res => res.text())
    .then(html => {
      contenedor.innerHTML = html;
      detalle.dataset.cargado = 'true';

      if (typeof callback === 'function') callback();

      console.log(`✅ Sección ${seccion} cargada.`);
    })
    .catch(err => {
      contenedor.innerHTML = '<p class="text-danger">Error al cargar los datos.</p>';
      console.error(err);
    });
}

function renderizarProgresoCategorias() {
  const contenedor = document.querySelector('#grafico-categorias');
  const loader = document.querySelector('#grafico-categorias-loader');

  loader?.remove();
  if (!contenedor || typeof dashboardData === 'undefined') return;

  const categorias = dashboardData.progresoPorCategoria;
  if (!Array.isArray(categorias) || categorias.length === 0) return;

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

  const series = categorias.map(c => parseFloat(c.porcentaje));
  const labels = categorias.map(c => aliasCategorias[c.categoria] || c.categoria);

  const colors = [
    '#1ab7ea', '#0084ff', '#39539E', '#0077B5',
    '#e91e63', '#ffc107', '#4caf50', '#9c27b0', '#795548'
  ];

  const options = {
    series: series,
    chart: { height: 390, type: 'radialBar' },
    plotOptions: {
      radialBar: {
        offsetY: 0,
        startAngle: 0,
        endAngle: 270,
        hollow: { margin: 5, size: '30%', background: 'transparent' },
        dataLabels: { name: { show: false }, value: { show: false } },
        barLabels: {
          enabled: true,
          useSeriesColors: true,
          offsetX: -8,
          fontSize: '16px',
          formatter: (seriesName, opts) => seriesName + ": " + opts.w.globals.series[opts.seriesIndex] + "%"
        }
      }
    },
    labels: labels,
    colors: colors.slice(0, series.length),
    responsive: [{ breakpoint: 480, options: { legend: { show: false } } }]
  };

  const chart = new ApexCharts(contenedor, options);
  chart.render();
}

function renderizarPublicacionesIA() {
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

function renderizarRadarCompetencias() {
    const contenedor = document.querySelector('#grafico-radar-competencias');
    const loader = document.querySelector('#grafico-radar-competencias-loader');
    loader?.remove();
  
    if (!contenedor || typeof dashboardData === 'undefined') return;
  
    const radarData = dashboardData.radarCompetencias;
    if (!radarData || !Array.isArray(radarData.series) || radarData.series.length === 0) {
      contenedor.innerHTML = '<p class="text-muted">No hay evaluaciones registradas aún.</p>';
      return;
    }
  
    // 🧪 Mostrar en consola los datos (debug)
    console.table(radarData.series.map(s => ({
      Serie: s.name,
      ...s.data.reduce((acc, val, i) => {
        acc[radarData.labels[i]] = val;
        return acc;
      }, {})
    })));
  
    const options = {
      series: radarData.series,
      chart: {
        height: 450,
        type: 'radar'
      },
      title: {
        text: 'Progreso por competencia',
        align: 'center'
      },
      xaxis: {
        categories: radarData.labels
      },
      stroke: {
        width: 2
      },
      fill: {
        opacity: 0.25
      },
      markers: {
        size: 4
      },
      tooltip: {
        y: {
          formatter: val => val + ' pts'
        }
      },
      legend: {
        position: 'top',
        horizontalAlign: 'center'
      }
    };
  
    const chart = new ApexCharts(contenedor, options);
    chart.render();
  }

function renderizarMedallas() {
  const contenedor = document.querySelector('#medallas');
  const loader = document.querySelector('#medallas-loader');
  if (!contenedor || typeof dashboardData?.userMedallas === 'undefined') {
    console.warn('⚠️ Contenedor o datos de medallas no disponibles');
    return;
  }
  loader?.remove();

  const datos = dashboardData.userMedallas;
  const base = dashboardData.medallasBase;
  const niveles = ["", "bronce", "plata", "oro", "rubi", "diamante"];


    console.log('📦 Datos de medallas:', datos); // 👈 Mostrás todo lo que trae

    const medallas = [
      {
        clave: 'explorador',
        nombre: 'Explorador',
        descripcion: [
          '',
          'Visitaste 1 problema',
          'Resolvés 5 problemas',
          'Resolvés 10 problemas',
          'Resolvés 25 problemas',
          'Resolvés 50 problemas'
        ],
        icono: base + 'explorador-icono.svg'
      },
      {
        clave: 'colaborador',
        nombre: 'Colaborador',
        descripcion: [
          '',
          'Hiciste tu primer comentario o respuesta en IA',
          'Comentaste o respondiste en 5 publicaciones IA',
          'Participaste en 10 publicaciones IA',
          'Comentaste o respondiste en 25 publicaciones IA',
          'Participaste activamente en 50 publicaciones IA'
        ],
        icono: base + 'colaborador-icono.svg'
      },
      {
        clave: 'valorado',
        nombre: 'Valorado',
        descripcion: [
          '',
          'Recibiste tu primer like',
          'Recibiste 5 likes en total',
          'Recibiste 10 likes entre comentarios y publicaciones',
          'Recibiste 25 likes acumulados',
          'Recibiste 50 likes o más en la comunidad'
        ],
        icono: base + 'valorado-icono.svg'
      },
      {
        clave: 'multilenguaje',
        nombre: 'Multilenguaje',
        descripcion: [
          '',
          'Resolviste 1 problema en 1 lenguaje',
          'Resolviste problemas en 2 lenguajes distintos',
          'Resolviste en 3 lenguajes',
          'Resolviste 5 o más por lenguaje',
          'Resolviste 10+ por lenguaje en todos'
        ],
        icono: base + 'multilenguaje-icono.svg'
      },
      {
        clave: 'creadorIA',
        nombre: 'Creador IA',
        descripcion: [
          '',
          'Creaste tu primera publicación IA',
          'Creaste 3 publicaciones IA',
          'Creaste 5 publicaciones IA',
          'Creaste 10 publicaciones IA',
          'Creaste 20 publicaciones IA'
        ],
        icono: base + 'creadoria-icono.svg'
      }
    ];
  
    medallas.forEach(medalla => {
      const info = datos[medalla.clave];
      const nivel = info?.nivel || 0;
      if (nivel === 0) return;
  
      const div = document.createElement('div');
      div.className = `medalla-capas ${niveles[nivel]}`;
      div.setAttribute('data-tooltip', `Nivel ${nivel} - ${medalla.nombre}: ${medalla.descripcion[nivel]}`);
  
      div.innerHTML = `
        <div class="fondo"></div>
        <img class="icono" src="${medalla.icono}" alt="${medalla.nombre}">
        <svg viewBox="0 0 100 100" class="svg-titulo" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <path id="curvaTexto-${medalla.clave}" d="M10,45 A40,40 0 1,1 90,45" fill="none"/>
          </defs>
          <text fill="white" font-size="6" font-weight="bold" text-anchor="middle">
            <textPath xlink:href="#curvaTexto-${medalla.clave}" startOffset="50%">
              ${medalla.nombre.toUpperCase()}
            </textPath>
          </text>
        </svg>
        
      `;
  
      contenedor.appendChild(div);
    });
  
    
}


function renderizarEvolucionTemporal() {
    const contenedor = document.querySelector('#grafico-evolucion-temporal');
    const loader = document.querySelector('#grafico-evolucion-temporal-loader');
    loader?.remove();

    if (!contenedor || typeof dashboardData === 'undefined') return;

    const data = dashboardData.heatmapData;
    const coloresOriginales = dashboardData.coloresCategorias || {};

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

    // 🎨 Sincronizar colores con los alias
    const coloresAlias = Object.fromEntries(
        Object.entries(coloresOriginales).map(([key, color]) => [
            aliasCategorias[key] || key,
            color
        ])
    );

    // 🔁 Aplicar alias a cada serie
    const series = data.map(serie => ({
        name: aliasCategorias[serie.name] || serie.name,
        data: serie.data
    }));

    // 🧪 Mostrar en consola para depuración
    console.table(series.map(s => ({
        Categoría: s.name,
        ParticipacionesTotales: s.data.reduce((acc, punto) => acc + punto.y, 0),
        ColorAsignado: coloresAlias[s.name] || '#ccc'
    })));

    // ⚙️ Opciones del gráfico
    const options = {
        series: [...series].reverse(), // Apex muestra de abajo hacia arriba
        chart: {
            height: 450,
            type: 'heatmap'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
              return val === 0 ? '' : val;
            }
          },
          
        colors: series.map(s => coloresAlias[s.name] || '#ccc'),
        title: {
            text: 'Evolución mensual por categoría',
            align: 'center'
        },
        xaxis: {
            type: 'category'
        },
        tooltip: {
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
              const val = series[seriesIndex][dataPointIndex];
              if (val === 0) return '';
          
              const label = w.globals.seriesNames[seriesIndex];
              const mes = w.globals.labels[dataPointIndex];
              const color = w.config.colors[seriesIndex] || '#007bff';
          
              return `
                <article class="card-1001" style="
                  --color: ${color};
                  min-width: auto;
                  flex: none;
                  width: auto;
                  max-width: 240px;
                ">
                  <div class="barra-color"></div>
                  <div class="contenido-card" style="
                    flex-direction: column;
                    align-items: center;
                    padding: 0.75rem;
                  ">
                    <div class="texto" style="text-align: center;">
                        <p style="margin: 0; font-size: 1rem; font-weight: bold;">${label}</p>
                      <p style="margin: 0 0 0.25rem 0; font-size: 0.75rem;">${mes}</p>
        <p style="margin: 0; font-size: 0.9rem;"><strong>${val}</strong> ${val === 1 ? 'participación' : 'participaciones'}</p>
                    </div>
                  </div>
                </article>
              `;
            }
          }
          
          
          
          
    };

    // 🚀 Renderizar gráfico
    const chart = new ApexCharts(contenedor, options);
    chart.render();
}


  
