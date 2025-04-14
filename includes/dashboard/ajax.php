<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_dashboard_cargar_seccion', 'dashboard_ajax_cargar_seccion');

function dashboard_ajax_cargar_seccion() {
    if (!is_user_logged_in()) wp_die();

    $user_id = get_current_user_id();
    $seccion = sanitize_text_field($_POST['seccion'] ?? '');

    $secciones_validas = [
        'resumen-general',
        'progreso-categorias',
        'publicaciones-ia',
        'progreso-competencias',
        'medallas',
        'interacciones-sociales',
        'evolucion-temporal',
        'actividad-por-tipo',
    ];

    if (!in_array($seccion, $secciones_validas)) {
        echo "<p class='text-danger'>❌ Sección inválida</p>";
        wp_die();
    }

    require_once FUNC_PATH . 'includes/dashboard/functions.php';
    require_once FUNC_PATH . 'includes/utils/estadisticas-dashboard.php';

    error_log("📂 Includes cargados correctamente");
    
    $func = "renderizar_" . str_replace('-', '_', $seccion);
    error_log("🧪 Intentando ejecutar $func para user $user_id");

    $transient_key = "dashboard_{$seccion}_{$user_id}";
    $html = get_transient($transient_key);

    if ($html === false) {
        if (function_exists($func)) {
            $html = $func($user_id);
            set_transient($transient_key, $html, 10 * MINUTE_IN_SECONDS);
            error_log("🟢 Transient usado para $seccion ($user_id)");
        } else {
            $html = "<p class='text-danger'>❌ No existe la función $func()</p>";
            error_log("🆕 Transient NO encontrado, generando nuevo para $seccion ($user_id)");

        }
    }

    echo $html;
    wp_die();
}
