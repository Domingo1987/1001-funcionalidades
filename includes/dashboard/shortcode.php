<?php

function shortcode_dashboard() {
    ob_start();
    ?>
    <section class="container">
        <details data-seccion="resumen-general">
            <summary>📌 Resumen general</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>
        
        <details data-seccion="progreso-categorias">
            <summary>📊 Progreso por categoría</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="publicaciones-ia">
            <summary>📊 Comentarios por publicación IA</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="progreso-competencias">
            <summary>📐 Progreso por competencia</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="medallas">
            <summary>🏅 Medallas de participación</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="interacciones-sociales">
            <summary>💬 Interacciones sociales</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="evolucion-temporal">
            <summary>📈 Evolución temporal</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>

        <details data-seccion="actividad-por-tipo">
            <summary>🔍 Actividad por tipo de contenido</summary>
            <div class="contenido-seccion">Cargando...</div>
        </details>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('dashboard', 'shortcode_dashboard');
