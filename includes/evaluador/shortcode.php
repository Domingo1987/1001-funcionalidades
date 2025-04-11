<?php
// Shortcode para el evaluador de problemas
// Este shortcode se utiliza para mostrar un formulario donde los usuarios pueden enviar un problema y una solución para su evaluación.

function evaluador_problemas_shortcode() {
    if (!is_user_logged_in()) {
        // Usuario no logueado
        if (isset($_COOKIE['evaluador_anonimo_usado'])) {
            // 🔍 Log en consola para verificar que detecta la cookie
            echo "<script>console.log('🔁 Cookie evaluador_anonimo_usado detectada. Valor: " . esc_js($_COOKIE['evaluador_anonimo_usado']) . "');</script>";

            // Solo si su valor es '1'
            if ($_COOKIE['evaluador_anonimo_usado'] === '1') {
                return mostrar_modal_redireccion_login(2000, 'Ya utilizaste tu intento anónimo. Inicia sesión para más intentos.');
            }
        } elseif (!isset($_COOKIE['acepto_cookies']) || $_COOKIE['acepto_cookies'] !== '1') {
            // No aceptó cookies → tampoco permitimos uso
            // 🔍 Log en consola para verificar que detecta la cooki
            echo "<script>console.log('🔁 Cookie acepto_cookies no detectada. Valor: " . esc_js($_COOKIE['acepto_cookies']) . "');</script>";
            return mostrar_modal_redireccion_login(2000, 'Esta herramienta requiere aceptar cookies');
        } else {
            // Aceptó cookies y es su primer intento → permitimos y creamos la cookie de uso
            setcookie('evaluador_anonimo_usado', '1', time() + 3600, '/', $_SERVER['HTTP_HOST']);
            echo "<script>console.log('✅ Cookie evaluador_anonimo_usado creada');</script>";
        }
    }

    // ✅ Usuario actual o visitante anónimo con ID simbólico
    $user_id = is_user_logged_in() ? get_current_user_id() : 1001;

    // Problema y solución predeterminados
    $problemaDefault = "Escribe una función en Python que reciba dos números y retorne su suma.";
    $solucionDefault = "def suma(a, b):\n    return a + b\n\n# Ejemplo de uso:\nresultado = suma(2, 3)\nprint(resultado)  # Debería imprimir 5";

    // Obtener el problema y la solución enviados por el usuario o usar los predeterminados
    $problema = isset($_POST['problema']) ? sanitize_textarea_field($_POST['problema']) : $problemaDefault;
    $solucion = isset($_POST['solucion']) ? sanitize_textarea_field($_POST['solucion']) : $solucionDefault;

    ob_start();
    ?>
    <div class="evaluador-problemas-container">
        <form id="evaluarFormulario" method="POST" action="">
            <div class="evaluador-form-group">
                <label for="problema">Problema:</label>
                <textarea id="problema" name="problema" required><?= esc_textarea($problema) ?></textarea>
            </div>

            <div class="evaluador-form-group">
                <label for="solucion">Solución:</label>
                <textarea id="solucion" name="solucion" required><?= esc_textarea($solucion) ?></textarea>
            </div>

            <!-- Campo oculto para pasar el user_id -->
            <input type="hidden" name="user_id3" value="<?= esc_attr($user_id); ?>" />

            <button type="submit" id="evaluarBoton" class="evaluador-button">Evaluar</button>
            <button type="button" id="subirOtro" class="evaluador-button" style="display:none;">Subir Otro Problema</button>
        </form>

        <!-- Contenedor donde se mostrarán los resultados -->
        <div id="resultadoEvaluacion" class="evaluador-resultado"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('evaluador_problemas', 'evaluador_problemas_shortcode');