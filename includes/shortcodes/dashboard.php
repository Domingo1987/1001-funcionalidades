<?php
// Archivo: shortcodes/dashboard.php

if (!defined('ABSPATH')) exit;

function shortcode_dashboard() {
    if (!is_user_logged_in()) {
        return '<article class="contrast"><p>Debes estar logueado para ver tu tablero personalizado.</p></article>';
    }

    // Datos simulados (luego irán desde la BD)
    $problemas = 145;
    $puntaje = 17.8;
    $tendencia = +12.5;
    $comentarios = 62;
    $ia_posts = 7;
    $medallas = 3;

    $color = $tendencia >= 0 ? 'green' : 'red';
    $icono = $tendencia >= 0 ? '⬆️' : '⬇️';
    $tendencia_valor = abs($tendencia); // solo el número positivo

    // Datos de la base de datos (simulados)
    global $wpdb;
    $user_id = get_current_user_id();

    ob_start();
    ?>
    <section class="container">
        <details open>
            <summary>📌 Resumen general</summary>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin-top: 1rem;">
                <article class="card-resumen">
                    <h3>💻</h3>
                    <p>Problemas</p>
                    <strong class="contador-animado" data-valor="<?php echo $problemas; ?>">0</strong>
                </article>
                <article class="card-resumen">
                    <h3>📊</h3>
                    <p>Puntaje Promedio</p>
                    <strong class="contador-animado" data-valor="<?php echo $puntaje; ?>">0</strong>
                </article>

                <article class="card-resumen">
                    <h3 style="color: <?php echo $color; ?>;"><?php echo $icono; ?></h3>
                    <p>Tendencia</p>
                    <strong style="color: <?php echo $color; ?>;">
                        <span class="contador-animado" data-valor="<?php echo $tendencia_valor; ?>">0</span>%
                    </strong>
                </article>

                <article class="card-resumen">
                    <h3>💬</h3>
                    <p>Comentarios</p>
                    <strong class="contador-animado" data-valor="<?php echo $comentarios; ?>">0</strong>
                </article>

                <article class="card-resumen">
                    <h3>🤖</h3>
                    <p>Post IA</p>
                    <strong class="contador-animado" data-valor="<?php echo $ia_posts; ?>">0</strong>
                </article>

                <article class="card-resumen">
                    <h3>🏅</h3>
                    <p>Medallas</p>
                    <strong class="contador-animado" data-valor="<?php echo $medallas; ?>">0</strong>
                </article>

            </section>
        </details>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('dashboard', 'shortcode_dashboard');


/*function shortcode_dashboard() {

    global $wpdb;
    $user_id = get_current_user_id();

    // 1. Datos principales
    $total_eval = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d", $user_id));
    $prom_eval = $wpdb->get_var($wpdb->prepare("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d", $user_id));
    $mensajes = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d", $user_id));
    $resueltos = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(DISTINCT p.ID) 
        FROM {$wpdb->posts} p
        JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE c.user_id = %d 
        AND pm.meta_key = 'num_problema'
        AND p.post_status = 'publish'
    ", $user_id));

    // 2. Tendencia
    $ultimas = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d ORDER BY fecha_evaluacion DESC LIMIT 5", $user_id));
    $prom_ult = !empty($ultimas) ? $wpdb->get_var("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE id IN (" . implode(",", $ultimas) . ")") : 0;
    $prom_hist = !empty($ultimas) ? $wpdb->get_var("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE user_id = $user_id AND id NOT IN (" . implode(",", $ultimas) . ")") : 0;
    $mejora = $prom_hist > 0 ? number_format((($prom_ult - $prom_hist) / $prom_hist) * 100, 2) : 0;
    $clase_mejora = $mejora > 0 ? 'green' : 'red';
    $icono = $mejora > 0 ? '↑' : '↓';

    // 3. Gráfico radar (usuario vs grupo)
    $grafico1_usuario = $wpdb->get_results($wpdb->prepare("
        SELECT ec.criterio_id, AVG(ec.criterio_puntos) as promedio_puntos
        FROM {$wpdb->prefix}evaluaciones_criterios ec
        JOIN {$wpdb->prefix}evaluaciones e ON ec.evaluacion_id = e.id
        WHERE e.user_id = %d
        GROUP BY ec.criterio_id
        ORDER BY FIELD(ec.criterio_id, 1, 2, 4, 3)
    ", $user_id));

    $grafico1_grupo = $wpdb->get_results("
        SELECT ec.criterio_id, AVG(ec.criterio_puntos) as promedio_puntos
        FROM {$wpdb->prefix}evaluaciones_criterios ec
        JOIN {$wpdb->prefix}evaluaciones e ON ec.evaluacion_id = e.id
        GROUP BY ec.criterio_id
        ORDER BY FIELD(ec.criterio_id, 1, 2, 4, 3)
    ");

    // 4. Gráfico de barras (últimas 10 evaluaciones)
    $grafico2 = $wpdb->get_results($wpdb->prepare("
        SELECT e.id, e.total_puntos, DATE_FORMAT(e.fecha_evaluacion, '%%Y-%%m-%%d') as fecha_evaluacion
        FROM {$wpdb->prefix}evaluaciones e
        WHERE e.user_id = %d
        ORDER BY e.fecha_evaluacion DESC
        LIMIT 10
    ", $user_id));

    ob_start();
    ?>
*/



   