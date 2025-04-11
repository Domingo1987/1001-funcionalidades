document.addEventListener('DOMContentLoaded', function () {
    const yaUsado = sessionStorage.getItem('evaluador_anonimo_usado');
    const estaLogueado = document.body.classList.contains('logged-in'); // Clase que agrega WordPress

    if (!estaLogueado && yaUsado === '1') {
        const modal = document.createElement('dialog');
        modal.id = 'modal-evaluador-limitado';
        modal.innerHTML = `
            <main data-theme="pico" style="max-width:500px;text-align:center;">
                <p>Ya usaste tu intento anónimo del evaluador.</p>
                <button onclick="window.location.href='/login/'">Iniciar Sesión</button>
            </main>
        `;
        document.body.appendChild(modal);
        modal.showModal();
        return;
    }


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
