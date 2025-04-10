document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ 1001-scripts.js cargado correctamente');

    const contenedor = document.getElementById('contenedor-problemas');
    const barra = document.querySelector('.barra-problemas');

    function inicializarBarraProblemas() {
        document.querySelectorAll('.btn-azar').forEach(btn => {
            btn.addEventListener('click', () => {
                const cap = parseInt(btn.dataset.capitulo);
                const url = cap > 0
                    ? `https://pruebas.1001problemas.com/?problema_azar=1&capitulo=${cap}`
                    : `https://pruebas.1001problemas.com/?problema_azar=1`;
                sweetSorteoProblema(url);
            });
        });

        const inputBuscar = document.getElementById('filtro-problemas');
        if (inputBuscar) {
            inputBuscar.addEventListener('input', () => {
                const texto = inputBuscar.value.toLowerCase();
                console.log('🔍 Texto de búsqueda:', texto);

                const items = document.querySelectorAll('.problema-item');
                console.log('📦 Problemas en DOM:', items.length);

                items.forEach(div => {
                    const contenido = div.textContent.toLowerCase() + ' ' + div.getAttribute('data-letra');
                    const coincide = contenido.includes(texto);
                    div.style.display = coincide ? 'block' : 'none';
            
                    if (coincide) {
                        console.log(`✅ Coincidencia: ${div.textContent.trim()}`);
                    }
                });
            });
        }
    }

    let offset = 0;
    const cargando = document.getElementById('cargando-problemas');
    const botonVerMas = document.getElementById('ver-mas-problemas');
    let cargandoDatos = false;
    let categoria = contenedor ? contenedor.getAttribute('data-categoria') : '';

    function cargarProblemas() {
        if (cargandoDatos) return;
        cargandoDatos = true;

        if (cargando) cargando.style.display = 'block';
        if (botonVerMas) {
            botonVerMas.disabled = true;
            botonVerMas.textContent = 'Cargando...';
        }

        fetch(cienuno.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cargar_mas_problemas',
                offset: offset,
                categoria: categoria
            })
        })
        .then(res => res.json())
        .then(respuesta => {
            if (respuesta.success && respuesta.data.length > 0) {
                console.log('📦 Problemas cargados:', respuesta.data.map(p => p.num));

                respuesta.data.forEach(problema => {
                    const div = document.createElement('div');
                    div.className = 'problema-item';
                    div.setAttribute('data-num', problema.num);
                    div.setAttribute('data-letra', problema.letra_completa.toLowerCase());

                    if (problema.comentado) div.classList.add('comentado');

                    //div.style.backgroundImage = `url('${problema.imagen}')`;
                    
                    div.innerHTML = `
                        <a href="${problema.url}" class="problema-link">
                            <img src="${problema.imagen}" alt="Problema ${problema.num}" class="problema-img" loading="lazy" width="150" height="173" />
                            <div class="overlay-num">
                                <h4>Problema ${problema.num}</h4>
                            </div>
                        </a>
                    `;


                    contenedor.appendChild(div);
                });


                offset += respuesta.data.length;
                cargandoDatos = false;
                if (cargando) cargando.style.display = 'none';
                if (botonVerMas) {
                    botonVerMas.disabled = false;
                    botonVerMas.textContent = 'Ver más';
                }
            } else {
                if (cargando) cargando.innerText = '✔️ Todos los problemas cargados.';
                if (botonVerMas) botonVerMas.style.display = 'none';
            }
        });
    }

    if (botonVerMas) {
        botonVerMas.addEventListener('click', function () {
            let timerInterval;
            Swal.fire({
                title: "Cargando más problemas...",
                html: "Cierre automático en <b></b> ms.",
                timer: 1000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            });

            cargarProblemas();
        });
    }

    if (barra) inicializarBarraProblemas();
    if (contenedor) cargarProblemas();
});