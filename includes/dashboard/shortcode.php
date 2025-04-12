<?php

function shortcode_dashboard() {
    ob_start();
    ?>
    <section class="container">
        <?php
        render_resumen_general();
        error_log('✅ render_resumen_general ejecutado');

        render_progreso_por_categorias(); // ✅ nueva sección
        render_publicaciones_ia();
        render_medallas();
        render_interacciones_sociales();
        render_progreso_por_competencias();
        render_evolucion_temporal();
        ?>
    </section>
    <?php
    error_log('🔚 Final del shortcode_dashboard() alcanzado.');
    return ob_get_clean();
}
add_shortcode('dashboard', 'shortcode_dashboard');
