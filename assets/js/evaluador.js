document.addEventListener('DOMContentLoaded', function () {
    verificarIntentoAnonimo();




    const form = document.getElementById('evaluarFormulario');
    const evaluarBoton = document.getElementById('evaluarBoton');
    const resultadoEvaluacion = document.getElementById('resultadoEvaluacion');
    const subirOtro = document.getElementById('subirOtro');

    form.addEventListener('submit', handleSubmit);
    subirOtro.addEventListener('click', resetForm);

    async function handleSubmit(e) {
        e.preventDefault();
        const problema = document.getElementById('problema').value;
        const solucion = document.getElementById('solucion').value.trim();
        const user_id3 = document.querySelector('input[name="user_id3"]').value; // Obtener el user_id del campo hidden

        evaluarBoton.disabled = true;
        resultadoEvaluacion.innerHTML = '<p class="evaluador-evaluando">Evaluando...</p>';

        try {
            // Enviar el problema y la solución al servidor para evaluar
            const response = await fetch('/wp-json/evaluador/v1/evaluar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ problema, solucion, user_id3 })
            });

            if (!response.ok) throw new Error('Error en la solicitud: ' + response.statusText);

            const data = await response.json();

            // Verificar si se recibió algún dato antes de mostrar
            if (data && data.criterios) {
                mostrarEvaluacion(data);
                evaluarBoton.style.display = 'none';
                subirOtro.style.display = 'block';

                // 👇 Marcar que ya lo usó (si no está logueado)
                if (!document.body.classList.contains('logged-in')) {
                    sessionStorage.setItem('evaluador_anonimo_usado', '1');
                }

                if (typeof actualizarDashboard === 'function') {
                    actualizarDashboard();
                }
            } else if (data && data.error) {
                resultadoEvaluacion.innerHTML = `<p class="evaluador-error">${data.error}</p>`;
                evaluarBoton.disabled = false;
            } else {
                resultadoEvaluacion.innerHTML = '<p class="evaluador-error">No se pudo obtener una evaluación. Inténtalo de nuevo.</p>';
                evaluarBoton.disabled = false;
            }
        } catch (error) {
            console.error('Ocurrió un error:', error);
            resultadoEvaluacion.innerHTML = '<p class="evaluador-error">Hubo un error al evaluar el problema. Por favor, intenta nuevamente.</p>';
            evaluarBoton.disabled = false;
        }
    }

    function mostrarEvaluacion(data) {
        resultadoEvaluacion.innerHTML = '';  // Limpiar contenido anterior

        // Mostrar la evaluación de cada criterio desglosado
        data.criterios.forEach(function (criterio, index) {
            resultadoEvaluacion.innerHTML += `
                <div class="evaluador-criterio">
                    <div class="evaluador-titulo">Criterio ${index + 1}: ${criterio.criterio}</div>
                    <div class="evaluador-form-group">
                        <div class="evaluador-subtitulo">Puntaje Asignado</div>
                        <div class="evaluador-puntaje">${criterio.puntaje_asignado} / ${criterio.puntaje_maximo}</div>
                    </div>
                    <div class="evaluador-form-group">
                        <div class="evaluador-subtitulo">Justificación</div>
                        <div class="evaluador-texto">${criterio.justificacion}</div>
                    </div>
                    <div class="evaluador-form-group">
                        <div class="evaluador-subtitulo">Retroalimentación</div>
                        <div class="evaluador-texto">${criterio.retroalimentacion}</div>
                    </div>
                </div>
            `;
        });

        // Mostrar el total de puntos
        if (data.total_puntos !== undefined) {
            resultadoEvaluacion.innerHTML += `<div class="evaluador-total-puntos">Total de puntos: <strong>${data.total_puntos} / 24</strong></div>`;
        }
    }

    function resetForm() {
        form.reset();
        resultadoEvaluacion.innerHTML = '';
        evaluarBoton.style.display = 'block';
        evaluarBoton.disabled = false;
        subirOtro.style.display = 'none';
    }
});

function verificarIntentoAnonimo() {
    const yaUsado = sessionStorage.getItem('evaluador_anonimo_usado');
    const estaLogueado = document.body.classList.contains('logged-in');

    console.log('🧠 ¿Usuario está logueado?', estaLogueado);
    console.log('📦 ¿Session evaluador usado?', yaUsado);

    if (!estaLogueado && yaUsado === '1') {
        console.log('🚫 Mostrando modal visual tipo PHP');

        const modal = document.createElement('dialog');
        modal.id = 'modal-construccion'; // ⬅️ Reutiliza el mismo estilo CSS
        modal.innerHTML = `
            <main data-theme="pico" style="position: relative; padding: 0;">
                <div style="position: relative;">
                    <img src="/wp-content/plugins/1001-funcionalidades/assets/img/inicia_sesion.webp"
                        alt="Inicia Sesión"
                        style="width: 100%; max-height: 90vh; object-fit: contain; border-radius: 10px;" />

                    <div style="
                        position: absolute;
                        bottom: 0;
                        width: 100%;
                        background: rgba(0, 0, 0, 0.6);
                        color: white;
                        text-align: center;
                        padding: 0.75rem 1rem;
                        font-weight: bold;
                        font-size: 1rem;
                    ">
                        Ya usaste tu intento anónimo. Inicia sesión para continuar.
                    </div>
                </div>

                <form method="dialog" class="text-center" style="margin-top: -1rem;">
                    <button onclick="window.location.href='/login/'" class="primary">Iniciar sesión</button>
                </form>
            </main>
        `;

        document.body.appendChild(modal);
        modal.showModal();
    }
}
