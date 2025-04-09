function sweetSorteoProblema(url) {
    Swal.fire({
        title: "⚙️ Compilando el dado...",
        html: "Por favor espera...",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();

            setTimeout(() => {
                Swal.update({
                    title: "🧠 Iniciando algoritmo de selección...",
                    html: `<div id="contador-problema" style="font-size: 1.5rem;">...</div>`
                });

                const display = Swal.getPopup().querySelector("#contador-problema");
                let count = 0;
                const fakeInterval = setInterval(() => {
                    const random = Math.floor(Math.random() * 1000) + 1;
                    if (display) display.textContent = `Posible problema #${random}`;
                    count++;
                    if (count > 15) clearInterval(fakeInterval);
                }, 40);

                setTimeout(() => {
                    if (display) display.textContent = `Problema desbloqueado 🎉`;
                    Swal.update({
                        title: `🔓 Problema listo`,
                        html: "¡A resolverlo!",
                        icon: "success"
                    });
                }, 1200);
            }, 1400);

            setTimeout(() => {
                window.location.href = url;
            }, 3500);
        }
    });
}

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
                document.querySelectorAll('.problema-item').forEach(div => {
                    const contenido = div.textContent.toLowerCase();
                    div.style.display = contenido.includes(texto) ? 'block' : 'none';
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
                    div.style.backgroundImage = `url('${problema.imagen}')`;
                    div.setAttribute('data-num', problema.num);
                    div.setAttribute('data-letra', problema.letra_completa || '');

                    if (problema.comentado) div.classList.add('comentado');

                    div.innerHTML = `
                        <a href="${problema.url}" class="problema-link">
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
