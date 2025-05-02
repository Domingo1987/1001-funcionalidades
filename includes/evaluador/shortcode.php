<?php
// Shortcode para el evaluador de problemas
// Este shortcode se utiliza para mostrar un formulario donde los usuarios pueden enviar un problema y una solución para su evaluación.

function evaluador_problemas_shortcode() {

    $user_id = is_user_logged_in() ? get_current_user_id() : 1001;

    $problemaDefault = "Escribe una función en Python que reciba dos números y retorne su suma.";
    $solucionDefault = "def suma(a, b):\n    return a + b\n\n# Ejemplo de uso:\nresultado = suma(2, 3)\nprint(resultado)  # Debería imprimir 5";

    $problema = isset($_POST['problema']) ? sanitize_textarea_field($_POST['problema']) : $problemaDefault;
    $solucion = isset($_POST['solucion']) ? sanitize_textarea_field($_POST['solucion']) : $solucionDefault;

    $problemasDisponibles = obtener_problemas_practicos_usuario($user_id);

    ob_start();
    ?>
    <div class="evaluador-problemas-container">
        <form id="evaluarFormulario" method="POST" action="">

            <!-- ✅ Switch para activar uso de desafíos precargados -->
            <div class="evaluador-form-group">
                <label for="usarExistente">
                    <input type="checkbox" id="usarExistente" name="usarExistente" role="switch" />
                    Usar desafío precargado de mi curso
                </label>
            </div>

            <!-- ✅ Selector visible solo si el switch está activado -->
            <div class="evaluador-form-group" id="selectorDesafio" style="display: none;">
                <label for="desafioSeleccionado">Seleccionar desafío:</label>
                <select id="desafioSeleccionado" name="desafioSeleccionado">
                    <option value="">-- Elegí un desafío --</option>
                    <?php foreach ($problemasDisponibles as $item): ?>
                        <option value="<?= esc_attr($item->descripcion); ?>">
                            <?= esc_html($item->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 📝 Campo editable o autocompletado -->
            <div class="evaluador-form-group">
                <label for="problema">Problema:</label>
                <textarea id="problema" name="problema" required><?= esc_textarea($problema) ?></textarea>
            </div>

            <div class="evaluador-form-group">
                <label for="solucion">Solución:</label>
                <textarea id="solucion" name="solucion" required><?= esc_textarea($solucion) ?></textarea>
            </div>

            <!-- 🔒 ID oculto del problema (si fue seleccionado de la lista) -->
            <input type="hidden" name="problema_id" id="problema_id" value="0" />
            <input type="hidden" name="user_id3" value="<?= esc_attr($user_id); ?>" />

            <button type="submit" id="evaluarBoton" class="evaluador-button">Evaluar</button>
            <button type="button" id="subirOtro" class="evaluador-button" style="display:none;">Subir Otro Problema</button>
        </form>

        <!-- Resultado detallado por criterio -->
        <div id="resultadoEvaluacion" class="evaluador-resultado"></div>

        <!-- Retroalimentación comparativa final -->
        <div id="evaluacionComparativaFinal" class="evaluador-retro-final" style="margin-top: 2rem;"></div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = document.getElementById('desafioSeleccionado');
            const problemaIdInput = document.getElementById('problema_id');

            if (selector && problemaIdInput) {
                selector.addEventListener('change', function () {
                    const selected = selector.options[selector.selectedIndex];
                    const id = selected.dataset.id || 0;
                    problemaIdInput.value = id;
                });
            }
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('evaluador_problemas', 'evaluador_problemas_shortcode');
